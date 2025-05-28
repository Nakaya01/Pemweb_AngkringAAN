<?php
function getPesananAktif() {
    global $koneksi;
    
    $query = "SELECT id, kode_pesanan, nama_pelanggan, no_meja, total_harga, status, 
              DATE_FORMAT(tanggal_pesanan, '%d/%m/%Y %H:%i') as tanggal_pesanan 
              FROM pesanan 
              WHERE status != 'selesai' AND status != 'batal'
              ORDER BY tanggal_pesanan DESC";
    
    $result = $koneksi->query($query);
    
    if (!$result) {
        return [];
    }
    
    return $result->fetch_all(MYSQLI_ASSOC);
}