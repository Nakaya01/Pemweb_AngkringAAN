<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_menu = $_POST['id_menu'] ?? '';

    if (!is_numeric($id_menu)) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        exit;
    }

    // Ambil gambar untuk dihapus
    $query = $koneksi->prepare("SELECT gambar FROM menu WHERE id_menu = ?");
    $query->bind_param("i", $id_menu);
    $query->execute();
    $result = $query->get_result();
    $menu = $result->fetch_assoc();

    // Hapus menu
    $delete = $koneksi->prepare("DELETE FROM menu WHERE id_menu = ?");
    $delete->bind_param("i", $id_menu);

    if ($delete->execute()) {
        // Hapus file gambar jika bukan default
        if (!empty($menu['gambar']) && file_exists($menu['gambar']) && $menu['gambar'] !== 'logo/default.png') {
            unlink($menu['gambar']);
        }

        echo json_encode(['status' => 'success', 'message' => 'Menu berhasil dihapus']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus menu']);
    }
}

?>
