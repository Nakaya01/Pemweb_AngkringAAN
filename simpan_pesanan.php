<?php
$host = "localhost";
$user = "root";
$password = ""; // Ganti jika ada password
$dbname = "angkringaan"; // Sesuaikan nama database

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Koneksi gagal: ' . $conn->connect_error]));
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['items']) || empty($data['items'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data pesanan kosong']);
    exit;
}

$created_at = date('Y-m-d H:i:s');
$conn->query("INSERT INTO orders (created_at) VALUES ('$created_at')");
$order_id = $conn->insert_id;

foreach ($data['items'] as $item) {
    $name = $conn->real_escape_string($item['name']);
    $quantity = intval($item['quantity']);
    $price = intval(str_replace(['Rp', '.', ','], '', $item['price']));
    $total_price = $price * $quantity;

    // Cek apakah item sudah ada di menu_items
    $result = $conn->query("SELECT id FROM menu_items WHERE name = '$name'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $item_id = $row['id'];
    } else {
        $conn->query("INSERT INTO menu_items (name, price) VALUES ('$name', $price)");
        $item_id = $conn->insert_id;
    }

    // Simpan ke order_items
    $conn->query("INSERT INTO order_items (order_id, item_id, quantity, total_price) 
                  VALUES ($order_id, $item_id, $quantity, $total_price)");
}

echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil disimpan']);
?>
