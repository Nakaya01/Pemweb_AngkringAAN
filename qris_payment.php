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
    <title>Pembayaran QRIS - AngkringAan</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Zetta&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Sansita+Swashed&display=swap" rel="stylesheet" />
    <!-- Style -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        .qris-container {
            max-width: 600px;
            margin: 8rem auto 2rem auto;
            padding: 2rem;
            background: rgba(19, 61, 47, 0.1);
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.08);
        }
        
        .qris-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .qris-icon {
            font-size: 3rem;
            color: #FF6B35;
            margin-bottom: 1rem;
        }
        
        .qris-title {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .qris-subtitle {
            color: #61fac4;
            font-size: 1.1rem;
        }
        
        .order-info {
            background: #184c3a;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .order-info h3 {
            color: var(--primary);
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            color: var(--primary);
        }
        
        .qris-code-container {
            background: #184c3a;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .qris-code-container h3 {
            color: var(--primary);
            margin-bottom: 1.5rem;
        }
        
        .qris-code {
            width: 200px;
            height: 200px;
            background: #fff;
            border-radius: 8px;
            margin: 0 auto 1rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: #333;
            border: 2px solid #fbfada33;
        }
        
        .qris-amount {
            font-size: 1.5rem;
            color: #61fac4;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        
        .order-details {
            background: #184c3a;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .order-details h3 {
            color: var(--primary);
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #2a5a4a;
            color: var(--primary);
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-name {
            flex: 1;
        }
        
        .item-qty {
            margin: 0 1rem;
            color: #61fac4;
        }
        
        .item-price {
            color: #61fac4;
            font-weight: bold;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #2a5a4a;
            color: #61fac4;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .instructions {
            background: #184c3a;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .instructions h3 {
            color: var(--primary);
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .instruction-list {
            color: var(--primary);
            padding-left: 1.5rem;
        }
        
        .instruction-list li {
            margin-bottom: 0.5rem;
        }
        
        .supported-apps {
            background: #133d2f;
            border-radius: 6px;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .supported-apps h4 {
            color: var(--primary);
            margin-bottom: 0.5rem;
            text-align: center;
        }
        
        .app-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
            color: #61fac4;
            font-size: 0.9rem;
        }
        
        .btn-container {
            text-align: center;
        }
        
        .btn-home {
            background: #fbfada;
            color: #133d2f;
            border: 1px solid #113327;
            border-radius: 6px;
            padding: 0.8rem 2rem;
            font-family: "Sansita Swashed", cursive;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
            margin-right: 1rem;
        }
        
        .btn-home:hover {
            background: #d2d0a0;
            color: #184c3a;
        }
        
        .btn-success {
            background: #4CAF50;
            color: white;
            border: 1px solid #45a049;
            border-radius: 6px;
            padding: 0.8rem 2rem;
            font-family: "Sansita Swashed", cursive;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-success:hover {
            background: #45a049;
        }
        
        @media (max-width: 768px) {
            .qris-container {
                margin: 6rem 1rem 2rem 1rem;
                padding: 1rem;
            }
            
            .qris-title {
                font-size: 1.5rem;
            }
            
            .order-info, .qris-code-container, .order-details, .instructions {
                padding: 1rem;
            }
            
            .qris-code {
                width: 150px;
                height: 150px;
                font-size: 3rem;
            }
            
            .btn-container {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }
            
            .btn-home, .btn-success {
                margin-right: 0;
            }
        }
        
        @media (max-width: 479px) {
            .navbar .navbar-nav p {
                font-size: 0.9rem !important;
            }
        }
        
        @media print {
            .navbar {
                display: none;
            }
            
            .btn-container {
                display: none;
            }
            
            .qris-container {
                margin: 0;
                padding: 1rem;
                box-shadow: none;
                background: white;
            }
            
            body {
                background: white;
            }
            
            .qris-icon {
                color: #000;
            }
            
            .qris-title {
                color: #000;
            }
            
            .qris-subtitle {
                color: #333;
            }
            
            .order-info, .qris-code-container, .order-details, .instructions {
                background: #f5f5f5;
                border: 1px solid #ddd;
            }
            
            .order-info h3, .qris-code-container h3, .order-details h3, .instructions h3 {
                color: #000;
            }
            
            .info-row, .order-item {
                color: #000;
            }
            
            .item-qty, .item-price, .total-row {
                color: #333;
            }
            
            .qris-code {
                background: #fff;
                border: 2px solid #000;
                color: #000;
            }
            
            .qris-amount {
                color: #000;
            }
            
            .supported-apps {
                background: #e9e9e9;
            }
            
            .app-list {
                color: #333;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="index.php" class="logo">AngkringAan</a>
        <div class="navbar-nav">
            <p>Pembayaran QRIS</p>
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

    <div class="qris-container">
        <div class="qris-header">
            <div class="qris-icon">📱</div>
            <h1 class="qris-title">Pembayaran QRIS</h1>
            <p class="qris-subtitle">Scan QR Code dengan aplikasi e-wallet Anda</p>
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
                <span>Total Pembayaran:</span>
                <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
            </div>
        </div>

        <div class="qris-code-container">
            <h3>QR Code Pembayaran</h3>
            <div class="qris-code">
                QRIS
            </div>
            <div class="qris-amount">
                Rp <?php echo number_format($total, 0, ',', '.'); ?>
            </div>
            <p style="color: var(--primary); font-size: 0.9rem; margin-bottom: 1rem;">
                Scan QR Code di atas dengan aplikasi e-wallet Anda
            </p>
            
            <div class="supported-apps">
                <h4>Aplikasi yang Didukung:</h4>
                <div class="app-list">
                    <span>• GoPay</span>
                    <span>• OVO</span>
                    <span>• DANA</span>
                    <span>• LinkAja</span>
                    <span>• ShopeePay</span>
                    <span>• QRIS</span>
                </div>
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

        <div class="instructions">
            <h3>Cara Pembayaran</h3>
            <ol class="instruction-list">
                <li>Buka aplikasi e-wallet di smartphone Anda</li>
                <li>Pilih menu "Scan QR" atau "Pay QR"</li>
                <li>Arahkan kamera ke QR Code di atas</li>
                <li>Masukkan jumlah pembayaran: <strong>Rp <?php echo number_format($total, 0, ',', '.'); ?></strong></li>
                <li>Konfirmasi pembayaran</li>
                <li>Simpan bukti pembayaran</li>
            </ol>
        </div>

        <div class="btn-container">
            <a href="index.php" class="btn-home">Kembali ke Menu</a>
            <button onclick="window.print()" class="btn-home">Cetak</button>
        </div>
    </div>
</body>
</html> 