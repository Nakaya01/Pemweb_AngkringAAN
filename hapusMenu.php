<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_menu = $_POST['id_menu'] ?? '';

    if (!is_numeric($id_menu)) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        exit;
    }

    $query = $koneksi->prepare("DELETE FROM menu WHERE id_menu = ?");
    $query->bind_param("i", $id_menu);

    if ($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Menu berhasil dihapus']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus menu']);
    }
}
?>
