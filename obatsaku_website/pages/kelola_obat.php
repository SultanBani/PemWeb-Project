<?php include "../header.php";

include "../db/koneksi.php"; // Pastikan path ini benar
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$keyword = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    // Pastikan $conn ada dan valid sebelum digunakan
    if (isset($conn) && $conn) {
        $keyword = trim(mysqli_real_escape_string($conn, $_GET['search']));
    } else {
        // Handle kasus $conn tidak ada (misalnya, error di koneksi.php)
        // Anda bisa set notifikasi error atau redirect
        $_SESSION['notif'] = "Error: Koneksi database tidak tersedia.";
        // header("Location: some_error_page.php");
        // exit;
    }
}

// Logika untuk menambahkan item ke keranjang
// Pastikan input name di form HTML adalah 'id_obat_to_cart'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_obat_to_cart'])) {
    // Pastikan $conn ada dan valid sebelum digunakan
    if (!isset($conn) || !$conn) {
        $_SESSION['notif'] = "Error: Koneksi database tidak tersedia untuk memproses keranjang.";
        header("Location: kelola_obat.php" . ($keyword ? "?search=" . urlencode($keyword) : ""));
        exit;
    }

    $id_obat_from_form = intval($_POST['id_obat_to_cart']);

    if ($id_obat_from_form > 0) {
        // 1. Ambil detail obat lengkap (termasuk id_obat asli) dari tabel 'obat'
        $sql_get_obat = "SELECT id_obat, nama_obat, harga, stok FROM obat WHERE id_obat = ?";
        $stmt_get_obat = mysqli_prepare($conn, $sql_get_obat);

        if ($stmt_get_obat) {
            mysqli_stmt_bind_param($stmt_get_obat, "i", $id_obat_from_form);
            mysqli_stmt_execute($stmt_get_obat);
            $result_obat = mysqli_stmt_get_result($stmt_get_obat);

            if ($obat = mysqli_fetch_assoc($result_obat)) {
                if ($obat['stok'] <= 0) {
                    $_SESSION['notif'] = "Maaf, stok untuk <strong>" . htmlspecialchars($obat['nama_obat']) . "</strong> sedang habis.";
                } else {
                    $id_obat_db = intval($obat['id_obat']);
                    $nama_produk_db = $obat['nama_obat']; // Tidak perlu escape di sini jika pakai prepared statement
                    $harga_db = floatval($obat['harga']);

                    // 2. Cek apakah obat sudah ada di keranjang (berdasarkan id_obat_db)
                    $sql_cek_keranjang = "SELECT id, jumlah FROM keranjang WHERE id_obat = ?";
                    $stmt_cek_keranjang = mysqli_prepare($conn, $sql_cek_keranjang);
                    mysqli_stmt_bind_param($stmt_cek_keranjang, "i", $id_obat_db);
                    mysqli_stmt_execute($stmt_cek_keranjang);
                    $result_cek_keranjang = mysqli_stmt_get_result($stmt_cek_keranjang);

                    if ($keranjang_item = mysqli_fetch_assoc($result_cek_keranjang)) {
                        $jumlah_baru = $keranjang_item['jumlah'] + 1;
                        if ($jumlah_baru > $obat['stok']) {
                             $_SESSION['notif'] = "Jumlah <strong>" . htmlspecialchars($obat['nama_obat']) . "</strong> di keranjang tidak bisa melebihi stok (" . $obat['stok'] . ").";
                        } else {
                            $sql_update_keranjang = "UPDATE keranjang SET jumlah = ? WHERE id_obat = ?";
                            $stmt_update_keranjang = mysqli_prepare($conn, $sql_update_keranjang);
                            mysqli_stmt_bind_param($stmt_update_keranjang, "ii", $jumlah_baru, $id_obat_db);
                            mysqli_stmt_execute($stmt_update_keranjang);
                            mysqli_stmt_close($stmt_update_keranjang);
                            $_SESSION['notif'] = "Obat <strong>" . htmlspecialchars($obat['nama_obat']) . "</strong> berhasil dimasukkan ke keranjang";
                        }
                    } else {
                        $sql_insert_keranjang = "INSERT INTO keranjang (id_obat, nama_produk, harga, jumlah) VALUES (?, ?, ?, 1)";
                        $stmt_insert_keranjang = mysqli_prepare($conn, $sql_insert_keranjang);
                        mysqli_stmt_bind_param($stmt_insert_keranjang, "isd", $id_obat_db, $nama_produk_db, $harga_db);
                        if (mysqli_stmt_execute($stmt_insert_keranjang)) {
                            $_SESSION['notif'] = "<strong>" . htmlspecialchars($obat['nama_obat']) . "</strong> berhasil ditambahkan ke keranjang!";
                        } else {
                             $_SESSION['notif'] = "Gagal menambahkan ke keranjang. Error: " . mysqli_stmt_error($stmt_insert_keranjang);
                             error_log("Gagal INSERT keranjang: " . mysqli_stmt_error($stmt_insert_keranjang) . " SQL: INSERT INTO keranjang (id_obat, nama_produk, harga, jumlah) VALUES ({$id_obat_db}, '{$nama_produk_db}', {$harga_db}, 1)");
                        }
                        mysqli_stmt_close($stmt_insert_keranjang);
                    }
                    mysqli_stmt_close($stmt_cek_keranjang);
                }
            } else {
                $_SESSION['notif'] = "Obat dengan ID (" . $id_obat_from_form . ") yang dipilih tidak ditemukan.";
            }
            mysqli_stmt_close($stmt_get_obat);
        } else {
            $_SESSION['notif'] = "Terjadi kesalahan saat mengambil data obat (prepare failed).";
            error_log("Gagal prepare statement get_obat: " . mysqli_error($conn));
        }
    } else {
        $_SESSION['notif'] = "ID Obat tidak valid atau tidak dikirimkan.";
    }

    header("Location: kelola_obat.php" . ($keyword ? "?search=" . urlencode($keyword) : ""));
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Katalog Obat - Obatsaku</title>
    <link rel="stylesheet" href="../assets/css/kelola_obat.css" /> <!-- Pastikan path CSS benar -->
    <link rel="stylesheet" href="../assets/css/header.css">
    <style>
        .notif-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50; /* Warna dasar hijau */
            color: white;
            padding: 15px;
            border-radius: 5px;
            z-index: 1000;
            opacity: 1;
            transition: opacity 0.6s ease-out;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .notif-popup.error { /* Tambahkan class error jika notif adalah error */
            background-color: #f44336; /* Warna merah untuk error */
        }
        .katalog-grid .card-obat .stok { /* Style untuk info stok */
            font-size: 0.9em;
            color: #555;
            margin-bottom: 10px;
        }
        .katalog-grid .card-obat .btn-tambah:disabled { /* Style untuk tombol disabled */
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Katalog Obat</h2>

        <form method="GET" action="kelola_obat.php" class="form-search">
            <input type="text" name="search" placeholder="Cari nama obat..." value="<?= htmlspecialchars($keyword) ?>" autocomplete="off" />
            <button type="submit">Cari</button>
            <?php if ($keyword): ?>
                <a href="kelola_obat.php" class="btn-reset">Reset</a>
            <?php endif; ?>
        </form>
        <a href="pembayaran.php" style="display: block; margin: 10px 0 20px; text-align: right; font-weight: bold; color: #007bff;">Lihat Keranjang &rarr;</a>

        <?php if (isset($_SESSION['notif'])): ?>
            <?php
            // Cek apakah notifikasi mengandung kata "Gagal" atau "Error" untuk styling
            $is_error_notif = (stripos($_SESSION['notif'], 'gagal') !== false || stripos($_SESSION['notif'], 'error') !== false || stripos($_SESSION['notif'], 'habis') !== false);
            ?>
            <div class="notif-popup <?= $is_error_notif ? 'error' : '' ?>" id="notif-popup">
                <?= $_SESSION['notif'] ?>
            </div>
            <?php unset($_SESSION['notif']); ?>
        <?php endif; ?>

        <div class="katalog-grid">
            <?php
            // Pastikan $conn ada dan valid sebelum query
            if (isset($conn) && $conn) {
                $sql_tampil_obat = "SELECT id_obat, nama_obat, harga, stok, gambar, deskripsi FROM obat"; // Sebutkan kolom eksplisit
                if ($keyword) {
                    $sql_tampil_obat .= " WHERE nama_obat LIKE ?";
                }
                $sql_tampil_obat .= " ORDER BY nama_obat ASC";

                $stmt_tampil_obat = mysqli_prepare($conn, $sql_tampil_obat);

                if ($stmt_tampil_obat) {
                    if ($keyword) {
                        $search_param = "%{$keyword}%";
                        mysqli_stmt_bind_param($stmt_tampil_obat, "s", $search_param);
                    }
                    mysqli_stmt_execute($stmt_tampil_obat);
                    $result_tampil_obat = mysqli_stmt_get_result($stmt_tampil_obat);

                    if (mysqli_num_rows($result_tampil_obat) > 0):
                        while ($row = mysqli_fetch_assoc($result_tampil_obat)):
                    ?>
                    <div class="card-obat">
                        <div class="img-container">
                            <!-- Sediakan gambar default jika $row['gambar'] kosong atau tidak ada -->
                            <img src="../assets/img/<?= htmlspecialchars(!empty($row['gambar']) ? $row['gambar'] : 'default-obat.png') ?>" 
                                 alt="<?= htmlspecialchars($row['nama_obat']) ?>" 
                                 onerror="this.onerror=null;this.src='../assets/img/default-obat.png';" 
                                 loading="lazy" />
                        </div>
                        <h3><?= htmlspecialchars($row['nama_obat']) ?></h3>
                        <p class="harga">Rp<?= number_format($row['harga'], 0, ',', '.') ?></p>
                        <p class="stok">Stok: <?= ($row['stok'] > 0) ? htmlspecialchars($row['stok']) : '<span style="color:red;">Habis</span>' ?></p>
                        <p class="deskripsi"><?= nl2br(htmlspecialchars(substr($row['deskripsi'], 0, 100) . (strlen($row['deskripsi']) > 100 ? '...' : ''))) ?></p>
                        
                        <!-- Pastikan form ini mengirim 'id_obat_to_cart' -->
                        <form method="POST" action="kelola_obat.php<?= ($keyword ? "?search=" . urlencode($keyword) : "") ?>" class="form-tambah-keranjang">
                            <input type="hidden" name="id_obat_to_cart" value="<?= $row['id_obat'] ?>" />
                            <button type="submit" class="btn-tambah" <?= ($row['stok'] <= 0) ? 'disabled' : '' ?>>
                                <?= ($row['stok'] <= 0) ? 'Stok Habis' : 'Tambah ke Keranjang' ?>
                            </button>
                        </form>
                    </div>
                    <?php
                        endwhile;
                    else:
                        echo "<p class='empty-message'>Obat dengan kata kunci <strong>" . htmlspecialchars($keyword) . "</strong> tidak ditemukan.</p>";
                    endif;
                    mysqli_stmt_close($stmt_tampil_obat);
                } else {
                    echo "<p class='empty-message'>Terjadi kesalahan saat mempersiapkan query pencarian obat.</p>";
                    error_log("Gagal prepare statement tampil_obat: " . mysqli_error($conn));
                }
            } else {
                echo "<p class='empty-message'>Koneksi database tidak tersedia untuk menampilkan katalog.</p>";
            }
            ?>
        </div>

        <div class="back-home-container" style="text-align: center; margin-top: 30px;">
            <a href="../index.php" class="btn-back-home">&larr; Kembali ke Beranda</a>
        </div>
    </div>

<script>
const notif = document.getElementById('notif-popup');
if (notif) {
    setTimeout(() => {
        if (notif) { // Cek lagi karena bisa saja sudah di-remove oleh interaksi lain
            notif.style.opacity = '0';
            setTimeout(() => {
                if (notif) notif.remove();
            }, 600); // Waktu untuk transisi opacity selesai
        }
    }, 3000); // Notifikasi hilang setelah 3 detik
}
<script src="assets/header.js"></script>
<body>
<html>
