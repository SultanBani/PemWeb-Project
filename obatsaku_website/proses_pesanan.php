<?php 
ini_set('display_errors', 0);
ini_set('log_errors', 1);

function send_json_response($data) {
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    echo json_encode($data);
    exit;
}

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR, E_PARSE])) {
        error_log("SHUTDOWN ERROR: {$error['message']} in {$error['file']} on line {$error['line']}");
        if (!headers_sent()) {
            send_json_response([
                'success' => false,
                'message' => 'Kritikal: Terjadi kesalahan fatal di server.'
            ]);
        }
    }
});

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$conn = null;
$input = [];

try {
    if (!@include_once "db/koneksi.php") {
        throw new Exception('File koneksi tidak ditemukan.');
    }

    if (!isset($conn) || !$conn) {
        throw new Exception('Koneksi database tidak valid.');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metode request harus POST.');
    }

    $input_raw = file_get_contents('php://input');
    $input = json_decode($input_raw, true);

    if ($input === null || !isset($input['items']) || !isset($input['total_keseluruhan']) || !isset($input['username_pembeli'])) {
        throw new Exception('Input tidak lengkap.');
    }

    $items_pesanan = $input['items'];
    $total_harga_pesanan = floatval($input['total_keseluruhan']);
    $username = $conn->real_escape_string($input['username_pembeli']);
    $nama_penerima = isset($input['nama_penerima']) ? $conn->real_escape_string($input['nama_penerima']) : 'Pelanggan';

    $status_pesanan_awal = 'Menunggu';
    $total_kuantitas = array_sum(array_column($items_pesanan, 'jumlah'));

    $ada_resep = false;

    foreach ($items_pesanan as $item) {
        $id_obat = intval($item['id_obat']);
        $stmt_cek_jenis = $conn->prepare("SELECT jenis_obat FROM obat WHERE id_obat = ?");
        $stmt_cek_jenis->bind_param("i", $id_obat);
        $stmt_cek_jenis->execute();
        $result_jenis = $stmt_cek_jenis->get_result();
        $jenis = $result_jenis->fetch_assoc();
        $stmt_cek_jenis->close();

        if ($jenis && strtolower($jenis['jenis_obat']) === 'resep') {
            $ada_resep = true;
        }
    }

    $tipe_pesanan = $ada_resep ? 'Resep' : 'Umum';

    $conn->autocommit(false);

    foreach ($items_pesanan as $item) {
        $id_obat = intval($item['id_obat']);
        $jumlah = intval($item['jumlah']);

        $stmt = $conn->prepare("SELECT stok FROM obat WHERE id_obat = ? FOR UPDATE");
        $stmt->bind_param("i", $id_obat);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if (!$row) {
            throw new Exception("Obat ID {$id_obat} tidak ditemukan.");
        }

        if ($row['stok'] < $jumlah) {
            throw new Exception("Stok obat ID {$id_obat} tidak mencukupi.");
        }
    }

    $stmt_pesanan = $conn->prepare("INSERT INTO pesanan (username, status_pesanan, tipe_pesanan, jumlah, nama_penerima, alamat_jalan, kota, kode_pos, id_obat, total_harga, tanggal_pesanan) VALUES (?, ?, ?, ?, ?, NULL, NULL, NULL, NULL, ?, NOW())");
    $stmt_pesanan->bind_param("sssisd", $username, $status_pesanan_awal, $tipe_pesanan, $total_kuantitas, $nama_penerima, $total_harga_pesanan);
    $stmt_pesanan->execute();
    $id_pesanan = $conn->insert_id;
    $stmt_pesanan->close();

    if ($id_pesanan == 0) {
        throw new Exception("Gagal menyimpan data pesanan utama.");
    }

    $stmt_detail = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_obat, nama_produk, harga, jumlah, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_update_stok = $conn->prepare("UPDATE obat SET stok = stok - ? WHERE id_obat = ?");

    foreach ($items_pesanan as $item) {
        $stmt_detail->bind_param("iisdid", $id_pesanan, $item['id_obat'], $item['nama_produk'], $item['harga'], $item['jumlah'], $item['subtotal']);
        $stmt_detail->execute();

        $stmt_update_stok->bind_param("ii", $item['jumlah'], $item['id_obat']);
        $stmt_update_stok->execute();
    }

    $stmt_detail->close();
    $stmt_update_stok->close();

    $conn->commit();

    $conn->query("DELETE FROM keranjang");

    $pesan_wa = "Pesanan berhasil dibuat dengan ID: {$id_pesanan}. Total: Rp " . number_format($total_harga_pesanan, 0, ',', '.');
    if ($tipe_pesanan === 'Resep') {
        $pesan_wa .= " (Harus dilengkapi resep dokter. Harap konfirmasi via WhatsApp).";
    }

    $response = [
        'success' => true,
        'message' => $pesan_wa,
        'id_pesanan' => $id_pesanan,
        'tipe_pesanan' => $tipe_pesanan
    ];

    $_SESSION['pesan_notif'] = $response['message'];
    $_SESSION['pesan_notif_tipe'] = 'notif-sukses';

} catch (Exception $e) {
    if ($conn && $conn->ping()) {
        $conn->rollback();
    }
    $response = [
        'success' => false,
        'message' => 'Gagal memproses pesanan: ' . $e->getMessage()
    ];
    $_SESSION['pesan_notif'] = $response['message'];
    $_SESSION['pesan_notif_tipe'] = 'notif-gagal';
} finally {
    if ($conn && $conn->ping()) {
        $conn->autocommit(true);
        $conn->close();
    }
}

send_json_response($response);
