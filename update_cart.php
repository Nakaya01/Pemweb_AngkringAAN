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

// Function to get order items HTML
function getOrderItemsHTML($conn, $pesanan_id) {
    $sql = "SELECT dp.*, m.nama, m.harga, m.gambar 
            FROM detail_pesanan dp
            JOIN menu m ON dp.menu_id = m.id_menu
            WHERE dp.pesanan_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pesanan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $html = '';
    if ($result->num_rows > 0) {
        while ($item = $result->fetch_assoc()) {
            $image_path = $item['gambar'];
            $image_exists = file_exists($image_path);
            
            $html .= '<div class="order-item">';
            $html .= '<img src="' . htmlspecialchars($image_path) . '" 
                           alt="' . htmlspecialchars($item['nama']) . '" 
                           class="order-item-img"
                           onerror="this.src=\'Assets/default-menu.png\'; this.onerror=null;"
                           style="' . (!$image_exists ? 'border: 2px dashed #ccc;' : '') . '" />';
            $html .= '<div class="order-item-info">';
            $html .= '<div class="order-item-name">' . htmlspecialchars($item['nama']) . '</div>';
            $html .= '<div class="order-item-price">Rp ' . number_format($item['harga'], 0, ',', '.') . '</div>';
            $html .= '</div>';
            $html .= '<div class="order-item-actions">';
            $html .= '<form method="post" action="update_cart.php" style="display:inline;">';
            $html .= '<input type="hidden" name="item_id" value="' . $item['id'] . '">';
            $html .= '<button class="btn-kurang btn-decrease" type="submit" name="action" value="decrease">-</button>';
            $html .= '</form>';
            $html .= '<input type="number" class="order-item-qty quantity" value="' . $item['jumlah'] . '" min="1" readonly />';
            $html .= '<form method="post" action="update_cart.php" style="display:inline;">';
            $html .= '<input type="hidden" name="item_id" value="' . $item['id'] . '">';
            $html .= '<button class="btn-tambah btn-increase" type="submit" name="action" value="increase">+</button>';
            $html .= '</form>';
            $html .= '<form method="post" action="update_cart.php" style="display:inline;">';
            $html .= '<input type="hidden" name="item_id" value="' . $item['id'] . '">';
            $html .= '<button class="btn-hapus" type="submit" name="action" value="delete"><i data-feather="trash-2"></i></button>';
            $html .= '</form>';
            $html .= '</div>';
            $html .= '</div>';
        }
    } else {
        $html = '<p>Keranjang kosong.</p>';
    }
    
    return $html;
}

// Function to calculate total price
function getTotalPrice($conn, $pesanan_id) {
    $sql = "SELECT SUM(dp.jumlah * m.harga) as total 
            FROM detail_pesanan dp
            JOIN menu m ON dp.menu_id = m.id_menu
            WHERE dp.pesanan_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pesanan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

// Function to get item count
function getItemCount($conn, $pesanan_id) {
    $sql = "SELECT COUNT(*) as count FROM detail_pesanan WHERE pesanan_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pesanan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] ?? 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'] ?? '';
    $action = $_POST['action'] ?? '';
    $pesanan_id = $_SESSION['pesanan_id'] ?? '';
    
    // Check if this is an AJAX request
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    if (empty($item_id) || empty($action) || empty($pesanan_id)) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
        } else {
            header("Location: chart.php?error=invalid_request");
        }
        exit();
    }
    
    $success = false;
    $message = '';
    
    switch ($action) {
        case 'increase':
            // Increase quantity
            $sql = "UPDATE detail_pesanan SET jumlah = jumlah + 1 WHERE id = ? AND pesanan_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $item_id, $pesanan_id);
            $success = $stmt->execute();
            $message = 'Jumlah berhasil ditambah';
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
                $success = $stmt->execute();
                $message = 'Jumlah berhasil dikurangi';
            } else {
                // Delete item if quantity becomes 0
                $sql = "DELETE FROM detail_pesanan WHERE id = ? AND pesanan_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $item_id, $pesanan_id);
                $success = $stmt->execute();
                $message = 'Item berhasil dihapus';
            }
            break;
            
        case 'delete':
            // Delete item completely
            $sql = "DELETE FROM detail_pesanan WHERE id = ? AND pesanan_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $item_id, $pesanan_id);
            $success = $stmt->execute();
            $message = 'Item berhasil dihapus';
            break;
            
        default:
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
            } else {
                header("Location: chart.php?error=invalid_action");
            }
            exit();
    }
    
    if ($isAjax) {
        // Return JSON response for AJAX requests
        header('Content-Type: application/json');
        
        if ($success) {
            $itemCount = getItemCount($conn, $pesanan_id);
            $totalHarga = getTotalPrice($conn, $pesanan_id);
            $orderItems = getOrderItemsHTML($conn, $pesanan_id);
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'itemCount' => $itemCount,
                'totalHarga' => $totalHarga,
                'orderItems' => $orderItems
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal memperbarui keranjang'
            ]);
        }
    } else {
        // Handle regular form submissions (fallback)
        if ($success) {
            $itemCount = getItemCount($conn, $pesanan_id);
            if ($itemCount == 0) {
                header("Location: chart.php?success=updated");
            } else {
                header("Location: chart.php?success=updated");
            }
        } else {
            header("Location: chart.php?error=update_failed");
        }
    }
    exit();
}

$conn->close();
?> 