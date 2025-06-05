<?php
session_start();
$_SESSION['username'] = 'admin'; // Dummy login

include "db/koneksi.php";
?>

<!DOCTYPE html>
   <html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <!--=============== REMIXICONS ===============-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">

      <!--=============== CSS ===============-->
      <link rel="stylesheet" href="assets/css/header.css">

      <title>Navigasi Bar</title>
   </head>
   <body>
      <!--==================== HEADER ====================-->
      <header class="header" id="header">
         <nav class="nav container">
            <a href="#" class="nav__logo"><h3>ObatSaku<h3></h1></a>

            <div class="nav__menu" id="nav-menu">
               <ul class="nav__list">
                  <li class="nav__item">
                     <a href="index.php" class="nav__link">Beranda</a>
                  </li>

                  <li class="nav__item">
                     <a href="pages/kelola_obat.php" class="nav__link">Katalog Obat</a>
                  </li>

                  <li class="nav__item">
                     <a href="pages/data_pesanan.php" class="nav__link">Data Pesanan</a>
                  </li>

                  <li class="nav__item">
                     <a href="pages/pembayaran.php" class="nav__link">Pembayaran</a>
                  </li>

                  <li class="nav__item">
                     <a href="hubungikami.php" class="nav__link">Hubungi Kami</a>
                  </li>

                 <li class="nav__item">
                     <a href="logout.php" class="nav__link">Logout</a>
                  </li>

               </ul>

               <!-- Close button -->
               <div class="nav__close" id="nav-close">
                  <i class="ri-close-line"></i>
               </div>
            </div>

            <div class="nav__actions">
               <!-- Search button -->
               <i class="ri-search-line nav__search" id="search-btn"></i>

               <!-- Login button -->
               <i class="ri-user-line nav__login" id="login-btn"></i>

               <!-- Toggle button -->
               <div class="nav__toggle" id="nav-toggle">
                  <i class="ri-menu-line"></i>
               </div>
            </div>
         </nav>
      </header>

      <!--==================== SEARCH ====================-->
      <div class="search" id="search">
         <form action="" class="search__form">
            <i class="ri-search-line search__icon"></i>
            <input type="search" placeholder="Apa yang Anda cari?" class="search__input">
         </form>

         <i class="ri-close-line search__close" id="search-close"></i>
      </div>

      <!--==================== LOGIN ====================-->
      <div class="login" id="login">
   <form action="" class="login__form">
      <h2 class="login__title">Masuk</h2>
      
      <div class="login__group">
         <div>
            <label for="email" class="login__label">Email</label>
            <input type="email" placeholder="Masukkan email Anda" id="email" class="login__input">
         </div>
         
         <div>
            <label for="password" class="login__label">Kata Sandi</label>
            <input type="password" placeholder="Masukkan kata sandi Anda" id="password" class="login__input">
         </div>
      </div>

      <div>
         <p class="login__signup">
            Belum punya akun? <a href="#">Daftar</a>
         </p>

         <a href="#" class="login__forgot">
            Lupa kata sandi?
         </a>

         <button type="submit" class="login__button">Masuk</button>
      </div>
   </form>


         <i class="ri-close-line login__close" id="login-close"></i>
      </div>

      <!--==================== MAIN ====================-->
      
      <!--=============== MAIN JS ===============-->
      <script src="assets/header.js"></script>
   </body>
</html>