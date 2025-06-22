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
    $item_id = $_POST['item_id'] ?? '';
    $action = $_POST['action'] ?? '';
    $pesanan_id = $_SESSION['pesanan_id'] ?? '';
    
    if (empty($item_id) || empty($action) || empty($pesanan_id)) {
        header("Location: chart.php?error=invalid_request");
        exit();
    }
    
    switch ($action) {
        case 'increase':
            // Increase quantity
            $sql = "UPDATE detail_pesanan SET jumlah = jumlah + 1 WHERE id = ? AND pesanan_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $item_id, $pesanan_id);
            $stmt->execute();
            break;
            
        case 'decrease':
            // Decrease quantity, delete if becomes 0
            $sql = "SELECT jumlah FROM detail_pesanan WHERE id = ? AND pesanan_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $item_id, $pesanan_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row && $row['jumlah'] > 1) {
                $sql = "UPDATE detail_pesanan SET jumlah = jumlah - 1 WHERE id = ? AND pesanan_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $item_id, $pesanan_id);
                $stmt->execute();
            } else {
                // Delete item if quantity becomes 0
                $sql = "DELETE FROM detail_pesanan WHERE id = ? AND pesanan_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $item_id, $pesanan_id);
                $stmt->execute();
            }
            break;
            
        case 'delete':
            // Delete item completely
            $sql = "DELETE FROM detail_pesanan WHERE id = ? AND pesanan_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $item_id, $pesanan_id);
            $stmt->execute();
            break;
            
        default:
            header("Location: chart.php?error=invalid_action");
            exit();
    }
    
    // Check if cart is empty and redirect accordingly
    $sql = "SELECT COUNT(*) as count FROM detail_pesanan WHERE pesanan_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pesanan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        // Cart is empty, redirect to index
        header("Location: index.php?message=cart_cleared");
    } else {
        // Redirect back to chart
        header("Location: chart.php?success=updated");
    }
    exit();
}

$conn->close();
?> 