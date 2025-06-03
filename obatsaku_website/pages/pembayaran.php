<?php 
include "../db/koneksi.php"; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Halaman Pembayaran</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FBFBFB;
            margin: 0; padding: 0; color: #333;
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
        .btn-bayar, .btn-hapus {
            display: inline-block;
            background: linear-gradient(to right, #C5BAFF, #C4D9FF);
            color: #222;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s;
            margin-right: 10px;
        }
        .btn-bayar:hover, .btn-hapus:hover {
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
<div class="container" id="notaContainer">
    <h2>Halaman Pembayaran</h2>

    <?php
    $sql = "SELECT * FROM keranjang";
    $result = mysqli_query($conn, $sql);
    $total = 0;

    if (mysqli_num_rows($result) > 0): 
        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $subtotal = $row['harga'] * $row['jumlah'];
            $total += $subtotal;
            $items[] = [
                'nama_produk' => $row['nama_produk'],
                'harga' => $row['harga'],
                'jumlah' => $row['jumlah'],
                'subtotal' => $subtotal
            ];
        }
    ?>

        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                    <td>Rp<?= number_format($item['harga'], 0, ',', '.') ?></td>
                    <td><?= $item['jumlah'] ?></td>
                    <td>Rp<?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <p class="total">Total Pembayaran: <strong>Rp<?= number_format($total, 0, ',', '.') ?></strong></p>

        <div style="text-align:right; margin-top:20px;">
            <button id="btnBayar" class="btn-bayar">Bayar Sekarang</button>
        </div>

    <?php else: ?>
        <p class="empty-message">Tidak ada item dalam keranjang.</p>
    <?php endif; ?>

    <a href="../index.php" class="back-link">← Kembali ke Beranda</a>
</div>

<script>
document.getElementById('btnBayar')?.addEventListener('click', function() {
    if(!confirm('Yakin ingin melanjutkan pembayaran via WhatsApp dan cetak nota?')) return;

    const btn = this;
    btn.disabled = true;

    // Data keranjang dari PHP ke JS
    const items = <?= json_encode($items ?? []) ?>;
    let total = 0;
    let messageLines = [];
    messageLines.push("🧾 *Nota Pembayaran* 🧾");

    items.forEach(item => {
        total += item.subtotal;
        messageLines.push(`- ${item.nama_produk} (${item.jumlah} x Rp${item.harga.toLocaleString('id-ID')}) = Rp${item.subtotal.toLocaleString('id-ID')}`);
    });
    messageLines.push(`\n*Total: Rp${total.toLocaleString('id-ID')}*`);

    // Gabungkan jadi satu string, encodeURIComponent untuk WA
    const message = encodeURIComponent(messageLines.join('\n'));

    // Nomor WA tujuan (ganti sesuai kebutuhan)
    const waNumber = "6289633436359";

    // URL WA
    const waURL = `https://wa.me/${waNumber}?text=${message}`;

    // Cetak nota dulu
    window.print();

    // Delay sebentar supaya print dialog jalan dulu
    setTimeout(() => {
        // Buka WA di tab baru
        window.open(waURL, '_blank');

        // Hapus keranjang via AJAX POST
        fetch('hapus_keranjang.php', {
            method: 'POST'
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const container = document.getElementById('notaContainer');
                container.innerHTML = `
                    <p class="empty-message">Keranjang telah dikosongkan setelah pembayaran.</p>
                    <a href="../index.php" class="back-link">← Kembali ke Beranda</a>
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
    }, 500);
});
</script>
</body>
</html>
