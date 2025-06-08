<?php
include "db/koneksi.php";
$result = mysqli_query($conn, "SELECT * FROM pengguna");
?>

<h2>Daftar Pengguna</h2>
<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>Username</th><th>Nama</th><th>Email</th><th>No HP</th><th>Status</th><th>Tipe</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= $row['username']; ?></td>
        <td><?= $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></td>
        <td><?= $row['email']; ?></td>
        <td><?= $row['no_hp']; ?></td>
        <td><?= $row['status_akun']; ?></td>
        <td><?= $row['tipe_pengguna']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<a href="../index.php">← Kembali</a>
