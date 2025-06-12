<?php include 'header.php';
include "db/koneksi.php"; // Pastikan path ini benar
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pastikan $conn ada dan valid setelah include
if (!isset($conn) || !$conn) {
    // Ini adalah error kritis, mungkin redirect ke halaman error atau tampilkan pesan
    die("KRITIS: Koneksi database tidak berhasil dimuat. Periksa file koneksi.php.");
}

$username_pengguna = isset($_SESSION['username']) ? mysqli_real_escape_string($conn, $_SESSION['username']) : 'guest';

// Logika untuk menambah/mengurangi jumlah item di keranjang (ACTION HANDLER)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_cart']) && isset($_POST['id_keranjang_item'])) {
    $id_keranjang_item = intval($_POST['id_keranjang_item']);
    $action_cart = $_POST['action_cart'];
    $id_obat_untuk_stok_check = null;
    $jumlah_saat_ini = 0;

    // 1. Dapatkan id_obat dan jumlah saat ini dari keranjang berdasarkan id_keranjang_item
    $item_query_sql = "SELECT id_obat, jumlah FROM keranjang WHERE id = ?";
    $stmt_item = mysqli_prepare($conn, $item_query_sql);

    if ($stmt_item) {
        mysqli_stmt_bind_param($stmt_item, "i", $id_keranjang_item);
        mysqli_stmt_execute($stmt_item);
        $result_item = mysqli_stmt_get_result($stmt_item);

        if ($item_data = mysqli_fetch_assoc($result_item)) {
            if (isset($item_data['id_obat']) && !empty($item_data['id_obat']) && is_numeric($item_data['id_obat']) && intval($item_data['id_obat']) > 0) {
                $id_obat_untuk_stok_check = intval($item_data['id_obat']);
                $jumlah_saat_ini = intval($item_data['jumlah']);
            } else {
                $_SESSION['pesan_notif'] = "Data item keranjang tidak valid (ID Obat internal error).";
                $_SESSION['pesan_notif_tipe'] = 'notif-gagal';
                error_log("pembayaran.php ERROR: id_obat tidak valid dari keranjang.id_keranjang_item: $id_keranjang_item, id_obat terbaca: " . ($item_data['id_obat'] ?? 'TIDAK ADA'));
            }
        } else {
            $_SESSION['pesan_notif'] = "Item keranjang tidak ditemukan untuk diupdate.";
            $_SESSION['pesan_notif_tipe'] = 'notif-gagal';
        }
        mysqli_stmt_close($stmt_item);
    } else {
        $_SESSION['pesan_notif'] = "Terjadi kesalahan internal saat mengambil data item (prepare failed).";
        $_SESSION['pesan_notif_tipe'] = 'notif-gagal';
        error_log("pembayaran.php ERROR: Gagal mysqli_prepare untuk SELECT id_obat FROM keranjang: " . mysqli_error($conn));
    }

    if ($id_obat_untuk_stok_check !== null && $id_obat_untuk_stok_check > 0) {
        $stok_obat = 0;
        $stok_query_sql = "SELECT stok FROM obat WHERE id_obat = ?";
        $stmt_stok = mysqli_prepare($conn, $stok_query_sql);

        if($stmt_stok){
            mysqli_stmt_bind_param($stmt_stok, "i", $id_obat_untuk_stok_check);
            mysqli_stmt_execute($stmt_stok);
            $result_stok = mysqli_stmt_get_result($stmt_stok);

            if ($stok_data = mysqli_fetch_assoc($result_stok)) {
                $stok_obat = intval($stok_data['stok']);
            } else {
                $_SESSION['pesan_notif'] = "Referensi obat untuk item di keranjang tidak ditemukan di katalog.";
                $_SESSION['pesan_notif_tipe'] = 'notif-gagal';
                error_log("pembayaran.php ERROR: Obat tidak ditemukan di tabel obat untuk id_obat: $id_obat_untuk_stok_check (dari keranjang.id: $id_keranjang_item)");
            }
            mysqli_stmt_close($stmt_stok);
        } else {
            $_SESSION['pesan_notif'] = "Terjadi kesalahan internal saat mengambil data stok (prepare failed).";
            $_SESSION['pesan_notif_tipe'] = 'notif-gagal';
            error_log("pembayaran.php ERROR: Gagal mysqli_prepare untuk SELECT stok FROM obat: " . mysqli_error($conn));
        }

        if (!isset($_SESSION['pesan_notif']) || $_SESSION['pesan_notif_tipe'] !== 'notif-gagal') { // Hanya proses jika tidak ada error sebelumnya
            if ($action_cart === 'plus') {
                if (($jumlah_saat_ini + 1) <= $stok_obat) {
                    $update_sql = "UPDATE keranjang SET jumlah = jumlah + 1 WHERE id = ?";
                    $stmt_update = mysqli_prepare($conn, $update_sql);
                    mysqli_stmt_bind_param($stmt_update, "i", $id_keranjang_item);
                    mysqli_stmt_execute($stmt_update);
                    mysqli_stmt_close($stmt_update);
                } else {
                    $_SESSION['pesan_notif'] = "Tidak bisa menambah jumlah, stok tidak mencukupi (tersisa: $stok_obat).";
                    $_SESSION['pesan_notif_tipe'] = 'notif-gagal';
                }
            } elseif ($action_cart === 'minus') {
                if ($jumlah_saat_ini > 1) {
                    $update_sql = "UPDATE keranjang SET jumlah = jumlah - 1 WHERE id = ?";
                    $stmt_update = mysqli_prepare($conn, $update_sql);
                    mysqli_stmt_bind_param($stmt_update, "i", $id_keranjang_item);
                    mysqli_stmt_execute($stmt_update);
                    mysqli_stmt_close($stmt_update);
                } else {
                    $delete_sql = "DELETE FROM keranjang WHERE id = ?";
                    $stmt_delete = mysqli_prepare($conn, $delete_sql);
                    mysqli_stmt_bind_param($stmt_delete, "i", $id_keranjang_item);
                    mysqli_stmt_execute($stmt_delete);
                    mysqli_stmt_close($stmt_delete);
                }
            }
        }
    }

    // header("Location: pembayaran.php");
    // exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Obatsaku</title>
    <link rel="stylesheet" href="../assets/css/pembayaran.css"> <!-- Pastikan path CSS benar -->
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f7f6; color: #333; }
        .container { max-width: 800px; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        .header-pembayaran h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .notif-pesan { padding: 12px 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; font-size: 0.95em; border: 1px solid transparent; }
        .notif-sukses { background-color: #d1e7dd; color: #0f5132; border-color: #badbcc; }
        .notif-gagal { background-color: #f8d7da; color: #842029; border-color: #f5c2c7; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #dee2e6; padding: 10px; text-align: left; vertical-align: middle; }
        th { background-color: #e9ecef; color: #495057; font-weight: 600; }
        td.action-buttons form { display: inline-block; margin: 0 2px; }
        .total { font-size: 1.25em; font-weight: light; text-align: right; margin-top: 10px; padding-top:10px; border-top: 1px solid #eee; }
        .btn-ikon { padding: 3px 8px; margin: 0 5px; cursor: pointer; font-weight: bold; border: 1px solid #ccc; background-color: #f8f9fa; border-radius: 4px; }
        .btn-ikon:hover { background-color: #e2e6ea; }
        .btn-wa { padding: 12px 18px; background-color: #25D366; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; text-decoration: none; display: inline-block; }
        .btn-wa:hover { background-color: #1EBE57; }
        .btn-wa:disabled { background-color: #90d8a4; cursor: not-allowed; }
        .back-link { display: inline-block; margin-top: 20px; color: #007bff; text-decoration: none; font-size: 0.9em; }
        .back-link:hover { text-decoration: underline; }
        .empty-message { text-align: center; font-size: 1.1em; color: #6c757d; margin: 30px 0; padding: 20px; background-color: #f8f9fa; border-radius: 5px;}
        #pembayaran-actions { text-align: right; margin-top: 25px; }
        .navigasi-beranda { display: flex; justify-content: space-between; align-items: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #eee;}

        @media print {
            body { background-color: #fff; padding: 0; margin:0; }
            .container { max-width: 100%; margin: 0; box-shadow: none; border-radius: 0; padding: 10px; }
            body * { visibility: hidden; }
            #nota-area, #nota-area * { visibility: visible; }
            #nota-area { position: absolute; left: 0; top: 0; width: 100%; }
            #pembayaran-actions, .back-link, .notif-pesan, .header-pembayaran h2, .action-buttons form button, .navigasi-beranda {
                display: none !important;
            }
            table { font-size: 10pt; width: 100%; margin: 0; }
            th, td { padding: 5px; border: 1px solid #333; }
            .total { font-size: 11pt; }
            #nota-area .judul-nota-print { display: block !important; visibility: visible; text-align: center; font-size: 14pt; margin-bottom: 15px; font-weight: bold;}
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header-pembayaran">
      <h2>Halaman Pembayaran</h2>
    </div>

    <?php if (isset($_SESSION['pesan_notif'])): ?>
        <div class="notif-pesan <?= htmlspecialchars($_SESSION['pesan_notif_tipe'] ?? 'notif-gagal'); ?>">
            <?= htmlspecialchars($_SESSION['pesan_notif']); ?>
        </div>
        <?php
        unset($_SESSION['pesan_notif']);
        unset($_SESSION['pesan_notif_tipe']);
        ?>
    <?php endif; ?>

    <div id="nota-area">
        <div class="judul-nota-print" style="display:none;">NOTA PEMBELIAN OBATSAKU</div>
        <?php
        $sql_keranjang = "SELECT id, id_obat, nama_produk, harga, jumlah FROM keranjang";
        // Jika keranjang per pengguna: $sql_keranjang .= " WHERE username = '$username_pengguna'";
        $result_keranjang = mysqli_query($conn, $sql_keranjang);
        $total_pembayaran = 0;
        $items_for_js = [];

        if ($result_keranjang && mysqli_num_rows($result_keranjang) > 0):
        ?>
            <table>
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                while ($item = mysqli_fetch_assoc($result_keranjang)):
                    $harga_item_numeric = floatval($item['harga']);
                    $jumlah_item_numeric = intval($item['jumlah']);
                    $subtotal_item = $harga_item_numeric * $jumlah_item_numeric;
                    $total_pembayaran += $subtotal_item;

                    $items_for_js[] = [
                        'id_keranjang' => intval($item['id']),
                        'id_obat' => intval($item['id_obat']),
                        'nama_produk' => $item['nama_produk'],
                        'harga' => $harga_item_numeric,
                        'jumlah' => $jumlah_item_numeric,
                        'subtotal' => $subtotal_item
                    ];
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                        <td>Rp<?= number_format($harga_item_numeric, 0, ',', '.') ?></td>
                        <td class="action-buttons">
                            <form method="POST" action="pembayaran.php" style="display:inline;">
                                <input type="hidden" name="id_keranjang_item" value="<?= $item['id'] ?>">
                                <input type="hidden" name="action_cart" value="minus">
                                <button type="submit" class="btn-ikon" title="Kurangi">-</button>
                            </form>
                            <?= $jumlah_item_numeric ?>
                            <form method="POST" action="pembayaran.php" style="display:inline;">
                                <input type="hidden" name="id_keranjang_item" value="<?= $item['id'] ?>">
                                <input type="hidden" name="action_cart" value="plus">
                                <button type="submit" class="btn-ikon" title="Tambah">+</button>
                            </form>
                        </td>
                        <td>Rp<?= number_format($subtotal_item, 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <p class="total">Total Pembayaran: <strong id="total-harga-display">Rp<?= number_format($total_pembayaran, 0, ',', '.') ?></p>

            <div id="pembayaran-actions">
                <button class="btn-wa" id="btnProsesPesanan" onclick="prosesDanCetak()">Selesaikan Pesanan, Cetak Nota & Kirim WA</button>
            </div>

        <?php else: ?>
            <p class="empty-message">Keranjang belanja Anda kosong. Silakan tambahkan produk dari katalog.</p>
        <?php endif; ?>
    </div> <!-- End #nota-area -->

    <div class="navigasi-beranda">
        <a href="kelola_obat.php" class="back-link">&larr; Lanjut Belanja</a>
        <a href="index.php" class="back-link">Kembali ke Beranda &rarr;</a>
    </div>
</div>

<script>
async function prosesDanCetak() {
    const itemsToProcess = <?= json_encode($items_for_js); ?>;
    const totalPembayaran = <?= $total_pembayaran; ?>;
    const usernamePembeli = '<?= $username_pengguna; ?>'; // Username diambil dari PHP session

    const btnProses = document.getElementById('btnProsesPesanan');

    if (itemsToProcess.length === 0) {
        alert("Keranjang kosong. Tidak ada yang bisa diproses.");
        return;
    }

    if (!confirm("Anda yakin ingin menyelesaikan pesanan ini? Stok akan dipotong dan keranjang dikosongkan setelah proses berhasil.")) {
        return;
    }

    btnProses.disabled = true;
    btnProses.innerText = 'Memproses...';

    try {
        const response = await fetch('proses_pesanan.php', { // Pastikan path ini benar
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                items: itemsToProcess,
                total_keseluruhan: totalPembayaran,
                username_pembeli: usernamePembeli,
                // Anda bisa tambahkan data lain jika ada formnya di halaman pembayaran
                // Contoh:
                // nama_penerima: document.getElementById('nama_penerima_input') ? document.getElementById('nama_penerima_input').value : "Pelanggan",
                // alamat_penerima: document.getElementById('alamat_input') ? document.getElementById('alamat_input').value : "",
                tipe_pesanan: "Umum" // Default atau ambil dari input jika ada
            })
        });

        // Selalu coba parse JSON, bahkan jika status tidak ok, untuk melihat pesan error dari server
        let result;
        try {
            result = await response.json();
        } catch (e) {
            // Jika gagal parse JSON, coba baca sebagai teks untuk melihat output error HTML
            const errorText = await response.text();
            console.error('Fetch Error: Gagal parse JSON. Respons Server:', errorText);
            alert('Terjadi kesalahan format respons dari server. Cek konsol (F12) untuk detail.');
            btnProses.disabled = false;
            btnProses.innerText = 'Selesaikan Pesanan, Cetak Nota & Kirim WA';
            return;
        }


        if (response.ok && result.success) {
            alert(result.message || "Pesanan berhasil diproses!");

            const nomorWA = "6285367336284"; // GANTI DENGAN NOMOR WA TUJUAN ANDA
            let pesanWA = `*Pesanan Baru Diterima (ID: ${result.id_pesanan})*\n\n`;
            pesanWA += `Halo Admin Obatsaku,\nAda pesanan baru atas nama: ${usernamePembeli}\nDetail:\n`;
            itemsToProcess.forEach(item => {
                pesanWA += `- ${item.nama_produk} (${item.jumlah}x) = Rp${Number(item.subtotal).toLocaleString('id-ID')}\n`;
            });
            const totalFormatted = `Rp${Number(totalPembayaran).toLocaleString('id-ID')}`;
            pesanWA += `\n*Total Pembayaran:* ${totalFormatted}\n\n`;
            pesanWA += `Silakan segera proses pesanan ini. Terima kasih.\n`;

            const waUrl = `https://wa.me/${nomorWA}?text=${encodeURIComponent(pesanWA)}`;
            window.open(waUrl, '_blank');

            // Tampilkan judul nota sebelum print
            const judulNota = document.querySelector('#nota-area .judul-nota-print');
            if(judulNota) judulNota.style.display = 'block';
            
            window.print();
            
            if(judulNota) judulNota.style.display = 'none'; // Sembunyikan lagi setelah print

            // Reload halaman untuk update tampilan (keranjang kosong, notif dari session)
            window.location.href = 'pembayaran.php';

        } else {
            // Jika response.ok false ATAU result.success false
            console.error('Server Response Error:', result);
            alert('Gagal memproses pesanan: ' + (result.message || `Server merespons dengan status ${response.status}. Cek konsol (F12) untuk detail.`));
            btnProses.disabled = false;
            btnProses.innerText = 'Selesaikan Pesanan, Cetak Nota & Kirim WA';
        }

    } catch (error) { // Error pada fetch itu sendiri (misal, network error)
        console.error('Fetch Error (catch):', error);
        alert('Terjadi kesalahan saat menghubungi server. Pastikan server berjalan dan koneksi internet Anda stabil. Cek konsol (F12).');
        btnProses.disabled = false;
        btnProses.innerText = 'Selesaikan Pesanan, Cetak Nota & Kirim WA';
    }
}
</script>
<script src="assets/header.js"></script>
</body>
</html>
<?php 
include 'footer.php'; 
?>
