<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_menu = $_POST['id_menu'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $harga = $_POST['harga'] ?? '';

    if (!is_numeric($id_menu) || empty($nama) || empty($harga)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
        exit;
    }

    // Ambil gambar lama
    $query = $koneksi->prepare("SELECT gambar FROM menu WHERE id_menu = ?");
    $query->bind_param("i", $id_menu);
    $query->execute();
    $result = $query->get_result();
    $oldData = $result->fetch_assoc();
    $gambar = $oldData['gambar'];

    // Proses gambar baru jika ada
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $uploadDir = 'Assets/';
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $uniqueName = 'menu_' . time() . '.' . $ext;
        $targetPath = $uploadDir . $uniqueName;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetPath)) {
            // Hapus gambar lama jika bukan default
            if (!empty($gambar) && file_exists($gambar) && $gambar !== 'logo/default.png') {
                unlink($gambar);
            }
            $gambar = $targetPath;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Upload gambar gagal']);
            exit;
        }
    }

    // Update menu
    $query = $koneksi->prepare("UPDATE menu SET nama = ?, harga = ?, gambar = ? WHERE id_menu = ?");
    $query->bind_param("sisi", $nama, $harga, $gambar, $id_menu);

    if ($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Menu berhasil diperbarui']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengedit menu']);
    }
}
?>