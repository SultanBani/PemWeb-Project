<?php 
// 1. Panggil "roti atas" (header, koneksi, session, dll)
include 'header.php';

// Logika PHP spesifik untuk halaman ini (pencarian dan tambah ke keranjang)
// Logika ini dipindahkan ke sini dari bagian atas file lama.
// --------------------------------------------------------------------------
$keyword = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    if (isset($conn) && $conn) {
        $keyword = trim($conn->real_escape_string($_GET['search']));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_obat_to_cart'])) {
    if (!isset($conn) || !$conn) {
        $_SESSION['notif'] = "Error: Koneksi database tidak tersedia untuk memproses keranjang.";
    } else {
        $id_obat_from_form = intval($_POST['id_obat_to_cart']);

        if ($id_obat_from_form > 0) {
            $sql_get_obat = "SELECT id_obat, nama_obat, harga, stok FROM obat WHERE id_obat = ?";
            $stmt_get_obat = $conn->prepare($sql_get_obat);
            $stmt_get_obat->bind_param("i", $id_obat_from_form);
            $stmt_get_obat->execute();
            $result_obat = $stmt_get_obat->get_result();

            if ($obat = $result_obat->fetch_assoc()) {
                if ($obat['stok'] <= 0) {
                    $_SESSION['notif'] = "Maaf, stok untuk <strong>" . htmlspecialchars($obat['nama_obat']) . "</strong> sedang habis.";
                } else {
                    $id_obat_db = intval($obat['id_obat']);
                    $nama_produk_db = $obat['nama_obat'];
                    $harga_db = floatval($obat['harga']);

                    $sql_cek_keranjang = "SELECT id, jumlah FROM keranjang WHERE id_obat = ?";
                    $stmt_cek = $conn->prepare($sql_cek_keranjang);
                    $stmt_cek->bind_param("i", $id_obat_db);
                    $stmt_cek->execute();
                    $result_cek = $stmt_cek->get_result();

                    if ($item_keranjang = $result_cek->fetch_assoc()) {
                        $jumlah_baru = $item_keranjang['jumlah'] + 1;
                        if ($jumlah_baru > $obat['stok']) {
                            $_SESSION['notif'] = "Jumlah <strong>" . htmlspecialchars($obat['nama_obat']) . "</strong> di keranjang melebihi stok (" . $obat['stok'] . ").";
                        } else {
                            $sql_update = "UPDATE keranjang SET jumlah = ? WHERE id_obat = ?";
                            $stmt_update = $conn->prepare($sql_update);
                            $stmt_update->bind_param("ii", $jumlah_baru, $id_obat_db);
                            $stmt_update->execute();
                            $_SESSION['notif'] = "Jumlah <strong>" . htmlspecialchars($obat['nama_obat']) . "</strong> di keranjang ditambah!";
                        }
                    } else {
                        $sql_insert = "INSERT INTO keranjang (id_obat, nama_produk, harga, jumlah) VALUES (?, ?, ?, 1)";
                        $stmt_insert = $conn->prepare($sql_insert);
                        $stmt_insert->bind_param("isd", $id_obat_db, $nama_produk_db, $harga_db);
                        $stmt_insert->execute();
                        $_SESSION['notif'] = "<strong>" . htmlspecialchars($obat['nama_obat']) . "</strong> berhasil ditambahkan ke keranjang!";
                    }
                }
            } else {
                $_SESSION['notif'] = "Obat tidak ditemukan.";
            }
        }
    }
    // Redirect untuk mencegah resubmit form saat refresh
    header("Location: kelola_obat.php" . ($keyword ? "?search=" . urlencode($keyword) : ""));
    exit;
}
// --------------------------------------------------------------------------
?>

<link rel="stylesheet" href="assets/css/kelola_obat.css" />

