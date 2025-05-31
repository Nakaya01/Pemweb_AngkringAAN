<?php
function getPesananAktif() {
    global $koneksi;

    // Ambil semua pesanan (bisa ditambahkan filter nanti jika status ditambahkan di tabel)
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
        ORDER BY p.waktu_pesan DESC
    ";

    $result = $koneksi->query($query);

    if (!$result) {
        return [];
    }

    $pesanan_list = [];

    while ($row = $result->fetch_assoc()) {
        $id_pesanan = $row['id_pesanan'];

        // Ambil detail item pesanan
        $detail_query = "
            SELECT 
                m.nama AS nama_menu,
                dp.jumlah
            FROM detail_pesanan dp
            JOIN menu m ON dp.menu_id = m.id_menu
            WHERE dp.pesanan_id = $id_pesanan
        ";

        $detail_result = $koneksi->query($detail_query);
        $items = [];

        while ($detail = $detail_result->fetch_assoc()) {
            $items[] = [
                'nama_menu' => $detail['nama_menu'],
                'jumlah' => $detail['jumlah']
            ];
        }

        // Gabungkan data pesanan dan item
        $pesanan_list[] = [
            'id' => $row['id_pesanan'],
            'waktu_pesan' => $row['waktu_pesan'],
            'total_harga' => $row['total_harga'],
            'pembayaran' => $row['pembayaran'],
            'nama_pelanggan' => $row['nama_pelanggan'],
            'no_meja' => $row['no_meja'],
            'items' => $items
        ];
    }

    return $pesanan_list;
}
?>
