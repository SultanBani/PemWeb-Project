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
    <link rel="stylesheet" href="assets/css/layanan.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="navbar">
        <h2>ObatSaku - Dashboard</h2>
        <ul>
            <li><a href="pages/dashboard.php">Beranda</a></li>
            <li><a href="pages/kelola_obat.php">Kelola Obat</a></li>
            <li><a href="pages/data_pesanan.php">Data Pesanan</a></li>
            <li><a href="pages/pembayaran.php">Pembayaran</a></li>
            <li><a href="pages/daftar_pengguna.php">Daftar Pengguna</a></li>
            <li><a href="pages/pesan_pengguna.php">Hubungi Kami</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Konten -->
    <main>

        <!-- Tentang Kami Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row g-5">
                    <div class="col-lg-6">
                        <div class="d-flex flex-column">
                            <img class="img-fluid rounded w-75 align-self-end" src="img/about-1.jpg" alt="">
                            <img class="img-fluid rounded w-50 bg-white pt-3 pe-3" src="img/about-2.jpg" alt="" style="margin-top: -25%;">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <p class="d-inline-block border rounded-pill py-1 px-4">Tentang Kami</p>
                        <h1 class="mb-4">Kenapa Sebaiknya Percaya Kami? Kenali Kami!</h1>
                        <p>ObatSaku adalah sebuah sistem informasi katalog obat berbasis web yang dirancang untuk memudahkan masyarakat dalam mencari dan memahami informasi seputar obat-obatan. Melalui tampilan yang sederhana dan fitur pencarian yang efisien, pengguna dapat mengakses data obat seperti nama, jenis, kegunaan, dosis, efek samping, hingga harga secara cepat dan akurat. ObatSaku hadir sebagai solusi digital untuk meningkatkan literasi obat dan membantu pengguna dalam mengambil keputusan yang lebih bijak terkait penggunaan obat.</p>
                        <p class="mb-4">Ada apa saja disini?</p>
                        <p><i class="far fa-check-circle text-primary me-3"></i>Quality health care</p>
                        <p><i class="far fa-check-circle text-primary me-3"></i>Only Qualified Doctors</p>
                        <p><i class="far fa-check-circle text-primary me-3"></i>Medical Research Professionals</p>
                        <a class="btn btn-primary rounded-pill py-3 px-5 mt-3" href="">Read More</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Tentang Kami End -->

        <!-- Layanan Kami Start -->
        <div class="container py-5">
            <h2 class="text-center fw-bold mb-5">Kategori Obat</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="icon-circle">❤️</div>
                        <h5 class="fw-bold">Antihistamin</h5>
                        <p>Obat yang digunakan untuk mengurangi reaksi alergi seperti gatal-gatal, hidung tersumbat, atau bersin.</p>
                        <a href="#" class="plus-btn">+</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="icon-circle">🏥</div>
                        <h5 class="fw-bold">Analgesik (Pereda Nyeri)</h5>
                        <p>Obat yang digunakan untuk meringankan atau menghilangkan rasa nyeri, seperti sakit kepala, nyeri otot, dan nyeri haid.</p>
                        <a href="#" class="read-more">+ Read More</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="icon-circle">🧠</div>
                        <h5 class="fw-bold">Antibiotik</h5>
                        <p>Obat yang digunakan untuk mengobati infeksi bakteri. Tidak efektif untuk virus seperti flu.</p>
                        <a href="#" class="plus-btn">+</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="icon-circle">♿</div>
                        <h5 class="fw-bold">Antipiretik</h5>
                        <p>Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet.</p>
                        <a href="#" class="plus-btn">+</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="icon-circle">🦷</div>
                        <h5 class="fw-bold">Antasida</h5>
                        <p>Obat yang digunakan untuk menetralisir asam lambung dan meredakan gangguan lambung seperti maag dan perih.</p>
                        <a href="#" class="plus-btn">+</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="icon-circle">🧪</div>
                        <h5 class="fw-bold">Antitussive & Ekspektoran</h5>
                        <p>Obat untuk mengobati batuk.</p>
                        <a href="#" class="plus-btn">+</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Layanan Kami End -->

    </main>
</body>
</html>
