<?php
include "../db/koneksi.php";
session_start();

$keyword = '';
if (isset($_GET['search'])) {
    $keyword = trim(mysqli_real_escape_string($conn, $_GET['search']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_obat'])) {
    $id_obat = intval($_POST['id_obat']);

    $sql = "SELECT nama_obat, harga FROM obat WHERE id_obat = $id_obat";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $obat = mysqli_fetch_assoc($result);
        $nama_produk = mysqli_real_escape_string($conn, $obat['nama_obat']);

        $cekKeranjang = mysqli_query($conn, "SELECT * FROM keranjang WHERE nama_produk = '$nama_produk'");
        if (mysqli_num_rows($cekKeranjang) > 0) {
            mysqli_query($conn, "UPDATE keranjang SET jumlah = jumlah + 1 WHERE nama_produk = '$nama_produk'");
        } else {
            mysqli_query($conn, "INSERT INTO keranjang (nama_produk, harga, jumlah) VALUES ('$nama_produk', {$obat['harga']}, 1)");
        }

        $_SESSION['notif'] = "Berhasil menambahkan <strong>" . htmlspecialchars($obat['nama_obat']) . "</strong> ke keranjang!";
    } else {
        $_SESSION['notif'] = "Obat tidak ditemukan.";
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
    <link rel="stylesheet" href="../assets/css/kelola_obat.css" />
</head>
<body>
    <div class="container">
        <h2>Katalog Obat</h2>

        <form method="GET" class="form-search">
            <input type="text" name="search" placeholder="Cari nama obat..." value="<?= htmlspecialchars($keyword) ?>" autocomplete="off" />
            <button type="submit">Cari</button>
            <?php if ($keyword): ?>
                <a href="kelola_obat.php" class="btn-reset">Reset</a>
            <?php endif; ?>
        </form>

        <?php if (isset($_SESSION['notif'])): ?>
            <div class="notif-popup" id="notif-popup">
                <?= $_SESSION['notif'] ?>
            </div>
            <?php unset($_SESSION['notif']); ?>
        <?php endif; ?>

        <div class="katalog-grid">
            <?php
            $sql = "SELECT * FROM obat";
            if ($keyword) {
                $sql .= " WHERE nama_obat LIKE '%$keyword%'";
            }
            $sql .= " ORDER BY nama_obat ASC";

            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0):
                while ($row = mysqli_fetch_assoc($result)):
            ?>
            <div class="card-obat">
                <div class="img-container">
                    <img src="../assets/img/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_obat']) ?>" loading="lazy" />
                </div>
                <h3><?= htmlspecialchars($row['nama_obat']) ?></h3>
                <p class="harga">Rp<?= number_format($row['harga'], 0, ',', '.') ?></p>
                <p class="deskripsi"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></p>
                <form method="POST" class="form-tambah-keranjang">
                    <input type="hidden" name="id_obat" value="<?= $row['id_obat'] ?>" />
                    <button type="submit" class="btn-tambah">Tambah ke Keranjang</button>
                </form>
            </div>
            <?php
                endwhile;
            else:
                echo "<p class='empty-message'>Obat dengan kata kunci <strong>" . htmlspecialchars($keyword) . "</strong> tidak ditemukan.</p>";
            endif;
            ?>
        </div>

        <div class="back-home-container">
            <a href="../index.php" class="btn-back-home">← Kembali ke Beranda</a>
        </div>
    </div>

<script>
const notif = document.getElementById('notif-popup');
if (notif) {
    setTimeout(() => {
        notif.style.opacity = '0';
        setTimeout(() => notif.remove(), 600);
    }, 3000);
}
</script>
</body>
</html>
