<?php
// LANGKAH 1: UNTUK PRODUKSI, SEBAIKNYA display_errors=0 dan log_errors=1
// Untuk debugging, Anda bisa sementara set display_errors=1 jika error tidak tertangkap di JSON
ini_set('display_errors', 0); // Matikan display_errors agar tidak mengganggu JSON
ini_set('log_errors', 1);    // Aktifkan logging error ke file
// error_reporting(E_ALL); // Laporkan semua error

// Fungsi untuk mengirim respons JSON dan keluar
function send_json_response($data) {
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    echo json_encode($data);
    exit;
}

// Custom error handler untuk mengubah error menjadi Exception
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        // Error reporting level ini tidak termasuk error ini
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Shutdown function untuk menangkap fatal errors
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR, E_PARSE])) {
        // Terjadi fatal error, coba kirim respons JSON
        error_log("proses_pesanan.php SHUTDOWN ERROR: Type: {$error['type']}, Message: {$error['message']}, File: {$error['file']}, Line: {$error['line']}");
        
        if (!headers_sent()) {
             send_json_response([
                'success' => false,
                'message' => 'KRITIS SERVER ERROR: Terjadi kesalahan fatal di server. Silakan cek log server.',
                'error_detail' => [ // Jangan tampilkan detail error ke klien di produksi
                    'type' => $error['type'],
                    'message' => $error['message'],
                    'file' => basename($error['file']), 
                    'line' => $error['line']
                ]
            ]);
        } else {
            error_log("proses_pesanan.php SHUTDOWN ERROR: Headers already sent, cannot send JSON response for fatal error.");
        }
    }
});


error_log("proses_pesanan.php: Skrip dimulai.");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
    error_log("proses_pesanan.php: Session dimulai.");
} else {
    error_log("proses_pesanan.php: Session sudah aktif.");
}

$response = ['success' => false, 'message' => 'Terjadi kesalahan umum di awal proses.']; // Default response

