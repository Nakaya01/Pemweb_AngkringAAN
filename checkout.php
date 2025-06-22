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
        // 1. Insert or update pelanggan
        $stmt = $conn->prepare("INSERT INTO pelanggan (nama, no_meja) VALUES (?, ?)");
        $stmt->bind_param("si", $nama, $meja);
        $stmt->execute();
        $pelanggan_id = $conn->insert_id;
        
        // 2. Calculate total harga from detail_pesanan
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
        
        // 3. Create new pesanan with proper structure
        $stmt = $conn->prepare("INSERT INTO pesanan (id_pelanggan, total_harga, pembayaran) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $pelanggan_id, $total_harga, $pembayaran);
        $stmt->execute();
        $new_pesanan_id = $conn->insert_id;
        
        // 4. Move detail_pesanan from temporary pesanan to new pesanan
        $stmt = $conn->prepare("UPDATE detail_pesanan SET pesanan_id = ? WHERE pesanan_id = ?");
        $stmt->bind_param("ii", $new_pesanan_id, $pesanan_id);
        $stmt->execute();
        
        // 5. Get temporary pelanggan ID before deleting pesanan
        $stmt = $conn->prepare("SELECT id_pelanggan FROM pesanan WHERE id_pesanan = ?");
        $stmt->bind_param("i", $pesanan_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $temp_pesanan = $result->fetch_assoc();
        $temp_pelanggan_id = $temp_pesanan['id_pelanggan'];
        
        // 6. Delete the temporary pesanan
        $stmt = $conn->prepare("DELETE FROM pesanan WHERE id_pesanan = ?");
        $stmt->bind_param("i", $pesanan_id);
        $stmt->execute();
        
        // 7. Delete the temporary pelanggan if it's a temporary one
        $stmt = $conn->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ? AND nama = 'Temporary'");
        $stmt->bind_param("i", $temp_pelanggan_id);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        // Clear session
        unset($_SESSION['pesanan_id']);
        
        // Redirect to success page or show success message
        header("Location: index.php?success=1");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        die("Error: " . $e->getMessage());
    }
}

$conn->close();
?> 