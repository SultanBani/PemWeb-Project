<!-- <?php include "../db/koneksi.php"; ?> -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FBFBFB;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #E8F9FF;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        h2 {
            text-align: center;
            color: #4A4A4A;
            font-size: 28px;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        th {
            background-color: #C5BAFF;
            color: #222;
            text-align: left;
            padding: 14px;
        }
        td {
            padding: 14px;
            border-top: 1px solid #e2e2e2;
        }
        .total {
            text-align: right;
            font-size: 18px;
            margin-top: 20px;
        }
        .btn-bayar {
            display: inline-block;
            background: linear-gradient(to right, #C5BAFF, #C4D9FF);
            color: #222;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s ease, transform 0.2s;
        }
        .btn-bayar:hover {
            background: linear-gradient(to right, #b7aaff, #aac9ff);
            transform: translateY(-2px);
        }
        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: #444;
            text-decoration: none;
            font-size: 14px;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .empty-message {
            text-align: center;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
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
                        <td><?= $row['jumlah'] ?></td>
                        <td>Rp<?= number_format($subtotal, 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <p class="total">Total Pembayaran: <strong>Rp<?= number_format($total, 0, ',', '.') ?></strong></p>

            <div style="text-align:right;">
                <a href="https://contoh-pembayaran.com/checkout" class="btn-bayar" target="_blank">Bayar Sekarang</a>
            </div>

        <?php else: ?>
            <p class="empty-message">Tidak ada item dalam keranjang.</p>
        <?php endif; ?>

        <a href="../index.php" class="back-link">← Kembali ke Beranda</a>
    </div>
</body>
</html>
