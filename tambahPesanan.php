<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pelanggan = $_POST['nama'] ?? '';
    $no_meja = $_POST['no_meja'] ?? '';
    $pembayaran = $_POST['pembayaran'] ?? '';
    $items = $_POST['items'] ?? []; // Array: [ ['id_menu' => 1, 'jumlah' => 2], ...]

    if (empty($nama_pelanggan) || empty($no_meja) || empty($pembayaran) || empty($items)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    // Simpan ke tabel pelanggan
    $stmt = $koneksi->prepare("INSERT INTO pelanggan (nama, no_meja) VALUES (?, ?)");
    $stmt->bind_param("si", $nama_pelanggan, $no_meja);
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan pelanggan']);
        exit;
    }

    $id_pelanggan = $stmt->insert_id;

    // Hitung total harga
    $total_harga = 0;
    foreach ($items as $item) {
        $id_menu = $item['id_menu'];
        $jumlah = $item['jumlah'];

        $menu_result = $koneksi->query("SELECT harga FROM menu WHERE id_menu = $id_menu");
        $menu = $menu_result->fetch_assoc();
        $total_harga += $menu['harga'] * $jumlah;
    }

    // Simpan ke tabel pesanan
    $stmt = $koneksi->prepare("INSERT INTO pesanan (id_pelanggan, total_harga, pembayaran) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_pelanggan, $total_harga, $pembayaran);
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan pesanan']);
        exit;
    }

    $id_pesanan = $stmt->insert_id;

    // Simpan detail item pesanan
    $stmt = $koneksi->prepare("INSERT INTO detail_pesanan (pesanan_id, menu_id, jumlah) VALUES (?, ?, ?)");
    foreach ($items as $item) {
        $stmt->bind_param("iii", $id_pesanan, $item['id_menu'], $item['jumlah']);
        $stmt->execute();
    }

    echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil ditambahkan']);
}
?>
