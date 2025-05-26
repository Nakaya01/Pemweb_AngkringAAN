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

INSERT INTO menu (nama, kategori, harga, gambar) VALUES
-- Makanan
('Mie Goreng', 'makanan', 8000, 'Assets/miegoreng.png'),
('Gado-Gado', 'makanan', 10000, 'Assets/gado.png'),
('Bakso', 'makanan', 12000, 'Assets/bakso.png'),
('Nasi Goreng', 'makanan', 13000, 'Assets/nasgor.png'),
('Soto Ayam', 'makanan', 15000, 'Assets/soto.png'),
('Ayam Bakar', 'makanan', 20000, 'Assets/ayam bakar.png'),
('Rendang', 'makanan', 22000, 'Assets/rendang.png'),

-- Minuman
('Air Mineral', 'minuman', 3000, 'Assets/mineral.png'),
('Es Teh', 'minuman', 5000, 'Assets/esteh.png'),
('Jus Alpukat', 'minuman', 6000, 'Assets/alpukat.png'),
('Kopi Hitam', 'minuman', 7000, 'Assets/kopi.png'),
('Jus Jeruk', 'minuman', 8000, 'Assets/jeruk.png'),
('Susu Coklat', 'minuman', 9000, 'Assets/susu_coklat.png'),
('Teh Tarik', 'minuman', 10000, 'Assets/teh tarik.png'),
('Soda Gembira', 'minuman', 11000, 'Assets/soda gembira.png'),

-- Snack
('Bakwan', 'snack', 5000, 'Assets/bakwan.png'),
('Risoles', 'snack', 7000, 'Assets/risol.png'),
('Tahu Crispy', 'snack', 8000, 'Assets/tahu_crispyB.png'),
('Pisang Goreng', 'snack', 9000, 'Assets/pisgor2.png'),
('Cireng', 'snack', 10000, 'Assets/cireng.png'),
('Singkong Keju', 'snack', 11000, 'Assets/singkong_keju.png'),
('Lumpia', 'snack', 12000, 'Assets/lumpia.png'),
('Kentang Goreng', 'snack', 15000, 'Assets/kentang.png'),
('Sate Taichan', 'snack', 16000, 'Assets/taichan.png'),
('Sate Ayam', 'snack', 16000, 'Assets/sate top.png');

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
