<?php 
include "../db/koneksi.php";

// Kurangi jumlah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    $cek = mysqli_query($conn, "SELECT jumlah FROM keranjang WHERE id = $id");
    $data = mysqli_fetch_assoc($cek);

    if ($data) {
        if ($action === 'plus') {
            mysqli_query($conn, "UPDATE keranjang SET jumlah = jumlah + 1 WHERE id = $id");
        } elseif ($action === 'minus') {
            if ($data['jumlah'] > 1) {
                mysqli_query($conn, "UPDATE keranjang SET jumlah = jumlah - 1 WHERE id = $id");
            } else {
                mysqli_query($conn, "DELETE FROM keranjang WHERE id = $id");
            }
        }
    }

    header("Location: pembayaran.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>
    <link rel="stylesheet" href="../assets/css/pembayaran.css">
</head>
<body>
<div class="container" id="nota">
    <h2>Halaman Pembayaran</h2>

    <?php
    $sql = "SELECT * FROM keranjang";
    $result = mysqli_query($conn, $sql);
    $total = 0;

    if (mysqli_num_rows($result) > 0): ?>
        <table>
            <tr>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)):
                $subtotal = $row['harga'] * $row['jumlah'];
                $total += $subtotal;
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                    <td>Rp<?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="action" value="minus">
                            <button type="submit" class="btn-ikon">−</button>
                        </form>
                        <?= $row['jumlah'] ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="action" value="plus">
                            <button type="submit" class="btn-ikon">+</button>
                        </form>
                    </td>
                    <td>Rp<?= number_format($subtotal, 0, ',', '.') ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <p class="total">Total Pembayaran: <strong id="total-harga">Rp<?= number_format($total, 0, ',', '.') ?></strong></p>

        <div style="text-align:right; margin-top:20px;">
            <button class="btn-wa" onclick="cetakDanKirim()">Cetak Nota & Kirim WA</button>
        </div>

    <?php else: ?>
        <p class="empty-message">Tidak ada item dalam keranjang.</p>
    <?php endif; ?>

    <a href="../index.php" class="back-link">← Kembali ke Beranda</a>
</div>

<script>
function cetakDanKirim() {
    const nomorWA = "6285367336284";
    const rows = document.querySelectorAll("table tr");
    let pesan = "*Detail Pesanan:*\n";

    for (let i = 1; i < rows.length; i++) {
        const cols = rows[i].querySelectorAll("td");
        if (cols.length >= 4) {
            const nama = cols[0].innerText;
            const jumlah = cols[2].innerText.replace(/[^0-9]/g, '');
            const subtotal = cols[3].innerText;
            pesan += `- ${nama} (${jumlah}x) = ${subtotal}\n`;
        }
    }

    const total = document.getElementById("total-harga").innerText;
    pesan += `\n*Total Pembayaran:* ${total}`;

    const waUrl = `https://wa.me/${nomorWA}?text=${encodeURIComponent(pesan)}`;
    window.open(waUrl, '_blank');
    window.print();

    fetch("hapus_keranjang.php", { method: 'POST' })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                alert("Nota dicetak & data dikirim ke WhatsApp.");
                location.reload();
            } else {
                alert("Gagal menghapus isi keranjang.");
            }
        }).catch(() => alert("Gagal proses pengosongan keranjang."));
}
</script>
</body>
</html>