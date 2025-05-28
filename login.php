<?php
session_start();

// Redirect jika sudah login (opsional)
if (isset($_SESSION['kasir_logged_in']) && $_SESSION['kasir_logged_in'] === true) {
    header("Location: kasir.html"); // Ganti ke halaman setelah login
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KasirAan</title>
    <!-- icon -->
    <script src="https://unpkg.com/feather-icons"></script>
    <!-- style -->
    <link rel="stylesheet" href="css/styleLogin.css" />
    <!--font-->
    <link
      href="https://fonts.googleapis.com/css2?family=Lexend+Zetta&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Sansita+Swashed&display=swap"
      rel="stylesheet"
    />
    <script src="js/scriptLogin.js" defer></script>
  </head>
  <body>
    <nav class="navbar">
      <a href="index.php" id="index" class="navbar-logo">
        <i data-feather="arrow-left"></i>
        <span class="back-text">Kembali</span>
      </a>
    </nav>
    <section class="login">
      <div class="container">
        <div class="login-form">
          <h1>Log in</h1>
          <form action="prosesLogin.php" method="POST">
            <p>Username</p>
            <input
              type="text"
              name="username"
              placeholder="Username"
              required
            />
            <p>Password</p>
            <input
              type="password"
              name="password"
              placeholder="Password"
              required
            />
            <button type="submit">Login</button>
          </form>
        </div>
      </div>
    </section>
    <div id="popup" class="popup hidden">
      <div class="popup-content">
        <span class="popup-icon"><i data-feather="check"></i></span>
        <h3 id="popup-title"></h3>
        <p id="popup-message"></p>
        <button id="popup-button" class="popup-button"></button>
      </div>
    </div>
    <script>
      feather.replace();
    </script>
  </body>
</html>
