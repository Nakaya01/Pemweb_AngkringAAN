<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pesanan_id']) && isset($_POST['status'])) {
	$pesanan_id = (int) $_POST['pesanan_id'];
	$status = $_POST['status']; // 'diterima' atau 'dibatalkan'

	// Perbaiki session ID - gunakan id_kasir yang benar
	$kasir_id = $_SESSION['id_kasir'] ?? null;

	// Validasi status
	if (!in_array($status, ['diterima', 'dibatalkan'])) {
		echo json_encode(['status' => 'error', 'message' => 'Status tidak valid']);
		exit;
	}

	// Ambil data pesanan
	$query = "SELECT total_harga FROM pesanan WHERE id_pesanan = ?";
	$stmt = $koneksi->prepare($query);
	$stmt->bind_param("i", $pesanan_id);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows === 0) {
		echo json_encode(['status' => 'error', 'message' => 'Pesanan tidak ditemukan']);
		exit;
	}

	$pesanan = $result->fetch_assoc();

	// Cek apakah pesanan sudah ada di history
	$check_query = "SELECT id FROM history_pesanan WHERE pesanan_id = ?";
	$stmt = $koneksi->prepare($check_query);
	$stmt->bind_param("i", $pesanan_id);
	$stmt->execute();
	$check_result = $stmt->get_result();

	if ($check_result->num_rows > 0) {
		echo json_encode(['status' => 'error', 'message' => 'Pesanan sudah diproses']);
		exit;
	}

	// Masukkan ke history_pesanan dengan status
	$insert_query = "INSERT INTO history_pesanan (pesanan_id, kasir_id, total_harga, status) VALUES (?, ?, ?, ?)";
	$stmt = $koneksi->prepare($insert_query);
	$stmt->bind_param("iiis", $pesanan_id, $kasir_id, $pesanan['total_harga'], $status);

	if ($stmt->execute()) {
		$status_text = $status === 'diterima' ? 'diselesaikan' : 'dibatalkan';
		echo json_encode(['status' => 'success', 'message' => "Pesanan berhasil $status_text"]);
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Gagal memproses pesanan: ' . $stmt->error]);
	}
} else {
	echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>