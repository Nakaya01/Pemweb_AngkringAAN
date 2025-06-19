CREATE DATABASE IF NOT EXISTS angkringaan_db;
USE angkringaan_db;

-- Tabel kasir
CREATE TABLE IF NOT EXISTS kasir (
  id_kasir INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

INSERT INTO kasir (username, password) VALUES 
('aan gagah', '078'), 
('iqbal gooners', '080'), 
('andra asix', '036');

-- Tabel menu
CREATE TABLE IF NOT EXISTS menu (
  id_menu INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  kategori ENUM('makanan', 'minuman', 'snack') NOT NULL,
  harga INT NOT NULL,
  gambar VARCHAR(255)
);

INSERT INTO menu (nama, kategori, harga, gambar) VALUES
('Mie Goreng', 'makanan', 8000, 'Assets/miegoreng.png'),
('Gado-Gado', 'makanan', 10000, 'Assets/gado.png'),
('Bakso', 'makanan', 12000, 'Assets/bakso.png'),
('Nasi Goreng', 'makanan', 13000, 'Assets/nasgor.png'),
('Soto Ayam', 'makanan', 15000, 'Assets/soto.png'),
('Ayam Bakar', 'makanan', 20000, 'Assets/ayam bakar.png'),
('Rendang', 'makanan', 22000, 'Assets/rendang.png'),
('Air Mineral', 'minuman', 3000, 'Assets/mineral.png'),
('Es Teh', 'minuman', 5000, 'Assets/esteh.png'),
('Jus Alpukat', 'minuman', 6000, 'Assets/alpukat.png'),
('Kopi Hitam', 'minuman', 7000, 'Assets/kopi.png'),
('Jus Jeruk', 'minuman', 8000, 'Assets/jeruk.png'),
('Susu Coklat', 'minuman', 9000, 'Assets/susu_coklat.png'),
('Teh Tarik', 'minuman', 10000, 'Assets/teh tarik.png'),
('Soda Gembira', 'minuman', 11000, 'Assets/soda gembira.png'),
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

-- Tabel pelanggan
CREATE TABLE IF NOT EXISTS pelanggan (
  id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  no_meja INT NOT NULL
);

INSERT INTO pelanggan (nama, no_meja) VALUES
('jariz imout', 1),
('Budi wan', 2),
('Citra Lestari', 3),
('Dewi Anggraeni', 4),
('Eko Prasetyo', 5),
('Fira Nurul', 6),
('Guntur Wibowo', 7),
('Hana Putri', 8);

-- Tabel pesanan
CREATE TABLE IF NOT EXISTS pesanan (
  id_pesanan INT AUTO_INCREMENT PRIMARY KEY,
  id_pelanggan INT NOT NULL,
  waktu_pesan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  total_harga INT NOT NULL,
  pembayaran ENUM('Cash', 'Qris', 'Transfer') NOT NULL,
  FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan) ON DELETE CASCADE
);
-- INSERT INTO pesanan (id_pelanggan, total_harga, pembayaran) VALUES (1, 31000, 'Cash'), ...
-- Insert data ke tabel pesanan
INSERT INTO pesanan (id_pelanggan, total_harga, pembayaran) VALUES
(1, 31000, 'Cash'),     -- Pesanan pelanggan id 1 dengan total harga 31.000
(2, 8000, 'Qris'),
(3, 32000, 'Transfer'),
(4, 22000, 'Cash'),
(5, 30000, 'Qris'),
(6, 33000, 'Transfer'),
(7, 26000, 'Cash'),
(8, 38000, 'Qris');

-- Tabel detail pesanan
CREATE TABLE IF NOT EXISTS detail_pesanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pesanan_id INT NOT NULL,
  menu_id INT NOT NULL,
  jumlah INT NOT NULL,
  FOREIGN KEY (pesanan_id) REFERENCES pesanan(id_pesanan) ON DELETE CASCADE,
  FOREIGN KEY (menu_id) REFERENCES menu(id_menu) ON DELETE CASCADE
);

-- INSERT INTO detail_pesanan (pesanan_id, menu_id, jumlah) VALUES (1, 4, 2), (1, 9, 1);
INSERT INTO detail_pesanan (pesanan_id, menu_id, jumlah) VALUES
(1, 4, 2),   -- Pesanan 1 pesan 2 porsi menu dengan id 4
(1, 9, 1),   -- Pesanan 1 pesan 1 porsi menu id 9

(2, 1, 1),   -- Pesanan 2 pesan 1 porsi menu id 1

(3, 3, 2),   -- Pesanan 3 pesan 2 porsi menu id 3
(3, 12, 1),  -- Pesanan 3 pesan 1 porsi menu id 12

(4, 5, 1),
(4, 18, 1),

(5, 6, 1),
(5, 14, 1),

(6, 7, 1),
(6, 21, 1),

(7, 22, 1),
(7, 15, 1),

(8, 25, 2),
(8, 10, 1);

-- Tabel history pesanan
CREATE TABLE IF NOT EXISTS history_pesanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pesanan_id INT NOT NULL,
  kasir_id INT,
  waktu_selesai TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  total_harga INT NOT NULL,
  FOREIGN KEY (pesanan_id) REFERENCES pesanan(id_pesanan) ON DELETE CASCADE,
  FOREIGN KEY (kasir_id) REFERENCES kasir(id_kasir) ON DELETE SET NULL
);
