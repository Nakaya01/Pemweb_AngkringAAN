<?php
session_start();
// Include file config menyambungkan ke db
require_once 'config.php';

// ambil data dari form login
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Validasi jika username dan password kosong
if (empty($username)) {
    header("Location: login.php?error=Username harus diisi");
    exit;
}

if (empty($password)) {
    header("Location: login.php?error=Password harus diisi");
    exit;
}

// Ambil data user dari database
$sql = "SELECT * FROM kasir WHERE username = ?";
$stmt = $koneksi->prepare($sql);

if (!$stmt) {
    header("Location: login.php?error=Terjadi kesalahan sistem");
    exit;
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Untuk keamanan, sebaiknya gunakan password_verify() jika password di-hash
    if ($password === $user['password']) {
        $_SESSION['kasir_logged_in'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_kasir'] = $user['nama_kasir'];
        $_SESSION['user_id'] = $user['id']; // Tambahkan ID user jika diperlukan
        
        // Regenerate session ID untuk mencegah session fixation
        session_regenerate_id(true);
        
        header("Location: kasir.php");
        exit();
    }
}

header("Location: login.php?error=Username atau password salah!");
exit;
?>