<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db = "angkringaan_db";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu_id = $_POST['menu_id'] ?? '';
    $jumlah = $_POST['jumlah'] ?? 1;
    $pesanan_id = $_SESSION['pesanan_id'] ?? null;
    
    // If no active pesanan, create one
    if (!$pesanan_id) {
        // Create temporary pelanggan
        $conn->query("INSERT INTO pelanggan (nama, no_meja) VALUES ('Temporary', 0)");
        $temp_pelanggan_id = $conn->insert_id;
        
        // Create temporary pesanan
        $conn->query("INSERT INTO pesanan (id_pelanggan, total_harga, pembayaran) VALUES ($temp_pelanggan_id, 0, 'Cash')");
        $pesanan_id = $conn->insert_id;
        $_SESSION['pesanan_id'] = $pesanan_id;
    }
    
    // Check if item already exists in cart
    $sql = "SELECT id, jumlah FROM detail_pesanan WHERE pesanan_id = ? AND menu_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $pesanan_id, $menu_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing item
        $row = $result->fetch_assoc();
        $new_jumlah = $row['jumlah'] + $jumlah;
        $sql = "UPDATE detail_pesanan SET jumlah = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $new_jumlah, $row['id']);
        $stmt->execute();
    } else {
        // Add new item
        $sql = "INSERT INTO detail_pesanan (pesanan_id, menu_id, jumlah) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $pesanan_id, $menu_id, $jumlah);
        $stmt->execute();
    }
    
    // Clear session cart after saving to database
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
    
    echo json_encode(['status' => 'success', 'message' => 'Item berhasil ditambahkan ke keranjang']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();
?>
