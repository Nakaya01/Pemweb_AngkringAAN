<?php
session_start();
require 'config.php';
require 'getPesanan.php';

// mengecek apakah user sudah login
if (!isset($_SESSION['kasir_logged_in']) || $_SESSION['kasir_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$nama_kasir = $_SESSION['nama_kasir'] ?? $_SESSION['username'];
$pesanan = getPesananAktif();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KasirAan</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Lexend+Zetta&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Sansita+Swashed&display=swap"
      rel="stylesheet"
    />
    <!--icon-->
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!--style-->
    <link rel="stylesheet" href="css/styleKasir.css" />
    <!-- script -->
    <script src="js/scriptKasir.js" defer></script>
  </head>
  <body>
    <nav class="navbar">
      <div class="navbar-menu">
        <a href="#" id="menu"><i data-feather="list"></i></a>
        <div class="navbar-extra">
          <a href="#" id="list"><i data-feather="inbox"></i>List Pemesanan</a>
          <a href="#" id="edit"><i data-feather="edit"></i>Tambah Menu</a>
          <a href="#" id="chart"><i data-feather="bar-chart-2"></i>Laporan Penjualan</a>
          <a href="#" id="archive"><i data-feather="archive"></i>Histori Penjualan</a>
          <a href="log-out.php" id="logout"><i data-feather="log-out"></i>Keluar</a>
        </div>
      </div>
      <div class="navbar-user">
        <h6>Selamat datang <?php echo htmlspecialchars($nama_kasir); ?></h6>
      </div>
    </nav>
    <main class="main-menu">
      <section class="list-pesanan">
        <div class="pesanan-header">
          <div class="search-box">
            <form action="" method="get">
              <input type="text" id="search-input" placeholder="Cari ID Pesanan..." name="search">
              <button type="submit"><i data-feather="search"></i></button>
            </form>
          </div>
          <div class="pesanan-count">
            <span><?php echo count($pesanan); ?> Pesanan</span>
          </div>
        </div>
        
        <div class="pesanan-container">
          <?php if(empty($pesanan)): ?>
            <div class="empty-state">
              <i data-feather="inbox" class="empty-icon"></i>
              <p>Tidak ada pesanan aktif saat ini</p>
            </div>
          <?php else: ?>
            <?php foreach($pesanan as $order): ?>
              <div class="pesanan-card">
                <div class="card-header">
                  <span class="order-id">#<?php echo $order['id']; ?></span>
                  <span class="order-time"><?php echo date('H:i', strtotime($order['waktu_pesan'])); ?></span>
                </div>
                <div class="card-body">
                  <div class="customer-info">
                    <h3><?php echo htmlspecialchars($order['nama_pelanggan']); ?></h3>
                    <p>Meja <?php echo $order['no_meja']; ?></p>
                  </div>
                  <div class="order-summary">
                    <?php foreach($order['items'] as $item): ?>
                      <div class="order-item">
                        <span class="item-name"><?php echo $item['nama_menu']; ?></span>
                        <span class="item-qty">x<?php echo $item['jumlah']; ?></span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
                <div class="card-footer">
                  <span class="total-price">Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?></span>
                  <button class="btn-rincian" data-order-id="<?php echo $order['id']; ?>">
                    <i data-feather="shopping-bag"></i> Detail
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>
    </main>
    <aside class="rincian-pesanan">
      <div class="rincian-header">
        <h2></h2>
        <button class="close-rincian"><i data-feather="x"></i></button>
      </div>
      <div class="rincian-content">
      </div>
      <button class="btn-selesai">Selesai</button>
      <button class="btn-batal">Batalkan Pesanan</button>
    </aside>
    <script>
      feather.replace();
    </script>
  </body>
</html>
