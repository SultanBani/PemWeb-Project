<?php
session_start();
$_SESSION['username'] = 'admin'; // Dummy login

include "db/koneksi.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>ObatSaku</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="navbar">
        <h2>ObatSaku - Dashboard</h2>
        <ul>
            <li><a href="pages/dashboard.php">Dashboard</a></li>
            <li><a href="pages/kelola_obat.php">Kelola Obat</a></li>
            <li><a href="pages/pesan_pengguna.php">Pesan Pengguna</a></li>
            <li><a href="pages/data_pesanan.php">Data Pesanan</a></li>
            <li><a href="pages/pembayaran.php">Pembayaran</a></li>
            <li><a href="pages/daftar_pengguna.php">Daftar Pengguna</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
</body>
</html>