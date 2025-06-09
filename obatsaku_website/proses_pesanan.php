<?php 
// LANGKAH 1: PENGATURAN ERROR HANDLING & LINGKUNGAN
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Fungsi terpusat untuk mengirim respons JSON dan menghentikan skrip.
function send_json_response($data) {
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    echo json_encode($data);
    exit;
}

// Custom error handler untuk mengubah error PHP (Warning, Notice) menjadi Exception.
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Shutdown function untuk menangkap FATAL ERROR yang tidak bisa ditangkap oleh try-catch.
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR, E_PARSE])) {
        error_log("proses_pesanan.php SHUTDOWN FATAL ERROR: Type: {$error['type']}, Message: {$error['message']}, File: {$error['file']}, Line: {$error['line']}");
        if (!headers_sent()) {
            send_json_response([
                'success' => false,
                'message' => 'KRITIS SERVER ERROR: Terjadi kesalahan fatal di server. Silakan cek log server.'
            ]);
        }
    }
});

// LANGKAH 2: INISIALISASI PROSES
error_log("proses_pesanan.php: Skrip dimulai.");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$conn = null;
$input = [];

try {
    // LANGKAH 3: KONEKSI & VALIDASI INPUT DASAR
    if (!@include_once "db/koneksi.php") {
        throw new Exception('KRITIS: File koneksi.php tidak ditemukan. Periksa path.');
    }
    if (!isset($conn) || !$conn) {
        throw new Exception('KRITIS: Variabel koneksi database ($conn) tidak valid.');
    }
    if ($conn->connect_error) {
        throw new Exception('KRITIS: Gagal terhubung ke database: ' . $conn->connect_error);
    }
    error_log("proses_pesanan.php: Koneksi database berhasil.");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metode request tidak valid. Hanya POST yang diizinkan.');
    }

    $input_raw = file_get_contents('php://input');
    error_log("proses_pesanan.php: Raw input: " . $input_raw);
    $input = json_decode($input_raw, true);

    if ($input === null && json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Data input JSON tidak valid: ' . json_last_error_msg());
    }
    if (!$input || !isset($input['items']) || empty($input['items']) || !isset($input['total_keseluruhan']) || !isset($input['username_pembeli'])) {
        throw new Exception('Data input tidak lengkap atau keranjang kosong.');
    }
    error_log("proses_pesanan.php: Validasi data input dasar berhasil.");

    // LANGKAH 4: PERSIAPAN DATA
    $items_pesanan = $input['items'];
    $total_harga_pesanan = floatval($input['total_keseluruhan']);
    $username = $conn->real_escape_string($input['username_pembeli']);
    $nama_penerima = isset($input['nama_penerima']) ? $conn->real_escape_string($input['nama_penerima']) : 'Pelanggan';
    $tipe_pesanan = isset($input['tipe_pesanan']) ? $conn->real_escape_string($input['tipe_pesanan']) : 'Umum';
    $status_pesanan_awal = 'Menunggu';
    $total_kuantitas_semua_item = array_sum(array_column($items_pesanan, 'jumlah'));

    // LANGKAH 5: MEMULAI TRANSAKSI DATABASE
    $conn->autocommit(FALSE);
    error_log("proses_pesanan.php: Transaksi dimulai (autocommit=OFF) untuk user: {$username}.");

    // LANGKAH 5a: Validasi Stok Semua Item (Pre-flight Check)
    foreach ($items_pesanan as $item) {
        $id_obat_cek = intval($item['id_obat']);
        $jumlah_cek = intval($item['jumlah']);
        $nama_produk_cek = htmlspecialchars($item['nama_produk']);

        $stok_cek_sql = "SELECT stok FROM obat WHERE id_obat = ? FOR UPDATE";
        $stmt_stok_cek = $conn->prepare($stok_cek_sql);
        $stmt_stok_cek->bind_param("i", $id_obat_cek);
        $stmt_stok_cek->execute();
        $result_stok_cek = $stmt_stok_cek->get_result();
        $row_stok = $result_stok_cek->fetch_assoc();
        $stmt_stok_cek->close();

        if (!$row_stok) {
            throw new Exception("Produk '{$nama_produk_cek}' (ID: {$id_obat_cek}) tidak ditemukan.");
        }
        if ($row_stok['stok'] < $jumlah_cek) {
            throw new Exception("Stok '{$nama_produk_cek}' tidak cukup (tersisa: {$row_stok['stok']}, diminta: {$jumlah_cek}).");
        }
    }
    error_log("proses_pesanan.php: Semua item berhasil divalidasi stoknya.");

    // LANGKAH 6: INSERT DATA MASTER PESANAN
    $sql_pesanan = "INSERT INTO pesanan (username, status_pesanan, tipe_pesanan, jumlah, nama_penerima, alamat_jalan, kota, kode_pos, id_obat, total_harga, tanggal_pesanan)
                    VALUES (?, ?, ?, ?, ?, NULL, NULL, NULL, NULL, ?, NOW())";
    $stmt_pesanan = $conn->prepare($sql_pesanan);
    $stmt_pesanan->bind_param("sssisd",
        $username, $status_pesanan_awal, $tipe_pesanan, $total_kuantitas_semua_item,
        $nama_penerima, $total_harga_pesanan
    );
    $stmt_pesanan->execute();
    
    $id_pesanan_baru = $conn->insert_id;
    if ($id_pesanan_baru == 0) throw new Exception("Gagal membuat record pesanan utama.");
    $stmt_pesanan->close();
    error_log("proses_pesanan.php: Insert ke tabel pesanan berhasil. ID Pesanan Baru: {$id_pesanan_baru}");

    // LANGKAH 7: INSERT DETAIL PESANAN DAN UPDATE STOK
    $sql_detail = "INSERT INTO detail_pesanan (id_pesanan, id_obat, nama_produk, harga, jumlah, subtotal) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_detail = $conn->prepare($sql_detail);

    $sql_update_stok = "UPDATE obat SET stok = stok - ? WHERE id_obat = ?";
    $stmt_update_stok = $conn->prepare($sql_update_stok);

    foreach ($items_pesanan as $item) {
        $stmt_detail->bind_param("iisdid",
            $id_pesanan_baru, $item['id_obat'], $item['nama_produk'],
            $item['harga'], $item['jumlah'], $item['subtotal']
        );
        $stmt_detail->execute();

        $stmt_update_stok->bind_param("ii", $item['jumlah'], $item['id_obat']);
        $stmt_update_stok->execute();
    }
    $stmt_detail->close();
    $stmt_update_stok->close();
    error_log("proses_pesanan.php: Insert detail pesanan dan update stok selesai.");

    // =========================================================================
    // LANGKAH 8: PENGOSONGAN KERANJANG (DIKEMBALIKAN KE METODE AWAL)
    // PERINGATAN: Kode ini menghapus SELURUH isi tabel keranjang, bukan hanya
    // keranjang milik pengguna saat ini.
    // =========================================================================
    error_log("proses_pesanan.php: Memulai pengosongan SELURUH tabel keranjang.");
    $sql_clear_cart = "DELETE FROM keranjang";
    if (!$conn->query($sql_clear_cart)) {
        // Kegagalan di sini tidak fatal, cukup catat sebagai peringatan.
        error_log("Peringatan Proses Pesanan: Gagal kosongkan seluruh keranjang: " . $conn->error);
    }
    error_log("proses_pesanan.php: Pengosongan seluruh keranjang selesai.");


    // LANGKAH 9: COMMIT TRANSAKSI
    $conn->commit();
    error_log("proses_pesanan.php: Transaksi berhasil di-commit. ID Pesanan: {$id_pesanan_baru}");

    // LANGKAH 10: PERSIAPAN RESPONS SUKSES
    $response = [
        'success' => true,
        'message' => "Pesanan berhasil diproses dengan ID: {$id_pesanan_baru}",
        'id_pesanan' => $id_pesanan_baru
    ];
    $_SESSION['pesan_notif'] = $response['message'];
    $_SESSION['pesan_notif_tipe'] = 'notif-sukses';

} catch (Exception $e) { 
    if ($conn && $conn->ping()) {
        $conn->rollback();
    }
    error_log("proses_pesanan.php KRITIS EXCEPTION (User: " . ($username ?? 'unknown') . ", IP: {$_SERVER['REMOTE_ADDR']}): " . $e->getMessage() . "\nInput Data: " . json_encode($input));
    
    $response = [
        'success' => false,
        'message' => "Gagal memproses pesanan: " . $e->getMessage()
    ];
    if(isset($_SESSION)){
        $_SESSION['pesan_notif'] = $response['message'];
        $_SESSION['pesan_notif_tipe'] = 'notif-gagal';
    }
} finally {
    if ($conn && $conn->ping()) { 
        $conn->autocommit(TRUE);
        $conn->close();
        error_log("proses_pesanan.php: Autocommit ON & koneksi ditutup.");
    }
}

// LANGKAH 11: KIRIM RESPONS FINAL
error_log("proses_pesanan.php: Mengirim respons JSON: " . json_encode($response));
send_json_response($response);
