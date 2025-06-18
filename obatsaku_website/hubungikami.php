<?php 
include 'header.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "db/koneksi.php";

// Cek koneksi database
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}
?>

<link rel="stylesheet" href="assets/css/hubungikami.css" />

<div class="hubungi-kami-container">
    <div class="about-section">
        <h2>Tentang Kami</h2>
        <p>
            ObatSaku adalah solusi praktis untuk beli obat dan cari info kesehatan tepercaya, semua dalam satu genggaman!
            Temukan obat, pahami kegunaannya, dan jaga kesehatanmu dengan lebih mudah bersama ObatSaku.
        </p>
    </div>

    <div class="contact-form">
        <h2>Hubungi Kami</h2>

        <?php
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["nama"])) {
            $nama       = htmlspecialchars(trim($_POST["nama"]));
            $emailRaw   = trim($_POST["email"]);
            $perusahaan = htmlspecialchars(trim($_POST["perusahaan"]));
            $teleponRaw = trim($_POST["telepon"]);
            $pesan      = htmlspecialchars(trim($_POST["pesan"]));

            // Validasi
            $email   = filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
            $telepon = preg_replace('/[^0-9]/', '', $teleponRaw); // hanya angka

            if (!$email) {
                echo "<div class='error-message'>Format email tidak valid.</div>";
            } elseif (empty($nama) || empty($pesan)) {
                echo "<div class='error-message'>Nama dan pesan wajib diisi.</div>";
            } else {
                $stmt = $conn->prepare("INSERT INTO kontak (nama, email, perusahaan, telepon, pesan) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $nama, $email, $perusahaan, $telepon, $pesan);

                if ($stmt->execute()) {
                    echo "<div class='success-message'>";
                    echo "<p><strong>Terima kasih, $nama!</strong> Pesan Anda telah berhasil dikirim.</p>";
                    echo "<ul>";
                    echo "<li><strong>Email:</strong> $email</li>";
                    echo "<li><strong>Perusahaan:</strong> $perusahaan</li>";
                    echo "<li><strong>Telepon:</strong> $telepon</li>";
                    echo "<li><strong>Pesan:</strong> $pesan</li>";
                    echo "</ul>";
                    echo "</div>";
                } else {
                    echo "<div class='error-message'>Gagal mengirim pesan: " . $stmt->error . "</div>";
                }

                $stmt->close();
            }
        }
        ?>

        <form method="post" action="hubungikami.php" autocomplete="off">
            <div class="form-group">
                <input type="text" name="nama" class="form-input" placeholder="Nama" required />
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-input" placeholder="Alamat Email" required />
            </div>
            <div class="form-group">
                <input type="text" name="perusahaan" class="form-input" placeholder="Perusahaan" />
            </div>
            <div class="form-group">
                <input type="text" name="telepon" class="form-input" placeholder="Telepon" />
            </div>
            <div class="form-group">
                <textarea name="pesan" class="form-textarea" placeholder="Tulis pesan Anda..." rows="6" required></textarea>
            </div>
            <button type="submit" class="form-button">KIRIM</button>
        </form>
    </div>
</div>

<script src="assets/js/header.js"></script>

<?php include 'footer.php'; ?>
