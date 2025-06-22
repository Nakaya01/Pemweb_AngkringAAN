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

// Get parameters from URL
$pesanan_id = $_GET['pesanan_id'] ?? '';
$pembayaran = $_GET['pembayaran'] ?? '';
$total = $_GET['total'] ?? '';
$nama = $_GET['nama'] ?? '';
$meja = $_GET['meja'] ?? '';

// Get order details
$order_details = [];
if ($pesanan_id) {
    $sql = "SELECT m.nama, dp.jumlah, m.harga, (dp.jumlah * m.harga) as subtotal
            FROM detail_pesanan dp
            JOIN menu m ON dp.menu_id = m.id_menu
            WHERE dp.pesanan_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pesanan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $order_details[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran di Kasir - AngkringAan</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Zetta&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Sansita+Swashed&display=swap" rel="stylesheet" />
    <!-- Style -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/stylePayment.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="index.php" class="logo">AngkringAan</a>
        <div class="navbar-nav">
            <p>Pembayaran di Kasir</p>
        </div>
        <div class="navbar-extra">
            <a href="index.php">
                <svg class="feather feather-home" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9,22 9,12 15,12 15,22"></polyline>
                </svg>
            </a>
        </div>
    </nav>

    <div class="success-container">
        <div class="success-header">
            <div class="success-icon">🧾</div>
            <h1 class="success-title">Pembayaran di Kasir</h1>
            <p class="success-subtitle">Silakan tunjukkan halaman ini kepada kasir untuk menyelesaikan pembayaran.</p>
        </div>

        <div class="order-info">
            <h3>Informasi Pesanan</h3>
            <div class="info-row">
                <span>Order ID:</span>
                <span>#<?php echo $pesanan_id; ?></span>
            </div>
            <div class="info-row">
                <span>Nama:</span>
                <span><?php echo htmlspecialchars($nama); ?></span>
            </div>
            <div class="info-row">
                <span>Meja:</span>
                <span><?php echo htmlspecialchars($meja); ?></span>
            </div>
            <div class="info-row">
                <span>Metode Pembayaran:</span>
                <span><?php echo htmlspecialchars($pembayaran); ?></span>
            </div>
        </div>

        <div class="order-details">
            <h3>Detail Pesanan</h3>
            <?php foreach ($order_details as $item): ?>
            <div class="order-item">
                <span class="item-name"><?php echo htmlspecialchars($item['nama']); ?></span>
                <span class="item-qty"><?php echo $item['jumlah']; ?>x</span>
                <span class="item-price">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></span>
            </div>
            <?php endforeach; ?>
            
            <div class="total-row">
                <span>Total Pembayaran:</span>
                <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
            </div>
        </div>

        <div class="btn-container">
            <a href="index.php" class="btn-home">Buat Pesanan Baru</a>
            <button onclick="window.print()" class="btn-home">Cetak Struk</button>
        </div>
    </div>
</body>
</html> 