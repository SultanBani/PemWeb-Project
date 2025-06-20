<?php
session_start();
include "db/koneksi.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username       = htmlspecialchars(trim($_POST['username']));
    $email          = htmlspecialchars(trim($_POST['email']));
    $nama_depan     = htmlspecialchars(trim($_POST['nama_depan']));
    $nama_belakang  = htmlspecialchars(trim($_POST['nama_belakang']));
    $no_hp          = htmlspecialchars(trim($_POST['no_hp']));
    $password       = htmlspecialchars(trim($_POST['password']));
    $tipe_pengguna  = htmlspecialchars(trim($_POST['tipe_pengguna']));
    $status_akun    = "Aktif"; // otomatis aktif

    // Upload foto profil jika ada
    $foto_nama = null;
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $foto_tmp = $_FILES['foto_profil']['tmp_name'];
        $foto_nama = basename($_FILES['foto_profil']['name']);
        $target_dir = "uploads/";
        $target_file = $target_dir . $foto_nama;

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        move_uploaded_file($foto_tmp, $target_file);
    }

    // Cek duplikat username atau email
    $cek = mysqli_query($conn, "SELECT * FROM pengguna WHERE username = '$username' OR email = '$email'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username atau Email sudah digunakan!";
    } else {
        // Simpan data ke database tanpa mengacak password (plain text)
        $query = "INSERT INTO pengguna 
                  (username, email, nama_depan, nama_belakang, no_hp, password, foto_profil, tipe_pengguna, status_akun)
                  VALUES 
                  ('$username', '$email', '$nama_depan', '$nama_belakang', '$no_hp', '$password', '$foto_nama', '$tipe_pengguna', '$status_akun')";

        if (mysqli_query($conn, $query)) {
            header("Location: login.php?pesan=register_berhasil");
            exit;
        } else {
            $error = "Gagal menyimpan data, silakan coba lagi.";
        }
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
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-input" required />
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-input" required />
            </div>

            <div class="form-group">
                <label for="nama_depan">Nama Depan</label>
                <input type="text" name="nama_depan" id="nama_depan" class="form-input" required />
            </div>

            <div class="form-group">
                <label for="nama_belakang">Nama Belakang</label>
                <input type="text" name="nama_belakang" id="nama_belakang" class="form-input" required />
            </div>

            <div class="form-group">
                <label for="no_hp">Nomor HP</label>
                <input type="text" name="no_hp" id="no_hp" class="form-input" required />
            </div>

            <div class="form-group">
                <label for="password">Password (minimal 6 karakter)</label>
                <input type="password" name="password" id="password" class="form-input" minlength="6" required />
            </div>

            <div class="form-group">
                <label for="foto_profil">Foto Profil</label>
                <input type="file" name="foto_profil" id="foto_profil" class="form-input" accept="image/*" />
            </div>

            <div class="form-group">
                <label for="tipe_pengguna">Tipe Pengguna</label>
                <select name="tipe_pengguna" id="tipe_pengguna" class="form-input" required>
                    <option value="Pengguna">Pengguna</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>

            <button type="submit" class="form-button">Daftar</button>
        </form>

        <div class="form-footer">
            <p>Sudah punya akun? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>