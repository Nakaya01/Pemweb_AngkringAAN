<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db = "AngkringAan_db";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil pesanan aktif user (belum checkout, pembayaran NULL)
$user_id = $_SESSION['user_id'] ?? 1; // Ganti sesuai implementasi login
// Untuk demo, asumsikan satu pesanan aktif per user (bisa pakai session)
$pesanan_id = $_SESSION['pesanan_id'] ?? null;
if (!$pesanan_id) {
    // Cari pesanan aktif (pembayaran NULL)
    $pesanan = $conn->query("SELECT * FROM pesanan WHERE pembayaran IS NULL ORDER BY id DESC LIMIT 1")->fetch_assoc();
    if ($pesanan) {
        $pesanan_id = $pesanan['id'];
        $_SESSION['pesanan_id'] = $pesanan_id;
    }
}
if (!$pesanan_id) {
    // Buat pesanan baru
    $conn->query("INSERT INTO pesanan (no_meja, pembayaran) VALUES (0, NULL)");
    $pesanan_id = $conn->insert_id;
    $_SESSION['pesanan_id'] = $pesanan_id;
    $pesanan = $conn->query("SELECT * FROM pesanan WHERE id=$pesanan_id")->fetch_assoc();
} else {
    $pesanan = $conn->query("SELECT * FROM pesanan WHERE id=$pesanan_id")->fetch_assoc();
}

// Ambil item makanan di keranjang
$order_items = [];
$total_harga = 0;
if ($pesanan_id) {
    $sql = "SELECT dp.*, m.nama, m.harga, m.gambar 
            FROM detail_pesanan dp
            JOIN menu m ON dp.menu_id = m.id
            WHERE dp.pesanan_id = $pesanan_id";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $order_items[] = $row;
        $total_harga += $row['harga'] * $row['jumlah'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AngkringAan - Keranjang</title>
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Zetta&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Sansita+Swashed&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/chartscript.js" defer></script>
</head>
<body>
    <!-- menu navbar -->
    <nav class="navbar">
        <a href="index.php" class="logo">AngkringAan</a>
        <div class="navbar-nav">
            <p>Silahkan Dicek Lagi Yah Kak!</p>
        </div>
        <div class="navbar-extra">
            <a href="#" id="search"><i data-feather="shopping-cart"></i></a>
            <a href="login.php" id="kasir"><i data-feather="user"></i></a>
        </div>
    </nav>

    <!-- Konten Keranjang -->
    <main class="chart-container">
        <!-- Class 1: Daftar barang dipesan -->
        <div class="order-list" id="order-list">
            <h2>Pesanan Anda</h2>
            <?php if (count($order_items) > 0): ?>
                <?php foreach ($order_items as $item): ?>
                <div class="order-item">
                    <img src="img/<?= htmlspecialchars($item['gambar']) ?>" alt="<?= htmlspecialchars($item['nama']) ?>" class="order-item-img" />
                    <div class="order-item-info">
                        <div class="order-item-name"><?= htmlspecialchars($item['nama']) ?></div>
                        <div class="order-item-price">Rp <?= number_format($item['harga'], 0, ',', '.') ?></div>
                    </div>
                    <div class="order-item-actions">
                        <form method="post" action="update_cart.php" style="display:inline;">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <button class="btn-kurang btn-decrease" type="submit" name="action" value="decrease">-</button>
                        </form>
                        <input type="number" class="order-item-qty quantity" value="<?= $item['jumlah'] ?>" min="1" readonly />
                        <form method="post" action="update_cart.php" style="display:inline;">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <button class="btn-tambah btn-increase" type="submit" name="action" value="increase">+</button>
                        </form>
                        <form method="post" action="update_cart.php" style="display:inline;">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <button class="btn-hapus" type="submit" name="action" value="delete"><i data-feather="trash-2"></i></button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Keranjang kosong.</p>
            <?php endif; ?>
        </div>

        <!-- Class 2: Form nama dan nomor meja -->
        <div class="order-form">
            <h2>Data Pemesan</h2>
            <form method="post" action="checkout.php">
            <table>
                <tbody>
                    <tr>
                        <td><label for="nama">Nama:</label></td>
                        <td><input type="text" id="nama" name="nama" required value="<?= htmlspecialchars($pesanan['nama_pemesan'] ?? '') ?>" /></td>
                    </tr>
                    <tr>
                        <td><label for="meja">Nomor Meja:</label></td>
                        <td><input type="number" id="meja" name="meja" min="1" required value="<?= htmlspecialchars($pesanan['no_meja'] ?? '') ?>" /></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Class 3: Total dan metode pembayaran -->
        <div class="order-summary">
            <h2>Ringkasan</h2>
            <table>
                <tbody>
                    <tr>
                        <td>Total Harga:</td>
                        <td><span id="total-harga">Rp <?= number_format($total_harga, 0, ',', '.') ?></span></td>
                    </tr>
                    <tr>
                        <td><label for="pembayaran">Metode Pembayaran:</label></td>
                        <td>
                            <select id="pembayaran" name="pembayaran">
                                <option value="Cash">Cash</option>
                                <option value="Qris">QRIS</option>
                                <option value="Transfer">Transfer</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button id="btn-checkout" type="submit">Checkout</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="pesanan_id" value="<?= $pesanan_id ?>">
            </form>
        </div>
    </main>

    <script>
        feather.replace();
    </script>
</body>
</html>
<?php $conn->close(); ?>
