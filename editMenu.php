<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_menu = $_POST['id_menu'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $harga = $_POST['harga'] ?? '';
    $gambar = $_POST['gambar'] ?? '';

    if (!is_numeric($id_menu) || empty($nama) || empty($harga)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
        exit;
    }

    $query = $koneksi->prepare("UPDATE menu SET nama = ?, harga = ?, gambar = ? WHERE id_menu = ?");
    $query->bind_param("sisi", $nama, $harga, $gambar, $id_menu);

    if ($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Menu berhasil diperbarui']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengedit menu']);
    }
}
?>
