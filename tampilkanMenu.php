<?php
header('Content-Type: application/json');
require 'config.php';

if ($koneksi->connect_error) {
  die("Koneksi gagal: " . $koneksi->connect_error);
}

$sql = "SELECT id_menu, nama, kategori, harga, gambar FROM menu";
$result = $koneksi->query($sql);

$menu = [
    'makanan' => [],
    'minuman' => [],
    'snack' => []
];

while ($row = $result->fetch_assoc()) {
    $menu[$row['kategori']][] = [
        'id' => $row['id_menu'],
        'name' => $row['nama'],
        'price' => 'Rp ' . number_format($row['harga'], 0, ',', '.'), //buat format harga
        'price_value' => $row['harga'], //buat nilai harga tanpa format untuk di totalkan nanti
        'image' => $row['gambar']
    ];
}

echo json_encode($menu);
$koneksi->close();

?>