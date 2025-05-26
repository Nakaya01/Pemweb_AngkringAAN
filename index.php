<?php
session_start();

// Proses simpan data pesanan ke session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart'])) {
    $cartData = json_decode($_POST['cart'], true);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    foreach ($cartData as $item) {
        $name = $item['name'];
        $category = $item['category'];
        $qty = (int)$item['quantity'];

        if ($qty > 0) {
            $_SESSION['cart'][] = [
                'name' => $name,
                'category' => $category,
                'quantity' => $qty
            ];
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Pesanan disimpan di session.']);
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
    </script>
  </body>
</html>
