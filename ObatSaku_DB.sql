
-- Database: obatsaku

CREATE DATABASE IF NOT EXISTS obatsaku;
USE obatsaku;

-- Tabel: obat
CREATE TABLE obat (
    id_obat INT AUTO_INCREMENT PRIMARY KEY,
    nama_obat VARCHAR(100) NOT NULL,
    jenis_obat VARCHAR(50),
    deskripsi TEXT,
    indikasi TEXT,
    dosis VARCHAR(100),
    efek_samping TEXT,
    harga DECIMAL(10,2),
    stok INT,
    gambar VARCHAR(255),
    tanggal_kedaluwarsa DATE
);

-- Tabel: pengguna
CREATE TABLE pengguna (
    username VARCHAR(100) PRIMARY KEY,
    nama_depan VARCHAR(100),
    nama_belakang VARCHAR(100),
    email VARCHAR(255) UNIQUE,
    no_hp VARCHAR(15),
    password VARCHAR(255),
    foto_profil VARCHAR(255),
    status_akun ENUM('Aktif','Tidak Aktif') DEFAULT 'Aktif',
    tipe_pengguna ENUM('Admin','Manajer','Pengguna') DEFAULT 'Pengguna'
);

-- Tabel: pesanan
CREATE TABLE pesanan (
    id_pesanan INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    status_pesanan ENUM('Menunggu','Dikirim','Diterima') DEFAULT 'Menunggu',
    tipe_pesanan ENUM('Resep','Umum'),
    jumlah INT,
    nama_penerima VARCHAR(255),
    alamat_jalan VARCHAR(100),
    kota VARCHAR(100),
    kode_pos VARCHAR(10),
    url_resep VARCHAR(255),
    id_obat INT,
    total_harga DECIMAL(10,2),
    tanggal_pesanan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (username) REFERENCES pengguna(username),
    FOREIGN KEY (id_obat) REFERENCES obat(id_obat)
);

-- Tabel: pembayaran
CREATE TABLE pembayaran (
    id_pembayaran INT AUTO_INCREMENT PRIMARY KEY,
    id_pesanan INT,
    jumlah DECIMAL(10,2),
    bank VARCHAR(50),
    catatan TEXT,
    tanggal_bayar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    bukti_pembayaran VARCHAR(255),
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan)
);

-- Tabel: pesan
CREATE TABLE pesan (
    id_pesan INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    nama VARCHAR(100),
    isi_pesan TEXT NOT NULL,
    no_kontak VARCHAR(15),
    email VARCHAR(255),
    url_upload VARCHAR(255),
    tanggapan TEXT,
    tanggal_pesan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (username) REFERENCES pengguna(username)
);

CREATE TABLE keranjang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(255),
    harga INT,
    jumlah INT
);

INSERT INTO keranjang (nama_produk, harga, jumlah) VALUES
('Paracetamol', 5000, 2),
('Vitamin C', 10000, 1);

-- Data Dummy untuk tabel obat
INSERT INTO obat (nama_obat, jenis_obat, deskripsi, indikasi, dosis, efek_samping, harga, stok, gambar, tanggal_kedaluwarsa) VALUES
('Paracetamol', 'Tablet', 'Obat untuk menurunkan demam dan meredakan nyeri.', 'Demam, sakit kepala', '1 tablet, 3 kali sehari', 'Mual, ruam kulit', 5000.00, 100, 'paracetamol.jpg', '2025-12-31'),
('Amoxicillin', 'Kapsul', 'Antibiotik untuk mengatasi infeksi bakteri.', 'Infeksi saluran pernapasan', '1 kapsul, 3 kali sehari selama 7 hari', 'Diare, mual', 10000.00, 50, 'amoxicillin.jpg', '2025-10-01'),
('Vitamin C', 'Tablet', 'Vitamin untuk meningkatkan daya tahan tubuh.', 'Kekurangan vitamin C', '1 tablet sehari', 'Gangguan lambung (jika dosis tinggi)', 3000.00, 80, 'vitaminc.jpg', '2026-01-15');

-- Data Dummy untuk tabel pengguna
INSERT INTO pengguna (username, nama_depan, nama_belakang, email, no_hp, password, foto_profil, status_akun, tipe_pengguna) VALUES
('admin', 'Sultan', 'Bani', 'admin@obatsaku.com', '08123456789', 'admin123', 'admin.jpg', 'Aktif', 'Admin'),
('user01', 'Budi', 'Santoso', 'budi@gmail.com', '08234567890', 'user123', 'budi.jpg', 'Aktif', 'Pengguna');

-- Data Dummy untuk tabel pesan
INSERT INTO pesan (username, nama, isi_pesan, no_kontak, email, url_upload, tanggapan) VALUES
('user01', 'Budi Santoso', 'Apakah Paracetamol tersedia?', '08234567890', 'budi@gmail.com', NULL, 'Ya, tersedia.'),
('user01', 'Budi Santoso', 'Bagaimana aturan pakai Amoxicillin?', '08234567890', 'budi@gmail.com', NULL, 'Dikonsumsi 3 kali sehari.');

