<?php
require_once '../layouts/header.php';
require_once '../layouts/sidebar.php';
require_once '../config/database.php';

// Hanya Admin yang bisa mengakses halaman ini
if ($_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href = '<?php echo BASE_URL; ?>index.php';</script>";
    exit;
}

// Set default date range to today
$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Query untuk mengambil data penjualan berdasarkan rentang tanggal
$sql = "SELECT p.*, u.username 
        FROM penjualan p 
        JOIN users u ON p.iduser = u.id 
        WHERE DATE(p.tanggal) BETWEEN :start_date AND :end_date 
        ORDER BY p.tanggal DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':start_date' => $start_date,
    ':end_date' => $end_date
]);
$laporans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total pendapatan untuk periode yang dipilih
$total_pendapatan = 0;
foreach ($laporans as $laporan) {
    $total_pendapatan += $laporan['total'];
}

?>

<h1 class="mt-4">Laporan Penjualan</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Laporan Penjualan</li>
</ol>

<!-- Form Filter Tanggal -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-filter me-1"></i>
        Filter Laporan
    </div>
    <div class="card-body">
        <form method="GET" action="">
            <div class="row">
                <div class="col-md-5">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Laporan -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Hasil Laporan dari tanggal <?php echo date('d-m-Y', strtotime($start_date)); ?> s/d <?php echo date('d-m-Y', strtotime($end_date)); ?>
        <div class="float-end">
            <a href="<?php echo BASE_URL; ?>pages/export_excel.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn btn-success btn-sm"><i class="fas fa-file-excel me-1"></i> Export Excel</a>
            <a href="<?php echo BASE_URL; ?>pages/export_print.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" target="_blank" class="btn btn-secondary btn-sm"><i class="fas fa-print me-1"></i> Versi Cetak</a>
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>Total Pendapatan: Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></strong>
        </div>
        <table id="datatablesSimple" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Penjualan</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Total</th>
                    <th>Bayar</th>
                    <th>Kembalian</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($laporans) > 0): ?>
                    <?php $no = 1; foreach ($laporans as $laporan): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($laporan['idpenjualan']); ?></td>
                        <td><?php echo htmlspecialchars(date('d-m-Y H:i:s', strtotime($laporan['tanggal']))); ?></td>
                        <td><?php echo htmlspecialchars($laporan['username']); ?></td>
                        <td>Rp <?php echo number_format($laporan['total'], 0, ',', '.'); ?></td>
                        <td>Rp <?php echo number_format($laporan['bayar'], 0, ',', '.'); ?></td>
                        <td>Rp <?php echo number_format($laporan['kembalian'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data penjualan pada periode ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once '../layouts/footer.php';
?>
