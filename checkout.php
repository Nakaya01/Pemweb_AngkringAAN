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
    $nama = $_POST['nama'] ?? '';
    $meja = $_POST['meja'] ?? '';
    $pembayaran = $_POST['pembayaran'] ?? '';
    $pesanan_id = $_POST['pesanan_id'] ?? '';
    
    // Validate input
    if (empty($nama) || empty($meja) || empty($pembayaran)) {
        die("Semua field harus diisi!");
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // 1. Create customer record
        $stmt = $conn->prepare("INSERT INTO pelanggan (nama, no_meja) VALUES (?, ?)");
        $stmt->bind_param("si", $nama, $meja);
        $stmt->execute();
        $pelanggan_id = $conn->insert_id;
        
        // 2. Calculate total from cart items
        $total_harga = 0;
        $sql = "SELECT dp.jumlah, m.harga 
                FROM detail_pesanan dp
                JOIN menu m ON dp.menu_id = m.id_menu
                WHERE dp.pesanan_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $pesanan_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $total_harga += $row['jumlah'] * $row['harga'];
        }
        
        // 3. Create final order
        $stmt = $conn->prepare("INSERT INTO pesanan (id_pelanggan, total_harga, pembayaran) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $pelanggan_id, $total_harga, $pembayaran);
        $stmt->execute();
        $new_pesanan_id = $conn->insert_id;
        
        // 4. Move cart items to final order
        $stmt = $conn->prepare("UPDATE detail_pesanan SET pesanan_id = ? WHERE pesanan_id = ?");
        $stmt->bind_param("ii", $new_pesanan_id, $pesanan_id);
        $stmt->execute();
        
        // 5. Clean up temporary data
        $stmt = $conn->prepare("SELECT id_pelanggan FROM pesanan WHERE id_pesanan = ?");
        $stmt->bind_param("i", $pesanan_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $temp_pesanan = $result->fetch_assoc();
        $temp_pelanggan_id = $temp_pesanan['id_pelanggan'];
        
        $stmt = $conn->prepare("DELETE FROM pesanan WHERE id_pesanan = ?");
        $stmt->bind_param("i", $pesanan_id);
        $stmt->execute();
        
        $stmt = $conn->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ? AND nama = 'Temporary'");
        $stmt->bind_param("i", $temp_pelanggan_id);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        // Clear session
        unset($_SESSION['pesanan_id']);
        
        // Route to appropriate payment page
        $redirect_params = "pesanan_id=$new_pesanan_id&pembayaran=$pembayaran&total=$total_harga&nama=$nama&meja=$meja";
        
        switch ($pembayaran) {
            case 'Cash':
                header("Location: payment_success.php?$redirect_params");
                break;
            case 'Transfer':
                header("Location: transfer_payment.php?$redirect_params");
                break;
            case 'Qris':
                header("Location: qris_payment.php?$redirect_params");
                break;
            default:
                header("Location: index.php?success=1");
        }
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        die("Error: " . $e->getMessage());
    }
}

$conn->close();
?> 