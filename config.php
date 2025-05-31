<?php
// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "angkringaan_db");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Set timezone 
date_default_timezone_set('Asia/Makassar'); //gmt +8
define('BASE_URL', 'http://localhost/Pemweb_AngkringAAN/');
?>