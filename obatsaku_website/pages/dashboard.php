<?php
include "../db/koneksi.php";

$jumlah_obat = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM obat"));
$jumlah_pengguna = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pengguna"));
$jumlah_pesanan = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pesanan"));
$jumlah_pembayaran = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pembayaran"));
?>

<h2>Dashboard</h2>
<ul>
    <li>Jumlah Obat: <?= $jumlah_obat; ?></li>
    <li>Jumlah Pengguna: <?= $jumlah_pengguna; ?></li>
    <li>Jumlah Pesanan: <?= $jumlah_pesanan; ?></li>
    <li>Jumlah Pembayaran: <?= $jumlah_pembayaran; ?></li>
</ul>
<a href="../index.php">← Kembali</a>