<div class="container">
    <h2>Katalog Obat</h2>

    <form method="GET" action="kelola_obat.php" class="form-search">
        <input type="text" name="search" placeholder="Cari nama obat..." value="<?= htmlspecialchars($keyword) ?>" autocomplete="off" />
        <button type="submit">Cari</button>
        <?php if ($keyword): ?>
            <a href="kelola_obat.php" class="btn-reset">Reset</a>
        <?php endif; ?>
    </form>
    
    <a href="pembayaran.php" class="link-keranjang">Lihat Keranjang &rarr;</a>

    <?php if (isset($_SESSION['notif'])): ?>
        <div class="notif-popup <?=(stripos($_SESSION['notif'], 'gagal') !== false || stripos($_SESSION['notif'], 'error') !== false || stripos($_SESSION['notif'], 'habis') !== false) ? 'error' : '' ?>" id="notif-popup">
            <?= $_SESSION['notif'] ?>
        </div>
        <?php unset($_SESSION['notif']); ?>
    <?php endif; ?>

    <div class="katalog-grid">
        <?php
        if (isset($conn) && $conn) {
            $sql_tampil_obat = "SELECT id_obat, nama_obat, harga, stok, gambar, deskripsi FROM obat";
            if ($keyword) {
                $sql_tampil_obat .= " WHERE nama_obat LIKE ?";
            }
            $sql_tampil_obat .= " ORDER BY nama_obat ASC";
            $stmt_tampil_obat = $conn->prepare($sql_tampil_obat);

            if ($stmt_tampil_obat) {
                if ($keyword) {
                    $search_param = "%{$keyword}%";
                    $stmt_tampil_obat->bind_param("s", $search_param);
                }
                $stmt_tampil_obat->execute();
                $result_tampil_obat = $stmt_tampil_obat->get_result();

                if ($result_tampil_obat->num_rows > 0):
                    while ($row = $result_tampil_obat->fetch_assoc()):
                ?>
                <div class="card-obat">
                    <div class="img-container">
                        <img src="assets/img/<?= htmlspecialchars(!empty($row['gambar']) ? $row['gambar'] : 'default-obat.png') ?>" 
                             alt="<?= htmlspecialchars($row['nama_obat']) ?>" 
                             onerror="this.onerror=null;this.src='assets/img/default-obat.png';" 
                             loading="lazy" />
                    </div>
                    <div class="card-obat-konten">
                        <h3><?= htmlspecialchars($row['nama_obat']) ?></h3>
                        <p class="harga">Rp<?= number_format($row['harga'], 0, ',', '.') ?></p>
                        <p class="stok">Stok: <?= ($row['stok'] > 0) ? htmlspecialchars($row['stok']) : '<span style="color:red;">Habis</span>' ?></p>
                        <p class="deskripsi"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></p>
                    </div>
                    <form method="POST" action="kelola_obat.php<?= ($keyword ? "?search=" . urlencode($keyword) : "") ?>" class="form-tambah-keranjang">
                        <input type="hidden" name="id_obat_to_cart" value="<?= $row['id_obat'] ?>" />
                        <button type="submit" class="btn-tambah" <?= ($row['stok'] <= 0) ? 'disabled' : '' ?>>
                            <?= ($row['stok'] <= 0) ? 'Stok Habis' : 'Tambah ke Keranjang' ?>
                        </button>
                    </form>
                </div>
                <?php
                    endwhile;
                else:
                    echo "<p class='empty-message'>Obat dengan kata kunci <strong>\"" . htmlspecialchars($keyword) . "\"</strong> tidak ditemukan.</p>";
                endif;
                $stmt_tampil_obat->close();
            }
        } else {
            echo "<p class='empty-message'>Koneksi database tidak tersedia untuk menampilkan katalog.</p>";
        }
        ?>
    </div>

    <div class="back-home-container">
        <a href="index.php" class="btn-back-home">&larr; Kembali ke Beranda</a>
    </div>
</div>

<script>
// Script untuk notifikasi popup agar hilang otomatis
const notif = document.getElementById('notif-popup');
if (notif) {
    // Hilang setelah 3.5 detik (animasi keluar 0.5s + 3s tunggu)
    setTimeout(() => {
        if(notif) notif.style.display = 'none';
    }, 3500);
}
</script>
<script src="assets/header.js"></script>
<?php 
include 'footer.php'; 
?>