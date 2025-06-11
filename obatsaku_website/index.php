<?php 
include 'header.php';

include "db/koneksi.php";

?>

    <!-- <title>ObatSaku</title> -->
    <!-- <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/layanan.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
    <script defer src="assets/style.js"></script>  -->

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
                <span class="badge-about">ObatSaku</span>
                <h1 class="about-title mb-4">Hallo Semua!!</h1>
                <p class="text-muted">ObatSaku adalah sebuah sistem informasi katalog obat berbasis web yang dirancang untuk memudahkan masyarakat dalam mencari dan memahami informasi seputar obat-obatan. Melalui tampilan yang sederhana dan fitur pencarian yang efisien, pengguna dapat mengakses data obat seperti nama, jenis, kegunaan, dosis, efek samping, hingga harga secara cepat dan akurat.</p>
                <p class="text-muted">ObatSaku hadir sebagai solusi digital untuk meningkatkan literasi obat dan membantu pengguna dalam mengambil keputusan yang lebih bijak terkait penggunaan obat.</p>
                <ul class="about-list">
                <li><i class="fas fa-check-circle"></i>Terpercaya</li>
                <li><i class="fas fa-check-circle"></i>Dapat diandalkan</li>
                <li><i class="fas fa-check-circle"></i>Profesional</li>
                </ul>
                <a class="btn btn-primary mt-3" href="#">Sekian</a>
            </div>
            </div>
        </div>
        </div>
        <!-- Tentang Kami End -->

        <!-- Kategori Start -->
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
        <!-- Kategori End -->


        <!-- Galeri -->
         <!-- Gambar 1 -->
         <h1 style="text-align: center;">Obat</h1>
          <div class="containerBerita">
            <div class="postBerita">
              <img src="assets/img/amoxicillin.jpg">
            </div>
            <div class="postBerita">
              <img src="assets/img/amoxicillin.jpg">
            </div>
            <div class="postBerita">
              <img src="assets/img/amoxicillin.jpg">
            </div>
            <div class="postBerita">
              <img src="assets/img/amoxicillin.jpg">
            </div>
            <div class="postBerita">
              <img src="assets/img/amoxicillin.jpg">
            </div>
            <div class="postBerita">
              <img src="assets/img/amoxicillin.jpg">
            </div>
            <div class="postBerita">
              <img src="assets/img/amoxicillin.jpg">
            </div>
            <div class="postBerita">
              <img src="assets/img/amoxicillin.jpg">
            </div>
            <!-- Tambah sebanyak mungkin box -->
          </div>

          <!-- Gambar 2 -->
           <div class="containerBerita">
            <div class="postBerita">
              <img src="assets/img/Paracetamol.png">
            </div>
            <div class="postBerita">
              <img src="assets/img/Paracetamol.png">
            </div>
            <div class="postBerita">
              <img src="assets/img/Paracetamol.png">
            </div>
            <div class="postBerita">
              <img src="assets/img/Paracetamol.png">
            </div>
            <div class="postBerita">
              <img src="assets/img/Paracetamol.png">
            </div>
            <div class="postBerita">
              <img src="assets/img/Paracetamol.png">
            </div>
            <div class="postBerita">
              <img src="assets/img/Paracetamol.png">
            </div>
            <div class="postBerita">
              <img src="assets/img/Paracetamol.png">
            </div>
    </main>

<script src="assets/header.js"></script>
<?php 
include 'footer.php'; 
?>