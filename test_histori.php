<?php
// Simulasi POST request untuk getHistoriPenjualan.php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['tanggal'] = '2025-01-15'; // Tanggal yang ada di database

// Capture output
ob_start();
include 'getHistoriPenjualan.php';
$output = ob_get_clean();

echo "=== TEST HISTORI PENJUALAN ===\n";
echo "Tanggal yang diuji: 2025-01-15\n";
echo "Output:\n";
echo $output;

// Parse JSON
$data = json_decode($output, true);
if ($data) {
	echo "\n=== PARSED DATA ===\n";
	echo "Status: " . $data['status'] . "\n";
	echo "Total transaksi: " . $data['total_transaksi'] . "\n";
	echo "Total pendapatan: " . $data['total_pendapatan'] . "\n";

	if (isset($data['data']) && is_array($data['data'])) {
		echo "Jumlah data riwayat: " . count($data['data']) . "\n";
		foreach ($data['data'] as $index => $item) {
			echo "Data " . ($index + 1) . ":\n";
			echo "  - ID: " . $item['id'] . "\n";
			echo "  - Pelanggan: " . $item['nama_pelanggan'] . "\n";
			echo "  - Total: " . $item['total_harga'] . "\n";
			echo "  - Status: " . $item['status'] . "\n";
		}
	}
} else {
	echo "\nError: Tidak dapat parse JSON\n";
}
?>