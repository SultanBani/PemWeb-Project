<?php include "../db/koneksi.php"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background: #FBFBFB;
        }
        .nota {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            max-width: 800px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border-bottom: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }
        th {
            background: #C5BAFF;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
        }
        .btn-cetak {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 24px;
            background-color: #C4D9FF;
            color: #000;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-cetak:disabled {
            background-color: #aaa;
            cursor: not-allowed;
        }
        @media print {
            .btn-cetak, .btn-back {
                display: none;
            }
        }
        .empty-message {
            text-align: center;
            color: #666;
            font-style: italic;
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            background: #C5BAFF;
            color: #000;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-back:hover {
            background: #b7aaff;
        }
    </style>
</head>
<body>
<div class="nota" id="notaContainer">
    <h2>Nota Pembayaran</h2>
    <?php
    $sql = "SELECT * FROM keranjang";
    $result = mysqli_query($conn, $sql);
    $total = 0;

    if (mysqli_num_rows($result) > 0): ?>
        <table id="tableKeranjang">
            <tr>
                <th>Produk</th>
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
                    <td><?= $row['jumlah'] ?></td>
                    <td>Rp<?= number_format($subtotal, 0, ',', '.') ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <p class="total" id="totalBayar">Total: Rp<?= number_format($total, 0, ',', '.') ?></p>
        <button class="btn-cetak" id="btnCetak">Cetak & Hapus Keranjang</button>
        <br>
        <a href="../index.php" class="btn-back">← Kembali ke Beranda</a>
    <?php else: ?>
        <p class="empty-message">Tidak ada item dalam keranjang.</p>
        <a href="../index.php" class="btn-back">← Kembali ke Beranda</a>
    <?php endif; ?>
</div>

<script>
document.getElementById('btnCetak')?.addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    window.print();

    fetch('hapus_keranjang.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const container = document.getElementById('notaContainer');
            container.innerHTML = `
                <p class="empty-message">Keranjang telah dikosongkan setelah cetak.</p>
                <a href="../index.php" class="btn-back">← Kembali ke Beranda</a>
            `;
        } else {
            alert('Gagal menghapus keranjang!');
            btn.disabled = false;
        }
    })
    .catch(() => {
        alert('Terjadi kesalahan saat menghapus keranjang.');
        btn.disabled = false;
    });
});
</script>
</body>
</html>
