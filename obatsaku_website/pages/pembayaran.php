<?php include "../db/koneksi.php"; ?>
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
    $produkList = [];

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
                $produkList[] = "{$row['nama_produk']} ({$row['jumlah']}x)";
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                    <td>Rp<?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td><?= $row['jumlah'] ?></td>
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

    // Ambil isi tabel dan total
    const rows = document.querySelectorAll("table tr");
    let pesan = "*Detail Pesanan:*\n";

    for (let i = 1; i < rows.length; i++) {
        const cols = rows[i].querySelectorAll("td");
        if (cols.length === 4) {
            pesan += `- ${cols[0].innerText} (${cols[2].innerText}x) = ${cols[3].innerText}\n`;
        }
    }

    const total = document.getElementById("total-harga").innerText;
    pesan += `\n*Total Pembayaran:* ${total}`;

    // Kirim ke WhatsApp
    const waUrl = `https://wa.me/${nomorWA}?text=${encodeURIComponent(pesan)}`;
    window.open(waUrl, '_blank');

    // Cetak nota
    window.print();

    // Kosongkan keranjang setelah proses
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