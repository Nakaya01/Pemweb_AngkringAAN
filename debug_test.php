<?php
require 'config.php';

echo "=== DEBUG TEST ===\n";
echo "Database connected: " . ($koneksi ? "YES" : "NO") . "\n";

// Test query history_pesanan
$query = "SELECT COUNT(*) as total FROM history_pesanan";
$result = $koneksi->query($query);
if ($result) {
	$row = $result->fetch_assoc();
	echo "Total history_pesanan: " . $row['total'] . "\n";
} else {
	echo "Error querying history_pesanan: " . $koneksi->error . "\n";
}

// Test query dengan tanggal hari ini
$today = date('Y-m-d');
echo "Today's date: " . $today . "\n";

$query = "SELECT COUNT(*) as total FROM history_pesanan WHERE DATE(waktu_selesai) = '$today'";
$result = $koneksi->query($query);
if ($result) {
	$row = $result->fetch_assoc();
	echo "Today's transactions: " . $row['total'] . "\n";
} else {
	echo "Error querying today's transactions: " . $koneksi->error . "\n";
}

// Test sample data
$query = "SELECT * FROM history_pesanan LIMIT 3";
$result = $koneksi->query($query);
if ($result && $result->num_rows > 0) {
	echo "Sample data:\n";
	while ($row = $result->fetch_assoc()) {
		echo "- ID: " . $row['pesanan_id'] . ", Status: " . $row['status'] . ", Date: " . $row['waktu_selesai'] . "\n";
	}
} else {
	echo "No sample data found\n";
}

echo "=== END DEBUG ===\n";
?>