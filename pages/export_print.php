<?php
require_once '../config/database.php';
session_start();

// Hanya Admin yang bisa mengakses halaman ini
if ($_SESSION['role'] !== 'Admin') {
    die("Akses ditolak.");
}

$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Query untuk mengambil data
$sql = "SELECT p.*, u.username 
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

$total_pendapatan = array_sum(array_column($laporans, 'total'));

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
        body { font-family: sans-serif; }
        .container { max-width: 800px; margin: auto; }
        h2 { text-align: center; }
        .header-info { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Laporan Penjualan</h2>
        <div class="header-info">
            <strong>Periode:</strong> <?php echo date('d/m/Y', strtotime($start_date)); ?> - <?php echo date('d/m/Y', strtotime($end_date)); ?><br>
            <strong>Tanggal Cetak:</strong> <?php echo date('d/m/Y H:i:s'); ?>
        </div>

        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($laporans) > 0): ?>
                    <?php $no = 1; foreach ($laporans as $laporan): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($laporan['idpenjualan']); ?></td>
                        <td><?php echo htmlspecialchars(date('d-m-Y H:i', strtotime($laporan['tanggal']))); ?></td>
                        <td><?php echo htmlspecialchars($laporan['username']); ?></td>
                        <td class="text-end">Rp <?php echo number_format($laporan['total'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Total Pendapatan</th>
                    <th class="text-end">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        // Otomatis buka dialog print saat halaman dimuat
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
