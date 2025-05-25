<?php
session_start();
$koneksi = new mysqli("localhost", "root", "", "AngkringAan_db");

if ($koneksi->connect_error) {
  die("Koneksi gagal: " . $koneksi->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];

// Ambil data user dari database
$sql = "SELECT * FROM kasir WHERE username = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $user = $result->fetch_assoc();
  if (password_verify($password, $user['password'])) {
    $_SESSION['username'] = $username;
    header("Location: kasir.html");
    exit();
  }
}

echo "<script>
  alert('Username atau password salah!');
  window.location.href = 'login.html';
</script>";
?>
