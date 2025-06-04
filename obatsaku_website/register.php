<?php
session_start();
include "db/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    $result = mysqli_query($conn, "SELECT * FROM pengguna WHERE username = '$username'");
    
    if (mysqli_num_rows($result) === 1) {
        $data = mysqli_fetch_assoc($result);

        // Jika password disimpan dalam bentuk teks biasa
        if ($password === $data['password']) {
            $_SESSION['username'] = $data['username'];
            $_SESSION['nama'] = $data['nama_depan'] . " " . $data['nama_belakang'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Register - ObatSaku</title>
    <link rel="stylesheet" href="assets/css/register.css" />
</head>
<body>
    <div class="register-container">
        <h2 class="register-title">Daftar Akun ObatSaku</h2>

        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>

        <form method="post" action="register.php" enctype="multipart/form-data" autocomplete="off">
            <div class="form-group">
                <div class="form-col">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-input" required />
                </div>
                <div class="form-col">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-input" required />
                </div>
            </div>

            <div class="form-group">
                <div class="form-col">
                    <label for="nama_depan">Nama Depan</label>
                    <input type="text" name="nama_depan" id="nama_depan" class="form-input" required />
                </div>
                <div class="form-col">
                    <label for="nama_belakang">Nama Belakang</label>
                    <input type="text" name="nama_belakang" id="nama_belakang" class="form-input" required />
                </div>
            </div>

            <div class="form-group">
                <div class="form-col">
                    <label for="no_hp">Nomor HP</label>
                    <input type="text" name="no_hp" id="no_hp" class="form-input" required />
                </div>
                <div class="form-col">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-input" required />
                </div>
            </div>

            <div class="form-group">
                <div class="form-col">
                    <label for="foto_profil">Foto Profil</label>
                    <input type="file" name="foto_profil" id="foto_profil" class="form-input" accept="image/*" />
                </div>
                <div class="form-col">
                    <label for="tipe_pengguna">Tipe Pengguna</label>
                    <select name="tipe_pengguna" id="tipe_pengguna" class="form-input" required>
                        <option value="Pengguna">Pengguna</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
            </div>

            <input type="hidden" name="status_akun" value="Aktif" />

            <button type="submit" class="form-button">Daftar</button>
        </form>

        <div class="form-footer">
            <p>Sudah punya akun? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
