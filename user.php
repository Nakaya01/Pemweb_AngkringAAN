<?php
$koneksi = new mysqli("localhost", "root", "", "AngkringAan_db");

if ($koneksi->connect_error) {
  die("Koneksi gagal: " . $koneksi->connect_error);
}

// Daftar user yang ingin dimasukkan
$users = [
  ["aan gagah", "078"],
  ["iqbal imut", "080"],
  ["andra asix", "036"]
];

foreach ($users as $user) {
  $username = $user[0];
  $hashedPassword = password_hash($user[1], PASSWORD_DEFAULT);

  $stmt = $koneksi->prepare("INSERT INTO kasir (username, password) VALUES (?, ?)");
  $stmt->bind_param("ss", $username, $hashedPassword);
  $stmt->execute();
}

echo "User berhasil ditambahkan dengan password yang di-hash.";
$koneksi->close();
?>
