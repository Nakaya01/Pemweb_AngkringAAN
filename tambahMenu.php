<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori = $_POST['kategori'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $harga = $_POST['harga'] ?? '';

    // Proses upload gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $uploadDir = 'Assets/';
        $gambarName = basename($_FILES['gambar']['name']);
        $targetPath = $uploadDir . $gambarName;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetPath)) {
            $gambar = $targetPath;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Upload gambar gagal']);
            exit;
        }
    }

    if (empty($kategori) || empty($nama) || empty($harga) || empty($gambar)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    $query = $koneksi->prepare("INSERT INTO menu (kategori, nama, harga, gambar) VALUES (?, ?, ?, ?)");
    $query->bind_param("ssis", $kategori, $nama, $harga, $gambar);

    if ($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Menu berhasil ditambahkan']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan menu']);
    }
}
?>
