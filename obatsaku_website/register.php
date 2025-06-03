<?php
include "db/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $nama_depan = $_POST['nama_depan'];
    $nama_belakang = $_POST['nama_belakang'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $password = $_POST['password'];

    $cek = mysqli_query($conn, "SELECT * FROM pengguna WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $query = "INSERT INTO pengguna (username, nama_depan, nama_belakang, email, no_hp, password, status_akun, tipe_pengguna) 
                  VALUES ('$username', '$nama_depan', '$nama_belakang', '$email', '$no_hp', '$password', 'Aktif', 'Pengguna')";
        if (mysqli_query($conn, $query)) {
            header("Location: login.php");
            exit;
        } else {
            $error = "Gagal mendaftar.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Register - ObatSaku</title></head>
<body>
    <h2>Registrasi Akun</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
        Username: <input type="text" name="username" required><br><br>
        Nama Depan: <input type="text" name="nama_depan" required><br><br>
        Nama Belakang: <input type="text" name="nama_belakang" required><br><br>
        Email: <input type="email" name="email" required><br><br>
        No HP: <input type="text" name="no_hp" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <button type="submit">Daftar</button>
    </form>
    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
</body>
</html>
