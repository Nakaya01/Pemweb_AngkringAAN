<?php
// Test file untuk memastikan getLaporanPenjualan.php berfungsi
echo "<h2>Test Laporan Penjualan</h2>";

// Test 1: Cek apakah file ada
if (file_exists('getLaporanPenjualan.php')) {
	echo "<p style='color: green;'>✓ File getLaporanPenjualan.php ditemukan</p>";
} else {
	echo "<p style='color: red;'>✗ File getLaporanPenjualan.php tidak ditemukan</p>";
	exit;
}

// Test 2: Cek koneksi database
require 'config.php';
if ($koneksi) {
	echo "<p style='color: green;'>✓ Koneksi database berhasil</p>";
} else {
	echo "<p style='color: red;'>✗ Koneksi database gagal</p>";
	exit;
}

// Test 3: Cek tabel history_pesanan
$query = "SELECT COUNT(*) as total FROM history_pesanan";
$result = $koneksi->query($query);
if ($result) {
	$row = $result->fetch_assoc();
	echo "<p style='color: green;'>✓ Tabel history_pesanan ada, total data: " . $row['total'] . "</p>";
} else {
	echo "<p style='color: red;'>✗ Tabel history_pesanan tidak ada atau error</p>";
}

// Test 4: Cek data sample
$query = "SELECT * FROM history_pesanan LIMIT 3";
$result = $koneksi->query($query);
if ($result && $result->num_rows > 0) {
	echo "<p style='color: green;'>✓ Data sample ditemukan:</p>";
	echo "<ul>";
	while ($row = $result->fetch_assoc()) {
		echo "<li>Pesanan ID: " . $row['pesanan_id'] . ", Status: " . $row['status'] . ", Waktu: " . $row['waktu_selesai'] . "</li>";
	}
	echo "</ul>";
} else {
	echo "<p style='color: red;'>✗ Tidak ada data sample</p>";
}

// Test 5: Simulasi request POST
echo "<h3>Test Request POST</h3>";
echo "<form method='post' action='getLaporanPenjualan.php'>";
echo "<input type='hidden' name='periode' value='hari'>";
echo "<button type='submit'>Test Laporan Hari Ini</button>";
echo "</form>";

// Test 6: Cek response manual
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	echo "<h3>Response dari getLaporanPenjualan.php:</h3>";

	// Simulasi request
	$_POST['periode'] = 'hari';

	// Capture output
	ob_start();
	include 'getLaporanPenjualan.php';
	$output = ob_get_clean();

	echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
	echo htmlspecialchars($output);
	echo "</pre>";

	// Parse JSON
	$data = json_decode($output, true);
	if ($data) {
		echo "<p style='color: green;'>✓ JSON response valid</p>";
		echo "<p>Status: " . $data['status'] . "</p>";
		if (isset($data['data']['ringkasan'])) {
			echo "<p>Total transaksi: " . $data['data']['ringkasan']['total_transaksi'] . "</p>";
			echo "<p>Total pendapatan: Rp " . number_format($data['data']['ringkasan']['total_pendapatan']) . "</p>";
		}
	} else {
		echo "<p style='color: red;'>✗ JSON response tidak valid</p>";
	}
}
?>