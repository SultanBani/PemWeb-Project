<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hubungi Kami - ObatSaku</title>
    <link rel="stylesheet" href="assets/css/hubungikami.css" />
</head>
<body>
    <div class="container">
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
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $nama       = htmlspecialchars($_POST["nama"]);
                $email      = htmlspecialchars($_POST["email"]);
                $perusahaan = htmlspecialchars($_POST["perusahaan"]);
                $telepon    = htmlspecialchars($_POST["telepon"]);
                $pesan      = htmlspecialchars($_POST["pesan"]);

                echo "<div class='success-message'>";
                echo "<p><strong>Terima kasih, $nama!</strong></p>";
                echo "<p>Pesan Anda telah berhasil dikirim.</p>";
                echo "<ul>";
                echo "<li><strong>Email:</strong> $email</li>";
                echo "<li><strong>Perusahaan:</strong> $perusahaan</li>";
                echo "<li><strong>Telepon:</strong> $telepon</li>";
                echo "<li><strong>Pesan:</strong> $pesan</li>";
                echo "</ul>";
                echo "</div>";
            }
            ?>

            <form method="post" action="hubungi.php" autocomplete="off">
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
</body>
</html>