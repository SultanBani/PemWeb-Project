<?php
session_start();
include "db/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM pengguna WHERE username = '$username' AND password = '$password'");
    if (mysqli_num_rows($result) == 1) {
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Login - ObatSaku</title>
    <link rel="stylesheet" href="assets/css/login.css" />
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">Login ke ObatSaku</h2>

        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>

        <form method="post" autocomplete="off">
            <div class="form-group">
                <div class="form-col" style="width: 100%;">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-input" required />
                </div>
            </div>

            <div class="form-group">
                <div class="form-col" style="width: 100%;">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-input" required />
                </div>
            </div>

            <button type="submit" class="form-button">Login</button>
        </form>

        <div class="form-footer">
            <p>Belum punya akun? <a href="register.php">Daftar</a></p>
        </div>
    </div>
</body>
</html>
