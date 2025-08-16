<?php
session_start();
require_once '../config/database.php';

// Hanya Admin yang bisa mengakses halaman ini
if ($_SESSION['role'] !== 'Admin') {
    die("Akses ditolak.");
}

$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Query untuk mengambil data
$sql = "SELECT p.idpenjualan, p.tanggal, u.username, p.total, p.bayar, p.kembalian 
        FROM penjualan p 
        JOIN users u ON p.iduser = u.id 
        WHERE DATE(p.tanggal) BETWEEN :start_date AND :end_date 
        ORDER BY p.tanggal ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':start_date' => $start_date,
    ':end_date' => $end_date
]);
$laporans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nama file untuk diunduh
$filename = "Laporan_Penjualan_" . $start_date . "_sd_" . $end_date . ".csv";

// Set header untuk download file CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Buka output stream
$output = fopen('php://output', 'w');

// Tulis header kolom
fputcsv($output, ['ID Penjualan', 'Tanggal', 'Kasir', 'Total', 'Bayar', 'Kembalian']);

// Tulis data ke file CSV
if (count($laporans) > 0) {
    foreach ($laporans as $row) {
        fputcsv($output, $row);
    }
}

fclose($output);
exit();
?>