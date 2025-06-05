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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
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
            <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="d-flex flex-column position-relative">
                <img class="img-fluid rounded w-75 align-self-end" src="assets/img/medical.jpg" alt="gambar1">
                <img class="img-kecil" src="assets/img/medical2.jpg" alt="Gambar kecil">
                </div>
            </div>
            <div class="col-lg-6">
                <span class="badge-about">About Us</span>
                <h1 class="about-title mb-4">Why You Should Trust Us? Get Know About Us!</h1>
                <p class="text-muted">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo magna dolore erat amet</p>
                <p class="text-muted">Stet no et lorem dolor et diam, amet duo ut dolore vero eos. No stet est diam rebum amet diam ipsum. Clita clita labore, dolor duo nonumy clita sit at, sed sit sanctus dolor eos.</p>
                <ul class="about-list">
                <li><i class="fas fa-check-circle"></i>Quality health care</li>
                <li><i class="fas fa-check-circle"></i>Only Qualified Doctors</li>
                <li><i class="fas fa-check-circle"></i>Medical Research Professionals</li>
                </ul>
                <a class="btn btn-primary mt-3" href="#">Read More</a>
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
                        <a href="#" class="read-more">+ Read More</a>
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
                        <a href="#" class="read-more">+ Read More</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="icon-circle">♿</div>
                        <h5 class="fw-bold">Antipiretik</h5>
                        <p>Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet.</p>
                        <a href="#" class="read-more">+ Read More</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="icon-circle">🦷</div>
                        <h5 class="fw-bold">Antasida</h5>
                        <p>Obat yang digunakan untuk menetralisir asam lambung dan meredakan gangguan lambung seperti maag dan perih.</p>
                        <a href="#" class="read-more">+ Read More</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="icon-circle">🧪</div>
                        <h5 class="fw-bold">Antitussive & Ekspektoran</h5>
                        <p>Obat untuk mengobati batuk.</p>
                        <a href="#" class="read-more">+ Read More</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Layanan Kami End -->


        <!-- Image Galerry -->

    </main>
</body>
</html>
