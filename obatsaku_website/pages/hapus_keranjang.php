<?php
include "../db/koneksi.php";

header('Content-Type: application/json');

$sql = "TRUNCATE TABLE keranjang"; // Kosongkan tabel keranjang
$result = mysqli_query($conn, $sql);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
