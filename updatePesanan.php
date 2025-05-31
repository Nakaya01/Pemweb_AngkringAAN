<?php
require 'config.php';

function getPesananAktif() {
    global $koneksi;
    
    $query = "SELECT 
                p.id,
                pl.nama AS nama_pelanggan,
                pl.no_meja,
                dp.waktu_pesan,
                SUM(m.harga * dp.jumlah) AS total_harga
              FROM pesanan p
              JOIN detail_pesanan dp ON p.id = dp.pesanan_id
              JOIN pelanggan pl ON dp.nama_id = pl.id
              JOIN menu m ON dp.menu_id = m.id
              WHERE p.id NOT IN (SELECT pesanan_id FROM history_pesanan)
              GROUP BY p.id, pl.nama, pl.no_meja, dp.waktu_pesan
              ORDER BY dp.waktu_pesan DESC";
    
    $result = $koneksi->query($query);
    
    $pesanan = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Get items for each order
            $itemsQuery = "SELECT 
                            m.nama AS nama_menu,
                            dp.jumlah,
                            m.harga
                          FROM detail_pesanan dp
                          JOIN menu m ON dp.menu_id = m.id
                          WHERE dp.pesanan_id = {$row['id']}";
            
            $itemsResult = $koneksi->query($itemsQuery);
            $items = [];
            
            while ($item = $itemsResult->fetch_assoc()) {
                $items[] = $item;
            }
            
            $row['items'] = $items;
            $pesanan[] = $row;
        }
    }
    
    return $pesanan;
}
?>