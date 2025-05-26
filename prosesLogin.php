<?php
session_start();
$koneksi = new mysqli("localhost", "root", "", "angkringaan_db");

if ($koneksi->connect_error) {
  die("Koneksi gagal: " . $koneksi->connect_error);
}

// ambil data dari form login
$username = $_POST['username'];
$password = $_POST['password'];

// Validasi jika username dan password kosong
if (empty($username) || empty($password)) {
    header("Location: login.php?error=Username dan password harus diisi");
    exit;
}

// Ambil data user dari database
$sql = "SELECT * FROM kasir WHERE username = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $user = $result->fetch_assoc();
  if ($password === $user['password']) {
    $_SESSION['kasir_logged_in'] = true;
    $_SESSION['username'] = $user['username'];
    $_SESSION['nama_kasir'] = $user['nama_kasir'];
    header("Location: kasir.php");
    exit();
  }
}

header("Location: login.php?error=Username atau password salah!");
exit;
?>