try {
    if (!@include_once "../db/koneksi.php") {
        throw new ErrorException('KRITIS: File koneksi.php tidak ditemukan atau gagal di-include. Periksa path: ../db/koneksi.php');
    }
    error_log("proses_pesanan.php: File koneksi.php berhasil di-include.");

    if (!isset($conn) || !$conn) {
        throw new ErrorException('KRITIS: Variabel koneksi database ($conn) tidak terdefinisi atau tidak valid setelah include koneksi.php.');
    }
    error_log("proses_pesanan.php: Variabel \$conn terdefinisi.");

    if ($conn instanceof mysqli && $conn->connect_error) {
        throw new ErrorException('KRITIS: Gagal terhubung ke database: ' . htmlspecialchars($conn->connect_error));
    }
    error_log("proses_pesanan.php: Koneksi database berhasil.");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response['message'] = 'Metode request tidak valid. Hanya POST yang diizinkan.';
        send_json_response($response); 
    }
    error_log("proses_pesanan.php: Metode request adalah POST.");

    $input_raw = file_get_contents('php://input');
    error_log("proses_pesanan.php: Raw input: " . $input_raw);
    $input = json_decode($input_raw, true);

    if ($input === null && json_last_error() !== JSON_ERROR_NONE) {
        throw new ErrorException('Data input JSON tidak valid: ' . json_last_error_msg());
    }
    error_log("proses_pesanan.php: Input JSON berhasil di-decode.");

    if (!$input || !isset($input['items']) || !isset($input['total_keseluruhan']) || !isset($input['username_pembeli'])) {
        throw new ErrorException('Data input tidak lengkap. Pastikan items, total_keseluruhan, dan username_pembeli terkirim.');
    }
    error_log("proses_pesanan.php: Validasi data input dasar berhasil.");

    $items_pesanan = $input['items'];
    if (empty($items_pesanan)) {
        $response['message'] = 'Keranjang kosong. Tidak ada item untuk diproses.';
        send_json_response($response); 
    }

    $total_harga_pesanan = floatval($input['total_keseluruhan']);
    $username = mysqli_real_escape_string($conn, $input['username_pembeli']);
    $nama_penerima = isset($input['nama_penerima']) ? mysqli_real_escape_string($conn, $input['nama_penerima']) : 'Pelanggan';
    $alamat_jalan = isset($input['alamat_penerima']) ? mysqli_real_escape_string($conn, $input['alamat_penerima']) : NULL;
    $kota = isset($input['kota_penerima']) ? mysqli_real_escape_string($conn, $input['kota_penerima']) : NULL;
    $kode_pos = isset($input['kodepos_penerima']) ? mysqli_real_escape_string($conn, $input['kodepos_penerima']) : NULL;
    $tipe_pesanan = isset($input['tipe_pesanan']) ? mysqli_real_escape_string($conn, $input['tipe_pesanan']) : 'Umum';
    $status_pesanan_awal = 'Menunggu';

    mysqli_autocommit($conn, FALSE);
    error_log("proses_pesanan.php: Transaksi dimulai (autocommit=OFF).");

    error_log("proses_pesanan.php: Memulai validasi stok.");
    foreach ($items_pesanan as $item_idx => $item) {
        $id_obat_item_cek = isset($item['id_obat']) ? intval($item['id_obat']) : 0;
        $jumlah_item_cek = isset($item['jumlah']) ? intval($item['jumlah']) : 0;
        $nama_produk_cek = isset($item['nama_produk']) ? htmlspecialchars($item['nama_produk']) : 'ITEM_TANPA_NAMA';
        error_log("proses_pesanan.php: Validasi item ke-" . ($item_idx+1) . ": ID {$id_obat_item_cek}, Nama '{$nama_produk_cek}', Jumlah {$jumlah_item_cek}");

        if ($id_obat_item_cek <= 0) {
            throw new Exception("Item ke-".($item_idx+1)." ('{$nama_produk_cek}') memiliki ID Obat tidak valid ('{$id_obat_item_cek}'). Pesanan dibatalkan.");
        }
        if ($jumlah_item_cek <= 0) {
            throw new Exception("Item ke-".($item_idx+1)." ('{$nama_produk_cek}') memiliki jumlah tidak valid ('{$jumlah_item_cek}'). Pesanan dibatalkan.");
        }

        $stok_cek_sql = "SELECT stok FROM obat WHERE id_obat = ?";
        $stmt_stok_cek = mysqli_prepare($conn, $stok_cek_sql);
        if (!$stmt_stok_cek) throw new Exception("Gagal prepare cek stok (item: {$nama_produk_cek}): " . mysqli_error($conn));
        
        mysqli_stmt_bind_param($stmt_stok_cek, "i", $id_obat_item_cek);
        mysqli_stmt_execute($stmt_stok_cek);
        $result_stok_cek = mysqli_stmt_get_result($stmt_stok_cek);
        $row_stok = mysqli_fetch_assoc($result_stok_cek);
        mysqli_stmt_close($stmt_stok_cek);

        if ($row_stok) {
            if ($row_stok['stok'] < $jumlah_item_cek) {
                throw new Exception("Stok untuk '{$nama_produk_cek}' tidak cukup (tersisa: {$row_stok['stok']}, diminta: {$jumlah_item_cek}).");
            }
        } else {
            throw new Exception("Produk '{$nama_produk_cek}' (ID: {$id_obat_item_cek}) tidak ditemukan di katalog.");
        }
        error_log("proses_pesanan.php: Validasi stok untuk item '{$nama_produk_cek}' berhasil.");
    }
    error_log("proses_pesanan.php: Semua item berhasil divalidasi stoknya.");

    $total_kuantitas_semua_item = 0;
    foreach ($items_pesanan as $item) {
        $total_kuantitas_semua_item += intval($item['jumlah']);
    }

    error_log("proses_pesanan.php: Memulai insert ke tabel pesanan.");
    $sql_pesanan = "INSERT INTO pesanan (username, status_pesanan, tipe_pesanan, jumlah, nama_penerima, alamat_jalan, kota, kode_pos, id_obat, total_harga, tanggal_pesanan)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NULL, ?, NOW())"; // 9 placeholders '?'
    $stmt_pesanan = mysqli_prepare($conn, $sql_pesanan);
    if (!$stmt_pesanan) throw new Exception("Gagal prepare statement pesanan: " . mysqli_error($conn));
    
    // PERBAIKAN: String tipe harus "sssissssd" (9 karakter) untuk 9 variabel
    mysqli_stmt_bind_param($stmt_pesanan, "sssissssd", 
        $username, $status_pesanan_awal, $tipe_pesanan, $total_kuantitas_semua_item,
        $nama_penerima, $alamat_jalan, $kota, $kode_pos, $total_harga_pesanan
    );
    if (!mysqli_stmt_execute($stmt_pesanan)) throw new Exception("Gagal execute statement pesanan: " . mysqli_stmt_error($stmt_pesanan));
    
    $id_pesanan_baru = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt_pesanan);
    if ($id_pesanan_baru == 0) throw new Exception("Gagal mendapatkan ID pesanan baru.");
    error_log("proses_pesanan.php: Insert ke tabel pesanan berhasil. ID Pesanan Baru: " . $id_pesanan_baru);

    error_log("proses_pesanan.php: Memulai insert ke tabel detail_pesanan dan update stok.");
    $sql_detail = "INSERT INTO detail_pesanan (id_pesanan, id_obat, nama_produk, harga, jumlah, subtotal) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_detail = mysqli_prepare($conn, $sql_detail);
    if (!$stmt_detail) throw new Exception("Gagal prepare statement detail pesanan: " . mysqli_error($conn));

    $sql_update_stok = "UPDATE obat SET stok = stok - ? WHERE id_obat = ? AND stok >= ?";
    $stmt_update_stok = mysqli_prepare($conn, $sql_update_stok);
    if (!$stmt_update_stok) throw new Exception("Gagal prepare statement update stok: " . mysqli_error($conn));

    foreach ($items_pesanan as $item_idx => $item) {
        $id_obat_item_val = (isset($item['id_obat']) && intval($item['id_obat']) > 0) ? intval($item['id_obat']) : null;
        $nama_produk_item = $item['nama_produk'];
        $harga_item = floatval($item['harga']);
        $jumlah_item = intval($item['jumlah']);
        $subtotal_item = floatval($item['subtotal']);
        error_log("proses_pesanan.php: Proses detail item ke-" . ($item_idx+1) . " ('{$nama_produk_item}'): ID Obat Val {$id_obat_item_val}");

        mysqli_stmt_bind_param($stmt_detail, "iisdid",
            $id_pesanan_baru, $id_obat_item_val, $nama_produk_item,
            $harga_item, $jumlah_item, $subtotal_item
        );
        if (!mysqli_stmt_execute($stmt_detail)) throw new Exception("Gagal execute detail item ('" . htmlspecialchars($nama_produk_item) . "'): " . mysqli_stmt_error($stmt_detail));

        if ($id_obat_item_val !== null) {
            mysqli_stmt_bind_param($stmt_update_stok, "iii", $jumlah_item, $id_obat_item_val, $jumlah_item);
            if (!mysqli_stmt_execute($stmt_update_stok)) throw new Exception("Gagal update stok ('" . htmlspecialchars($nama_produk_item) . "'): " . mysqli_stmt_error($stmt_update_stok));
            if (mysqli_stmt_affected_rows($stmt_update_stok) == 0) throw new Exception("Update stok gagal (stok berubah/obat ID {$id_obat_item_val} tidak ditemukan) untuk '" . htmlspecialchars($nama_produk_item) . "'.");
        }
        error_log("proses_pesanan.php: Detail item '{$nama_produk_item}' dan stok berhasil diproses.");
    }
    mysqli_stmt_close($stmt_detail);
    mysqli_stmt_close($stmt_update_stok);
    error_log("proses_pesanan.php: Semua detail item dan update stok selesai.");

    error_log("proses_pesanan.php: Memulai insert ke tabel pembayaran (opsional).");
    $catatan_pembayaran_awal = 'Menunggu konfirmasi pembayaran.';
    $sql_pembayaran_init = "INSERT INTO pembayaran (id_pesanan, jumlah, catatan, tanggal_bayar) VALUES (?, ?, ?, NOW())";
    $stmt_pembayaran = mysqli_prepare($conn, $sql_pembayaran_init);
    if ($stmt_pembayaran) {
        mysqli_stmt_bind_param($stmt_pembayaran, "ids", $id_pesanan_baru, $total_harga_pesanan, $catatan_pembayaran_awal);
        if (!mysqli_stmt_execute($stmt_pembayaran)) {
            error_log("Peringatan Proses Pesanan: Gagal simpan pembayaran awal (ID: {$id_pesanan_baru}): " . mysqli_stmt_error($stmt_pembayaran));
        }
        mysqli_stmt_close($stmt_pembayaran);
    } else {
        error_log("Peringatan Proses Pesanan: Gagal prepare statement pembayaran awal: " . mysqli_error($conn));
    }
    error_log("proses_pesanan.php: Proses insert pembayaran selesai.");

    error_log("proses_pesanan.php: Memulai pengosongan keranjang.");
    $sql_clear_cart = "DELETE FROM keranjang";
    if (!mysqli_query($conn, $sql_clear_cart)) {
        error_log("Peringatan Proses Pesanan: Gagal kosongkan keranjang (ID Pesanan: {$id_pesanan_baru}): " . mysqli_error($conn));
    }
    error_log("proses_pesanan.php: Pengosongan keranjang selesai.");

    mysqli_commit($conn);
    error_log("proses_pesanan.php: Transaksi berhasil di-commit. ID Pesanan: " . $id_pesanan_baru);
    $response['success'] = true;
    $response['message'] = "Pesanan berhasil diproses dengan ID: {$id_pesanan_baru}.";
    $response['id_pesanan'] = $id_pesanan_baru;
    $_SESSION['pesan_notif'] = $response['message'];
    $_SESSION['pesan_notif_tipe'] = 'notif-sukses';

} catch (ErrorException $e) { 
    if (isset($conn) && $conn) mysqli_rollback($conn); // Pastikan rollback jika $conn ada
    $response['message'] = "GAGAL PHP ERROR: " . $e->getMessage();
    error_log("proses_pesanan.php PHP ErrorException (User: {$username}, IP: {$_SERVER['REMOTE_ADDR']}): {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}\nInput Data: " . json_encode($input) . "\nTrace: " . $e->getTraceAsString());
    if(isset($_SESSION)){ // Cek jika session bisa diakses
        $_SESSION['pesan_notif'] = $response['message'];
        $_SESSION['pesan_notif_tipe'] = 'notif-gagal';
    }

} catch (Exception $e) { 
    if (isset($conn) && $conn) mysqli_rollback($conn); // Pastikan rollback jika $conn ada
    $response['message'] = "GAGAL EXCEPTION: " . $e->getMessage();
    error_log("proses_pesanan.php KRITIS EXCEPTION (User: {$username}, IP: {$_SERVER['REMOTE_ADDR']}): " . $e->getMessage() . "\nInput Data: " . json_encode($input) . "\nTrace: " . $e->getTraceAsString());
    if(isset($_SESSION)){ // Cek jika session bisa diakses
        $_SESSION['pesan_notif'] = $response['message'];
        $_SESSION['pesan_notif_tipe'] = 'notif-gagal';
    }
} finally {
    if (isset($conn) && $conn) { 
        mysqli_autocommit($conn, TRUE);
        error_log("proses_pesanan.php: Autocommit dikembalikan ke TRUE.");
    }
}

if (isset($conn) && $conn) {
    mysqli_close($conn);
    error_log("proses_pesanan.php: Koneksi database ditutup.");
} else {
    error_log("proses_pesanan.php: Variabel \$conn tidak valid saat akan menutup koneksi.");
}

if (!isset($response) || !is_array($response)) {
    $response = ['success' => false, 'message' => 'KRITIS: Variabel respons tidak terdefinisi dengan benar sebelum output.'];
    error_log("proses_pesanan.php FATAL: Variabel \$response tidak terdefinisi sebelum echo json_encode.");
}

error_log("proses_pesanan.php: Mengirim respons JSON: " . json_encode($response));
send_json_response($response);
