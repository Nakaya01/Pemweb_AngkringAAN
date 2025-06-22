<?php
session_start();

// Proses simpan data pesanan ke session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart'])) {
    header('Content-Type: application/json');
    
    // Validasi input
    $cartData = json_decode($_POST['cart'], true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($cartData)) {
        echo json_encode(['status' => 'error', 'message' => 'Data keranjang tidak valid']);
        exit;
    }

    // Inisialisasi cart jika belum ada
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    foreach ($cartData as $item) {
        // Validasi item
        if (!isset($item['id']) || !isset($item['name']) || !isset($item['category']) || 
            !isset($item['quantity']) || !isset($item['price_value']) || !isset($item['image'])) {
            continue;
        }

        $id = (int)$item['id'];
        $name = trim($item['name']);
        $category = trim($item['category']);
        $qty = (int)$item['quantity'];
        $price = (int)$item['price_value'];
        $image = trim($item['image']);

        // Cari item yang sudah ada di cart
        $itemExists = false;
        foreach ($_SESSION['cart'] as &$cartItem) {
            if ($cartItem['id'] === $id) {
                $cartItem['quantity'] += $qty;
                $itemExists = true;
                break;
            }
        }

        // Tambahkan item baru jika belum ada dan quantity > 0
        if (!$itemExists && $qty > 0) {
            $_SESSION['cart'][] = [
                'id' => $id,
                'name' => $name,
                'category' => $category,
                'quantity' => $qty,
                'price_value' => $price,
                'price_formatted' => 'Rp ' . number_format($price, 0, ',', '.'),
                'image' => $image,
                'added_at' => time() // Timestamp untuk sorting
            ];
        }
    }

    echo json_encode([
        'status' => 'success', 
        'message' => 'Pesanan disimpan di session.',
        'cart_count' => count($_SESSION['cart'])
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AngkringAan</title>
    <!--font--->
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
    <!--style-->
    <link rel="stylesheet" href="css/style.css" />
    <!-- script -->
    <script src="js/script.js" defer></script>
  </head>
  <body>
    <!-- menu navbar -->
    <nav class="navbar">
      <a href="#" class="logo">AngkringAan</a>

      <div class="navbar-nav">
        <a href="#makanan" class="Makanan-nav">Makanan</a>
        <a href="#minuman" class="Minuman-nav">Minuman</a>
        <a href="#snack" class="Snack-nav">Snack</a>
      </div>

      <div class="navbar-extra">
        <a href="chart.php" id="chart"><i data-feather="shopping-cart"></i></a>
        <a href="login.php" id="kasir"><i data-feather="user"></i></a>
      </div>
    </nav>

    <section class="makanan" id="makanan">
      <main class="makanan-main">
        <h1>Makanan</h1>
        <div class="row" id="makanan-list"></div>
      </main>
    </section>

    <section class="minuman" id="minuman">
      <main class="minuman-main">
        <h1>Minuman</h1>
        <div class="row" id="minuman-list"></div>
      </main>
    </section>

    <section class="snack" id="snack">
      <main class="snack-main">
        <h1>Snack</h1>
        <div class="row" id="snack-list"></div>
      </main>
    </section>

    <footer id="footer">
      <div class="footer-links">
        <a href="#privacy">Privacy Policy</a>
        <a href="#terms">Terms of Service</a>
      </div>
      <div class="social-media">
        <a href="#facebook" class="fb"><i data-feather="facebook"></i></a>
        <a href="https://www.instagram.com/tongkrong_aan/?__pwa=1" class="ig"
          ><i data-feather="instagram"></i
        ></a>
        <a href="#phone" class="wa"><i data-feather="phone"></i></a>
      </div>
      <p>&copy; 2025 TongkrongAan. All rights reserved.</p>
    </footer>
    <div id="popup" class="popup hidden">
      <div class="popup-content">
        <span class="popup-icon"><i data-feather="check"></i></span>
        <h3 id="popup-title"></h3>
        <p id="popup-message"></p>
        <button id="popup-button" class="popup-button">Close</button>
      </div>
    </div>
    <script>
      feather.replace();

      // Fungsi untuk menampilkan popup
      function showPopup(title, message) {
        document.getElementById('popup-title').textContent = title;
        document.getElementById('popup-message').textContent = message;
        document.getElementById('popup').classList.remove('hidden');
      }

      // Tutup popup saat tombol diklik
      document.getElementById('popup-button').addEventListener('click', function() {
        document.getElementById('popup').classList.add('hidden');
      });

      // Contoh event handler untuk tombol "Tambah ke Keranjang"
      // Pastikan tombol "Tambah ke Keranjang" memanggil fungsi ini
      function addToCart(item) {
        // item: {id, name, category, quantity, price_value, image}
        fetch('', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'cart=' + encodeURIComponent(JSON.stringify([item]))
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            showPopup('Berhasil!', 'Item berhasil ditambahkan ke keranjang.');
            // Update tampilan cart count jika perlu
            // document.getElementById('cart-count').textContent = data.cart_count;
          } else {
            showPopup('Gagal', data.message || 'Terjadi kesalahan.');
          }
        })
        .catch(() => {
          showPopup('Gagal', 'Tidak dapat terhubung ke server.');
        });
      }

      // Contoh: panggil addToCart dari tombol produk
      // document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
      //   btn.addEventListener('click', function() {
      //     const item = {...}; // Ambil data item dari atribut/data-*
      //     addToCart(item);
      //   });
      // });
    </script>
  </body>
</html>
