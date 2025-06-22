<?php
require 'config.php';

function getPesananAktif()
{
    global $koneksi;

    // Query yang benar sesuai dengan struktur database
    $query = "
        SELECT 
            p.id_pesanan,
            p.waktu_pesan,
            p.total_harga,
            p.pembayaran,
            pl.nama AS nama_pelanggan,
            pl.no_meja
        FROM pesanan p
        JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
        LEFT JOIN history_pesanan hp ON p.id_pesanan = hp.pesanan_id
        WHERE hp.pesanan_id IS NULL
        ORDER BY p.waktu_pesan DESC
    ";

    $result = $koneksi->query($query);

    $pesanan = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id_pesanan = $row['id_pesanan'];

            // Ambil detail item pesanan
            $itemsQuery = "
                SELECT 
                    m.nama AS nama_menu,
                    dp.jumlah,
                    m.gambar,
                    m.harga
                FROM detail_pesanan dp
                JOIN menu m ON dp.menu_id = m.id_menu
                WHERE dp.pesanan_id = $id_pesanan
            ";

            $itemsResult = $koneksi->query($itemsQuery);
            $items = [];

            while ($item = $itemsResult->fetch_assoc()) {
                $items[] = [
                    'nama_menu' => $item['nama_menu'],
                    'jumlah' => $item['jumlah'],
                    'gambar' => $item['gambar'],
                    'harga' => $item['harga']
                ];
            }

            $row['items'] = $items;
            $pesanan[] = $row;
        }
    }

    return $pesanan;
}
?>