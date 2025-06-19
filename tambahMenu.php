<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori = $_POST['kategori'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $harga = $_POST['harga'] ?? '';

    // Validasi kategori
    $kategoriList = ['makanan', 'minuman', 'snack'];
    if (!in_array($kategori, $kategoriList)) {
        echo json_encode(['status' => 'error', 'message' => 'Kategori tidak valid']);
        exit;
    }

    // Proses upload gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $uploadDir = 'Assets/';
        $gambarName = basename($_FILES['gambar']['name']);
        $gambarExt = pathinfo($gambarName, PATHINFO_EXTENSION);
        $gambarSafeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $gambarName);
        $targetPath = $uploadDir . $gambarSafeName;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetPath)) {
            $gambar = $targetPath;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Upload gambar gagal']);
            exit;
        }
    }

    // Validasi data lengkap
    if (empty($kategori) || empty($nama) || empty($harga) || empty($gambar)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    // Simpan ke database
    $query = $koneksi->prepare("INSERT INTO menu (nama, kategori, harga, gambar) VALUES (?, ?, ?, ?)");
    $query->bind_param("ssis", $nama, $kategori, $harga, $gambar);

    if ($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Menu berhasil ditambahkan']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan menu: ' . $query->error]);
    }
}
?>