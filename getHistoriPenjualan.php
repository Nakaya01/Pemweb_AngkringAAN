<?php
require 'config.php';

function getRiwayatTransaksi($tanggal)
{
	global $koneksi;

	// Format tanggal untuk query
	$tanggal_formatted = date('Y-m-d', strtotime($tanggal));

	// Query untuk mengambil pesanan yang sudah selesai (ada di history_pesanan)
	$query = "
        SELECT 
            p.id_pesanan,
            p.waktu_pesan,
            p.total_harga,
            p.pembayaran,
            pl.nama AS nama_pelanggan,
            pl.no_meja,
            hp.waktu_selesai,
            hp.status,
            k.username AS nama_kasir
        FROM pesanan p
        JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
        JOIN history_pesanan hp ON p.id_pesanan = hp.pesanan_id
        LEFT JOIN kasir k ON hp.kasir_id = k.id_kasir
        WHERE DATE(hp.waktu_selesai) = ?
        ORDER BY hp.waktu_selesai DESC
    ";

	$stmt = $koneksi->prepare($query);
	$stmt->bind_param("s", $tanggal_formatted);
	$stmt->execute();
	$result = $stmt->get_result();

	if (!$result) {
		return [];
	}

	$riwayat_list = [];

	while ($row = $result->fetch_assoc()) {
		$id_pesanan = $row['id_pesanan'];

		// Ambil detail item pesanan
		$detail_query = "
            SELECT 
                m.nama AS nama_menu,
                dp.jumlah,
                m.gambar,
                m.harga
            FROM detail_pesanan dp
            JOIN menu m ON dp.menu_id = m.id_menu
            WHERE dp.pesanan_id = $id_pesanan
        ";

		$detail_result = $koneksi->query($detail_query);
		$items = [];

		while ($detail = $detail_result->fetch_assoc()) {
			$items[] = [
				'nama_menu' => $detail['nama_menu'],
				'jumlah' => $detail['jumlah'],
				'gambar' => $detail['gambar'],
				'harga' => $detail['harga']
			];
		}

		// Gabungkan data pesanan dan item
		$riwayat_list[] = [
			'id' => $row['id_pesanan'],
			'waktu_pesan' => $row['waktu_pesan'],
			'waktu_selesai' => $row['waktu_selesai'],
			'total_harga' => $row['total_harga'],
			'pembayaran' => $row['pembayaran'],
			'nama_pelanggan' => $row['nama_pelanggan'],
			'no_meja' => $row['no_meja'],
			'nama_kasir' => $row['nama_kasir'] ?? 'Tidak diketahui',
			'status' => $row['status'],
			'items' => $items
		];
	}

	return $riwayat_list;
}

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tanggal'])) {
	header('Content-Type: application/json');

	$tanggal = $_POST['tanggal'];
	$riwayat = getRiwayatTransaksi($tanggal);

	// Hitung total pendapatan hanya dari pesanan yang diterima
	$total_pendapatan = 0;
	foreach ($riwayat as $item) {
		if ($item['status'] === 'diterima') {
			$total_pendapatan += $item['total_harga'];
		}
	}

	echo json_encode([
		'status' => 'success',
		'data' => $riwayat,
		'total_transaksi' => count($riwayat),
		'total_pendapatan' => $total_pendapatan
	]);
	exit;
}
?>