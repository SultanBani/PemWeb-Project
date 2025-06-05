<?php
session_start();
include "db/koneksi.php"; // pastikan file koneksi ini ada dan benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $nama_depan = htmlspecialchars(trim($_POST['nama_depan']));
    $nama_belakang = htmlspecialchars(trim($_POST['nama_belakang']));
    $no_hp = htmlspecialchars(trim($_POST['no_hp']));
    $password = htmlspecialchars(trim($_POST['password']));
    $tipe_pengguna = htmlspecialchars(trim($_POST['tipe_pengguna']));
    $status_akun = "Aktif";

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Proses upload foto profil
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

    // Cek duplikasi username/email
    $cek = mysqli_query($conn, "SELECT * FROM pengguna WHERE username = '$username' OR email = '$email'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username atau Email sudah digunakan!";
    } else {
        // Simpan ke database
        $query = "INSERT INTO pengguna (username, email, nama_depan, nama_belakang, no_hp, password, foto_profil, tipe_pengguna, status_akun)
                  VALUES ('$username', '$email', '$nama_depan', '$nama_belakang', '$no_hp', '$hashed_password', '$foto_nama', '$tipe_pengguna', '$status_akun')";

        if (mysqli_query($conn, $query)) {
            // Simpan session jika mau langsung login
            $_SESSION['username'] = $username;
            $_SESSION['tipe_pengguna'] = $tipe_pengguna;
            header("Location: beranda.php");
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
    <meta charset="UTF-8">
    <title>Daftar - ObatSaku</title>
    <link rel="stylesheet" href="assets/css/register.css" />
</head>
<body>
    <div class="register-container">
        <h2 class="register-title">Daftar Akun ObatSaku</h2>

        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>

        <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" enctype="multipart/form-data" autocomplete="off">
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

            <button type="submit" class="form-button">Daftar</button>
        </form>

        <div class="form-footer">
            <p>Sudah punya akun? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
