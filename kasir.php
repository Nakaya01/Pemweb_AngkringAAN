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
  <link href="https://fonts.googleapis.com/css2?family=Lexend+Zetta&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Sansita+Swashed&display=swap" rel="stylesheet" />
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
        <a href="#list-pesanan" id="list"><i data-feather="inbox"></i>List Pemesanan</a>
        <a href="#menambahkan-menu" id="edit"><i data-feather="edit"></i>Tambah Menu</a>
        <a href="#laporan-penjualan" id="chart"><i data-feather="bar-chart-2"></i>Laporan Penjualan</a>
        <a href="#Histori-penjualan" id="archive"><i data-feather="archive"></i>Histori Penjualan</a>
        <a href="log-out.php" id="logout"><i data-feather="log-out"></i>Logout</a>
      </div>
    </div>
    <div class="navbar-user" id="navbar-user">
      <h6>Selamat datang <?php echo htmlspecialchars($nama_kasir); ?></h6>
    </div>
    <form id="search-menu-form" class="search-menu-navbar">
      <input type="text" id="search-menu-input" placeholder="Cari Menu..." />
      <button type="submit"><i data-feather="search"></i></button>
    </form>
  </nav>
  <main class="main-menu">
    <section class="list-pesanan" id="list-pesanan">
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
        <?php if (empty($pesanan)): ?>
          <div class="empty-state">
            <i data-feather="inbox" class="empty-icon"></i>
            <p>Tidak ada pesanan aktif saat ini</p>
          </div>
        <?php else: ?>
          <?php foreach ($pesanan as $order): ?>
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
                  <?php foreach ($order['items'] as $item): ?>
                    <div class="order-item" data-image="<?php echo htmlspecialchars($item['gambar']); ?>"
                      data-price="<?php echo intval($item['harga']); ?>">
                      <span class="item-name"><?php echo htmlspecialchars($item['nama_menu']); ?></span>
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
      <div id="not-found-message" class="empty-state" style="display: none;">
        <i data-feather="alert-triangle" class="empty-icon"></i>
        <p>Pesanan tidak ditemukan</p>
      </div>
    </section>
    <section class="menambahkan-menu section-hidden" id="menambahkan-menu">
      <div class="menu-filter">
        <button class="btn-filter" data-category="makanan">Makanan</button>
        <button class="btn-filter" data-category="minuman">Minuman</button>
        <button class="btn-filter" data-category="snack">Snack</button>
      </div>
      <div id="menu-container" class="menu-container"></div>
      <button id="btn-tambah-menu" class="floating-add-button">
        <i data-feather="plus"></i>
      </button>
      <div id="popup-overlay"></div>
      <div id="popup-tambah-menu" class="popup hidden">
        <form id="form-tambah-menu" class="form-tambah-menu" enctype="multipart/form-data">
          <h3>Tambah Menu Baru</h3>

          <div class="form-group">
            <label for="kategori">Kategori</label>
            <select name="kategori" id="kategori" required>
              <option value="">Pilih Kategori</option>
              <option value="makanan">Makanan</option>
              <option value="minuman">Minuman</option>
              <option value="snack">Snack</option>
            </select>
          </div>
          <div class="form-group">
            <label for="nama">Nama Menu</label>
            <input type="text" name="nama" id="nama" placeholder="Contoh: Nasi Goreng" required>
          </div>
          <div class="form-group">
            <label for="harga">Harga</label>
            <input type="number" name="harga" id="harga" placeholder="cth: 10000" min="0" step="100" required>
          </div>
          <div class="form-group">
            <label for="gambar">Gambar Menu</label>
            <input type="file" name="gambar" id="gambar" accept="image/*" required>
          </div>
          <div class="popup-actions">
            <button type="submit" class="btn-submit">Simpan</button>
            <button type="button" id="btn-cancel" class="btn-cancel">Batal</button>
          </div>
        </form>
      </div>
      <div id="popup-edit-menu" class="popup hidden">
        <form id="form-edit-menu">
          <h3>Edit Menu</h3>
          <div class="image-preview">
            <img id="preview-edit-gambar" src="" alt="Preview Gambar" style="max-height: 120px; margin-top: 10px;">
          </div>
          <div class="form-group">
            <label for="edit-gambar">Gambar Menu</label>
            <input type="file" name="gambar" id="edit-gambar" accept="image/*">
          </div>
          <input type="hidden" name="id_menu" id="edit-id-menu">
          <div class="form-group">
            <label for="edit-nama">Nama Menu</label>
            <input type="text" name="nama" id="edit-nama" placeholder="Nama Menu" required>
          </div>
          <div class="form-group">
            <label for="edit-harga">Harga (contoh: 10000)</label>
            <input type="number" name="harga" id="edit-harga" required>
          </div>
          <div class="popup-actions">
            <button type="submit" class="btn-submit">Update</button>
            <button type="button" id="btn-cancel-edit" class="btn-cancel">Batal</button>
          </div>
        </form>
      </div>
      <div id="popup-konfirmasi-hapus" class="popup confirm-delete hidden">
        <div class="popup-icon">
          <i data-feather="alert-triangle" class="icon-warning"></i>
        </div>
        <h3>Apakah Anda yakin?</h3>
        <p>Menu ini akan dihapus secara permanen.</p>
        <div class="popup-actions">
          <button id="confirm-hapus" class="btn-submit-danger">Ya, Hapus</button>
          <button id="cancel-hapus" class="btn-cancel">Batal</button>
        </div>
      </div>
    </section>
    <section class="laporan-penjualan section-hidden" id="laporan-penjualan">

    </section>
    <section class="Histori-penjualan section-hidden" id="Histori-penjualan">

    </section>
  </main>

  <div class="rincian-pesanan">
    <div class="rincian-header">
      <h2></h2>
      <button class="close-rincian"><i data-feather="x"></i></button>
    </div>
    <div class="rincian-content"></div>
    <button class="btn-selesai">Selesai</button>
    <button class="btn-batal">Batalkan Pesanan</button>
  </div>

  <script>
    feather.replace();
  </script>
</body>

</html>