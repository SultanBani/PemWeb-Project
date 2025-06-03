<?php
include "../db/koneksi.php";
$result = mysqli_query($conn, "SELECT * FROM obat");
?>

<h2>Kelola Obat</h2>
<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>ID</th><th>Nama</th><th>Jenis</th><th>Harga</th><th>Stok</th><th>Kedaluwarsa</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= $row['id_obat']; ?></td>
        <td><?= $row['nama_obat']; ?></td>
        <td><?= $row['jenis_obat']; ?></td>
        <td>Rp<?= number_format($row['harga'], 0, ',', '.'); ?></td>
        <td><?= $row['stok']; ?></td>
        <td><?= $row['tanggal_kedaluwarsa']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<a href="../index.php">← Kembali</a>