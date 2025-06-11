<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once "db/koneksi.php";
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ObatSaku</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/layanan.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="assets/css/data_pesanan.css">
    <script defer src="assets/style.js"></script> 

</head>
<body>
    <header class="header" id="header">
        <nav class="nav container-nav">
            <a href="index.php" class="nav__logo"><h3>ObatSaku</h3></a>
            <div class="nav__menu" id="nav-menu">
                <ul class="nav__list">
                    <li class="nav__item"><a href="index.php" class="nav__link <?= ($current_page == 'index.php') ? 'active-link' : '' ?>">Beranda</a></li>
                    <li class="nav__item"><a href="kelola_obat.php" class="nav__link <?= ($current_page == 'kelola_obat.php') ? 'active-link' : '' ?>">Katalog Obat</a></li>
                    <li class="nav__item"><a href="data_pesanan.php" class="nav__link <?= ($current_page == 'data_pesanan.php') ? 'active-link' : '' ?>">Data Pesanan</a></li>
                    <li class="nav__item"><a href="pembayaran.php" class="nav__link <?= ($current_page == 'pembayaran.php') ? 'active-link' : '' ?>">Pembayaran</a></li>
                    <li class="nav__item"><a href="hubungikami.php" class="nav__link <?= ($current_page == 'hubungikami.php') ? 'active-link' : '' ?>">Hubungi Kami</a></li>
                    <li class="nav__item"><a href="logout.php" class="nav__link">Logout</a></li>
                </ul>
                <div class="nav__close" id="nav-close"><i class="ri-close-line"></i></div>
            </div>
            <div class="nav__actions">
                <i class="ri-search-line nav__search" id="search-btn"></i>
                <i class="ri-user-line nav__login" id="login-btn"></i>
                <div class="nav__toggle" id="nav-toggle"><i class="ri-menu-line"></i></div>
            </div>
        </nav>
    </header>
    <div class="search" id="search">
        <form action="kelola_obat.php" method="GET" class="search__form">
            <i class="ri-search-line search__icon"></i>
            <input type="search" name="search" placeholder="Cari obat..." class="search__input">
        </form>
        <i class="ri-close-line search__close" id="search-close"></i>
    </div>
    <div class="login" id="login">
        <form action="login.php" method="POST" class="login__form">
            <h2 class="login__title">Masuk</h2>
            <div class="login__group">
                <div>
                    <label for="username_login" class="login__label">Username</label>
                    <input type="text" name="username" placeholder="Masukkan username Anda" id="username_login" class="login__input">
                </div>
                <div>
                    <label for="password_login" class="login__label">Kata Sandi</label>
                    <input type="password" name="password" placeholder="Masukkan kata sandi Anda" id="password_login" class="login__input">
                </div>
            </div>
            <div>
                <p class="login__signup">Belum punya akun? <a href="register.php">Daftar</a></p>
                <button type="submit" class="login__button">Masuk</button>
            </div>
        </form>
        <i class="ri-close-line login__close" id="login-close"></i>
    </div>
    <main>