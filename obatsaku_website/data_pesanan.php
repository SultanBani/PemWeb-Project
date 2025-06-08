<?php  include 'header.php';

// Memeriksa dan memulai sesi hanya jika belum aktif
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['username'] = 'admin'; // Dummy

include "db/koneksi.php"; // Pastikan path ini benar dan $conn terdefinisi di file koneksi.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Data Pesanan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" href="assets/css/data_pesanan.css">
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
  </style>
</head>
<body class="px-8 py-10 bg-gray-50">

  <h1 class="text-3xl font-bold text-center mb-10 text-gray-700">DATA PESANAN</h1>

  <!-- Tombol kembali ke beranda -->
  <div class="mb-6">
    <a href="index.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition duration-200">
      ← Kembali ke Beranda
    </a>
  </div>

  <div class="overflow-x-auto bg-white shadow-md rounded-lg p-6">
    <table id="tabelPesanan" class="min-w-full border border-gray-300 text-sm">
      <thead class="bg-gray-200 text-gray-600 uppercase">
        <tr>
          <th>ID Pesanan</th>
          <th>Username</th>
          <th>Status Pesanan</th>
          <th>Tipe Pesanan</th>
          <th>Jumlah</th>
          <th>Nama Penerima</th>
          <th>Total Harga</th>
          <th>Tanggal Pesanan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody class="bg-white text-gray-700">
          <?php
          // Tampilkan pesan error koneksi di luar tabel jika diperlukan
          if (!isset($conn) || !$conn) {
              // echo "<p class='text-red-500'>Error koneksi database.</p>";
          } else {
              $sql = "SELECT id_pesanan, username, status_pesanan, tipe_pesanan, jumlah, nama_penerima, alamat_jalan, kota, kode_pos, url_resep, id_obat, total_harga, tanggal_pesanan FROM pesanan ORDER BY tanggal_pesanan DESC";
              $result = $conn->query($sql);

              // Hanya jalankan loop jika ada data, jika tidak, biarkan tbody kosong
              if ($result && $result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td class='px-4 py-3 border-b border-gray-300'>" . htmlspecialchars($row["id_pesanan"]) . "</td>";
                      echo "<td class='px-4 py-3 border-b border-gray-300'>" . htmlspecialchars($row["username"]) . "</td>";
                      echo "<td class='px-4 py-3 border-b border-gray-300'>" . htmlspecialchars($row["status_pesanan"]) . "</td>";
                      echo "<td class='px-4 py-3 border-b border-gray-300'>" . htmlspecialchars($row["tipe_pesanan"]) . "</td>";
                      echo "<td class='px-4 py-3 border-b border-gray-300 text-center'>" . htmlspecialchars($row["jumlah"]) . "</td>";
                      echo "<td class='px-4 py-3 border-b border-gray-300'>" . htmlspecialchars($row["nama_penerima"]) . "</td>";
                      echo "<td class='px-4 py-3 border-b border-gray-300 text-right'>" . number_format($row["total_harga"], 2, ',', '.') . "</td>";
                      echo "<td class='px-4 py-3 border-b border-gray-300'>" . date("d-m-Y H:i:s", strtotime($row["tanggal_pesanan"])) . "</td>";
                      echo "<td class='px-4 py-3 border-b border-gray-300 text-center'>";
                      echo "<button onclick=\"confirmDelete(" . $row["id_pesanan"] . ")\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700 transition-colors duration-150 text-xs'>Hapus</button>";
                      echo "</td>";
                      echo "</tr>";
                  }
              }
              // Tidak ada lagi blok "else" atau "else if" di sini
          }
          ?>
      </tbody>
    </table>
  </div>

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
        order: [[12, 'desc']]
      });
    });

    function confirmDelete(idPesanan) {
        if (confirm("Apakah Anda yakin ingin menghapus pesanan dengan ID " + idPesanan + "?")) {
            alert("Fitur hapus untuk ID " + idPesanan + " belum diimplementasikan.");
            // Implementasi nyata bisa seperti:
            // window.location.href = '../proses/hapus_pesanan.php?id=' + idPesanan;
        }
    }
  </script>
  <script src="assets/js/header.js"></script>

</body>
</html>
<?php 
include 'footer.php'; 
?>
