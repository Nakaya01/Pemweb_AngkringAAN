<?php
require 'config.php';
header('Content-Type: application/json');

$conn = $koneksi;

$kategori = $_GET['kategori'] ?? '';
$search = $_GET['search'] ?? '';

// Normalisasi pencarian
$search = strtolower($search);
$searchQuery = !empty($search) ? "%" . $search . "%" : null;

// Jika pencarian saja (tanpa kategori)
if (!empty($search) && empty($kategori)) {
    $query = $conn->prepare("SELECT * FROM menu WHERE LOWER(nama) LIKE ?");
    $query->bind_param("s", $searchQuery);
}
// Jika pencarian dan kategori ada
else if (!empty($search) && !empty($kategori)) {
    $query = $conn->prepare("SELECT * FROM menu WHERE kategori = ? AND LOWER(nama) LIKE ?");
    $query->bind_param("ss", $kategori, $searchQuery);
}
// Jika hanya kategori saja
else if (!empty($kategori)) {
    $query = $conn->prepare("SELECT * FROM menu WHERE kategori = ?");
    $query->bind_param("s", $kategori);
}
// Jika tidak ada parameter
else {
    echo json_encode([]);
    exit;
}

$query->execute();
$result = $query->get_result();

$menuList = [];
while ($row = $result->fetch_assoc()) {
    if (empty($row['gambar'])) {
        $row['gambar'] = 'logo/default.png';
    }
    $menuList[] = $row;
}

echo json_encode($menuList);
?>