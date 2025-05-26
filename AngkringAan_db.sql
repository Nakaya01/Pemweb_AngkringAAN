CREATE DATABASE IF NOT EXISTS AngkringAan_db;
USE AngkringAan_db;

-- Tabel kasir
CREATE TABLE IF NOT EXISTS kasir (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

insert into kasir (username, password) values ('aan gagah', '078'), ('iqbal gooners', '080'), ('andra asix', '036');

-- Tabel menu
CREATE TABLE IF NOT EXISTS menu (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  kategori ENUM('makanan', 'minuman', 'snack') NOT NULL,
  harga INT NOT NULL,
  gambar VARCHAR(255)
);

INSERT INTO `menu` (`id`, `nama`, `kategori`, `harga`, `gambar`) VALUES (NULL, 'Mie Goreng', 'makanan', '8000', NULL), (NULL, 'Gado-Gado', 'makanan', '10000', NULL), (NULL, 'Bakso', 'makanan', '12000', NULL), (NULL, 'Nasi Goreng', 'makanan', '13000', NULL), (NULL, 'Soto Ayam', 'makanan', '15000', NULL), (NULL, 'Ayam Bakar', 'makanan', '20000', NULL), (NULL, 'Rendang', 'makanan', '22000', NULL), (NULL, '[value-2]', '', '0', '[value-5]');

-- Tabel pesanan
CREATE TABLE IF NOT EXISTS pesanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_pemesan VARCHAR(100),
  no_meja INT NOT NULL,
  pembayaran ENUM('Cash', 'Qris', 'Transfer') NOT NULL,
  waktu_pesan TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel detail pesanan
CREATE TABLE IF NOT EXISTS detail_pesanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pesanan_id INT NOT NULL,
  menu_id INT NOT NULL,
  jumlah INT NOT NULL,
  FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
  FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE
);

-- Tabel history pesanan
CREATE TABLE IF NOT EXISTS history_pesanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pesanan_id INT NOT NULL,
  kasir_id INT NOT NULL,
  waktu_selesai TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  total_harga INT NOT NULL,
  FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
  FOREIGN KEY (kasir_id) REFERENCES kasir(id) ON DELETE SET NULL
);
