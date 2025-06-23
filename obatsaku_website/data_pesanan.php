<?php  
include 'header.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "db/koneksi.php";

$pesan = '';
if (isset($_GET['id_hapus'])) {
    $id = intval($_GET['id_hapus']);

    $conn->query("DELETE FROM pembayaran WHERE id_pesanan = $id");

    $hapusSql = "DELETE FROM pesanan WHERE id_pesanan = $id";
    if ($conn->query($hapusSql)) {
        $pesan = "Pesanan dengan ID $id berhasil dihapus.";
    } else {
        $pesan = "Gagal menghapus pesanan dengan ID $id.";
    }
}
?>

<meta charset="UTF-8">
<title>Data Pesanan</title>

<style>
    table.dataTable tbody td a {
        color: #3b82f6;
        text-decoration: underline;
    }
    table.dataTable tbody td a:hover {
        color: #2563eb;
    }
    table.dataTable thead th {
        padding: 10px 18px !important;
        text-align: left !important;
    }
    #tabelPesanan th, #tabelPesanan td {
        text-align: center !important;
    }
    main {
        padding-top: 55px; 
    }
</style>

<main>
<h1 class="text-3xl font-bold text-center mb-6 text-gray-700">DATA PESANAN</h1>

<!-- Toast Notifikasi -->
<?php if (!empty($pesan)): ?>
   <div id="popupNotif" class="fixed top-[70px] right-6 z-[9999] px-5 py-3 rounded-lg shadow-md text-white font-medium
    <?= str_contains($pesan, 'berhasil') ? 'bg-green-500' : 'bg-red-500' ?>">
        <?= $pesan ?>
    </div>
<?php endif; ?>

<div class="overflow-x-auto bg-white shadow-md rounded-lg p-6">
    <table id="tabelPesanan" class="min-w-full border border-gray-300 text-sm">
        <thead class="bg-gray-200 text-gray-600 uppercase">
            <tr>
                <th>ID Pesanan</th>
                <th>Username</th>
                <th>Status Pesanan</th>
                <th>Tipe Pesanan</th>
                <th>Jumlah</th>
                <th>Total Harga</th>
                <th>Tanggal Pesanan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white text-gray-700">
        <?php
        if ($conn) {
            $sql = "SELECT id_pesanan, username, status_pesanan, tipe_pesanan, jumlah, nama_penerima, total_harga, tanggal_pesanan FROM pesanan ORDER BY tanggal_pesanan DESC";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='px-4 py-3 border-b border-gray-300'>" . htmlspecialchars($row["id_pesanan"]) . "</td>";
                    echo "<td class='px-4 py-3 border-b border-gray-300'>" . htmlspecialchars($row["username"]) . "</td>";
                    echo "<td class='px-4 py-3 border-b border-gray-300'>" . htmlspecialchars($row["status_pesanan"]) . "</td>";
                    echo "<td class='px-4 py-3 border-b border-gray-300'>" . htmlspecialchars($row["tipe_pesanan"]) . "</td>";
                    echo "<td class='px-4 py-3 border-b border-gray-300 text-center'>" . htmlspecialchars($row["jumlah"]) . "</td>";
                    echo "<td class='px-4 py-3 border-b border-gray-300 text-right'>" . number_format($row["total_harga"], 2, ',', '.') . "</td>";
                    echo "<td class='px-4 py-3 border-b border-gray-300'>" . date("d-m-Y H:i:s", strtotime($row["tanggal_pesanan"])) . "</td>";
                    echo "<td class='px-4 py-3 border-b border-gray-300 text-center'>";
                    echo "<button onclick=\"confirmDelete(" . $row["id_pesanan"] . ")\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700 transition-colors duration-150 text-xs'>Hapus</button>";
                    echo "</td>";
                    echo "</tr>";
                }
            }
        }
        ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#tabelPesanan').DataTable({
            pagingType: 'simple_numbers',
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(disaring dari _MAX_ total entri)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                },
                zeroRecords: "Tidak ada data yang cocok ditemukan"
            },
            order: [[7, 'desc']]
        });
    });

    function confirmDelete(idPesanan) {
        if (confirm("Apakah Anda yakin ingin menghapus pesanan dengan ID " + idPesanan + "?")) {
            window.location.href = "data_pesanan.php?id_hapus=" + idPesanan;
        }
    }

    // Auto-hide toast notification
    setTimeout(() => {
        const popup = document.getElementById('popupNotif');
        if (popup) popup.style.display = 'none';
    }, 4000);
</script>
<script src="assets/header.js"></script>
</main>
</body>
</html>

<?php include 'footer.php'; ?>
