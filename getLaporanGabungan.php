<?php
require 'config.php';

function getLaporanGabungan($periode, $tanggal_mulai = null, $tanggal_akhir = null)
{
	global $koneksi;

	// Tentukan rentang tanggal berdasarkan periode
	switch ($periode) {
		case 'hari':
			$tanggal_mulai = date('Y-m-d');
			$tanggal_akhir = date('Y-m-d');
			break;
		case 'minggu':
			$tanggal_mulai = date('Y-m-d', strtotime('monday this week'));
			$tanggal_akhir = date('Y-m-d', strtotime('sunday this week'));
			break;
		case 'bulan':
			$tanggal_mulai = date('Y-m-01');
			$tanggal_akhir = date('Y-m-t');
			break;
		case 'custom':
			// Validasi input
			if (!$tanggal_mulai || !$tanggal_akhir) {
				throw new Exception('Tanggal mulai dan akhir harus diisi untuk periode kustom.');
			}
			break;
		default:
			$tanggal_mulai = date('Y-m-d');
			$tanggal_akhir = date('Y-m-d');
	}

	// Query untuk data ringkasan (hanya pesanan yang diterima)
	$summary_query = "
        SELECT 
            COUNT(DISTINCT p.id_pesanan) as total_transaksi,
            SUM(p.total_harga) as total_pendapatan,
            AVG(p.total_harga) as rata_transaksi,
            SUM(dp.jumlah) as total_menu_terjual
        FROM pesanan p
        JOIN history_pesanan hp ON p.id_pesanan = hp.pesanan_id
        JOIN detail_pesanan dp ON p.id_pesanan = dp.pesanan_id
        WHERE DATE(hp.waktu_selesai) BETWEEN ? AND ?
        AND hp.status = 'diterima'
    ";

	$stmt = $koneksi->prepare($summary_query);
	$stmt->bind_param("ss", $tanggal_mulai, $tanggal_akhir);
	$stmt->execute();
	$summary_result = $stmt->get_result();
	$summary = $summary_result->fetch_assoc();

	// Query untuk menu terlaris (hanya pesanan yang diterima)
	$menu_query = "
        SELECT 
            m.nama as nama_menu,
            SUM(dp.jumlah) as total_terjual
        FROM detail_pesanan dp
        JOIN menu m ON dp.menu_id = m.id_menu
        JOIN pesanan p ON dp.pesanan_id = p.id_pesanan
        JOIN history_pesanan hp ON p.id_pesanan = hp.pesanan_id
        WHERE DATE(hp.waktu_selesai) BETWEEN ? AND ?
        AND hp.status = 'diterima'
        GROUP BY m.id_menu, m.nama
        ORDER BY total_terjual DESC
        LIMIT 10
    ";

	$stmt = $koneksi->prepare($menu_query);
	$stmt->bind_param("ss", $tanggal_mulai, $tanggal_akhir);
	$stmt->execute();
	$menu_result = $stmt->get_result();

	$menu_terlaris = [];
	while ($row = $menu_result->fetch_assoc()) {
		$menu_terlaris[] = $row;
	}

	// Query untuk metode pembayaran (hanya pesanan yang diterima)
	$payment_query = "
        SELECT 
            p.pembayaran,
            COUNT(*) as jumlah_transaksi
        FROM pesanan p
        JOIN history_pesanan hp ON p.id_pesanan = hp.pesanan_id
        WHERE DATE(hp.waktu_selesai) BETWEEN ? AND ?
        AND hp.status = 'diterima'
        GROUP BY p.pembayaran
        ORDER BY jumlah_transaksi DESC
    ";

	$stmt = $koneksi->prepare($payment_query);
	$stmt->bind_param("ss", $tanggal_mulai, $tanggal_akhir);
	$stmt->execute();
	$payment_result = $stmt->get_result();

	$metode_pembayaran = [];
	while ($row = $payment_result->fetch_assoc()) {
		$metode_pembayaran[] = $row;
	}

	// Query untuk riwayat transaksi (hanya pesanan yang diterima)
	$riwayat_query = "
        SELECT 
            p.id_pesanan,
            p.total_harga,
            p.pembayaran,
            p.waktu_pesan,
            hp.waktu_selesai,
            hp.status,
            pl.nama as nama_pelanggan,
            pl.no_meja,
            k.username as nama_kasir
        FROM pesanan p
        JOIN history_pesanan hp ON p.id_pesanan = hp.pesanan_id
        JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
        LEFT JOIN kasir k ON hp.kasir_id = k.id_kasir
        WHERE DATE(hp.waktu_selesai) BETWEEN ? AND ?
        AND hp.status = 'diterima'
        ORDER BY hp.waktu_selesai DESC
    ";

	$stmt = $koneksi->prepare($riwayat_query);
	$stmt->bind_param("ss", $tanggal_mulai, $tanggal_akhir);
	$stmt->execute();
	$riwayat_result = $stmt->get_result();

	$riwayat = [];
	while ($row = $riwayat_result->fetch_assoc()) {
		// Ambil detail items untuk setiap pesanan
		$items_query = "
            SELECT 
                m.nama as nama_menu,
                dp.jumlah,
                m.harga
            FROM detail_pesanan dp
            JOIN menu m ON dp.menu_id = m.id_menu
            WHERE dp.pesanan_id = ?
        ";

		$stmt_items = $koneksi->prepare($items_query);
		$stmt_items->bind_param("i", $row['id_pesanan']);
		$stmt_items->execute();
		$items_result = $stmt_items->get_result();

		$items = [];
		while ($item = $items_result->fetch_assoc()) {
			$items[] = $item;
		}

		$row['items'] = $items;
		$riwayat[] = $row;
	}

	return [
		'periode' => [
			'mulai' => $tanggal_mulai,
			'akhir' => $tanggal_akhir
		],
		'ringkasan' => [
			'total_transaksi' => (int) $summary['total_transaksi'],
			'total_pendapatan' => (int) $summary['total_pendapatan'],
			'rata_transaksi' => round($summary['rata_transaksi'] ?? 0),
			'total_menu_terjual' => (int) $summary['total_menu_terjual']
		],
		'menu_terlaris' => $menu_terlaris,
		'metode_pembayaran' => $metode_pembayaran,
		'riwayat' => $riwayat
	];
}

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	header('Content-Type: application/json');

	$periode = $_POST['periode'] ?? 'hari';
	$tanggal_mulai = $_POST['tanggal_mulai'] ?? null;
	$tanggal_akhir = $_POST['tanggal_akhir'] ?? null;

	try {
		$laporan = getLaporanGabungan($periode, $tanggal_mulai, $tanggal_akhir);
		echo json_encode([
			'status' => 'success',
			'data' => $laporan
		]);
	} catch (Exception $e) {
		echo json_encode([
			'status' => 'error',
			'message' => 'Gagal memuat laporan: ' . $e->getMessage()
		]);
	}
	exit;
}
?>