<?php
// Include layout header
require_once 'layouts/header.php';
// Include database config
require_once 'config/database.php';

// Fetch data for dashboard cards
// 1. Total Jenis Barang
$stmt_barang = $pdo->query("SELECT count(idbarang) as total_barang FROM barang");
$total_barang = $stmt_barang->fetch(PDO::FETCH_ASSOC)['total_barang'];

// 2. Stok Habis (contoh: stok <= 5)
$stmt_stok = $pdo->query("SELECT count(idbarang) as total_stok_habis FROM barang WHERE stok <= 5");
$total_stok_habis = $stmt_stok->fetch(PDO::FETCH_ASSOC)['total_stok_habis'];

// Data untuk Admin
if ($_SESSION['role'] == 'Admin') {
    // Total Penjualan Hari Ini
    $stmt_penjualan_hari = $pdo->query("SELECT SUM(total) as total_penjualan_hari_ini FROM penjualan WHERE DATE(tanggal) = CURDATE()");
    $total_penjualan_hari_ini = $stmt_penjualan_hari->fetch(PDO::FETCH_ASSOC)['total_penjualan_hari_ini'];
    $total_penjualan_hari_ini = $total_penjualan_hari_ini ? $total_penjualan_hari_ini : 0;

    // Total Penjualan Bulan Ini
    $stmt_penjualan_bulan = $pdo->query("SELECT SUM(total) as total_penjualan_bulan_ini FROM penjualan WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())");
    $total_penjualan_bulan_ini = $stmt_penjualan_bulan->fetch(PDO::FETCH_ASSOC)['total_penjualan_bulan_ini'];
    $total_penjualan_bulan_ini = $total_penjualan_bulan_ini ? $total_penjualan_bulan_ini : 0;

    // Total Penjualan Tahun Ini
    $stmt_penjualan_tahun = $pdo->query("SELECT SUM(total) as total_penjualan_tahun_ini FROM penjualan WHERE YEAR(tanggal) = YEAR(CURDATE())");
    $total_penjualan_tahun_ini = $stmt_penjualan_tahun->fetch(PDO::FETCH_ASSOC)['total_penjualan_tahun_ini'];
    $total_penjualan_tahun_ini = $total_penjualan_tahun_ini ? $total_penjualan_tahun_ini : 0;

    // Jumlah Transaksi Hari Ini
    $stmt_transaksi_hari = $pdo->query("SELECT COUNT(idpenjualan) as jumlah_transaksi_hari_ini FROM penjualan WHERE DATE(tanggal) = CURDATE()");
    $jumlah_transaksi_hari_ini = $stmt_transaksi_hari->fetch(PDO::FETCH_ASSOC)['jumlah_transaksi_hari_ini'];
    $jumlah_transaksi_hari_ini = $jumlah_transaksi_hari_ini ? $jumlah_transaksi_hari_ini : 0;

    // Jumlah Transaksi Bulan Ini
    $stmt_transaksi_bulan = $pdo->query("SELECT COUNT(idpenjualan) as jumlah_transaksi_bulan_ini FROM penjualan WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())");
    $jumlah_transaksi_bulan_ini = $stmt_transaksi_bulan->fetch(PDO::FETCH_ASSOC)['jumlah_transaksi_bulan_ini'];
    $jumlah_transaksi_bulan_ini = $jumlah_transaksi_bulan_ini ? $jumlah_transaksi_bulan_ini : 0;

} else { // Data untuk Kasir
    // Total Penjualan Hari Ini (untuk kasir yang sedang login)
    $stmt_penjualan_kasir_hari = $pdo->prepare("SELECT SUM(total) as total_penjualan_kasir_hari_ini FROM penjualan WHERE DATE(tanggal) = CURDATE() AND iduser = :iduser");
    $stmt_penjualan_kasir_hari->execute([':iduser' => $_SESSION['id']]);
    $total_penjualan_kasir_hari_ini = $stmt_penjualan_kasir_hari->fetch(PDO::FETCH_ASSOC)['total_penjualan_kasir_hari_ini'];
    $total_penjualan_kasir_hari_ini = $total_penjualan_kasir_hari_ini ? $total_penjualan_kasir_hari_ini : 0;

    // Jumlah Transaksi Hari Ini (untuk kasir yang sedang login)
    $stmt_transaksi_kasir_hari = $pdo->prepare("SELECT COUNT(idpenjualan) as jumlah_transaksi_kasir_hari_ini FROM penjualan WHERE DATE(tanggal) = CURDATE() AND iduser = :iduser");
    $stmt_transaksi_kasir_hari->execute([':iduser' => $_SESSION['id']]);
    $jumlah_transaksi_kasir_hari_ini = $stmt_transaksi_kasir_hari->fetch(PDO::FETCH_ASSOC)['jumlah_transaksi_kasir_hari_ini'];
    $jumlah_transaksi_kasir_hari_ini = $jumlah_transaksi_kasir_hari_ini ? $jumlah_transaksi_kasir_hari_ini : 0;
}

?>

<!-- Include layout sidebar -->
<?php require_once 'layouts/sidebar.php'; ?>

<h1 class="mt-4">Dashboard</h1>
<p>Selamat datang, <?php echo $_SESSION['username']; ?>!</p>

<div class="row">
    <?php if ($_SESSION['role'] == 'Admin'): ?>
        <!-- Admin Dashboard Cards -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <i class="fa fa-box fa-2x"></i>
                        <div>
                            <div class="fs-3 fw-bold"><?php echo $total_barang; ?></div>
                            <div>Total Jenis Barang</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?php echo BASE_URL; ?>pages/barang.php">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <i class="fa fa-exclamation-triangle fa-2x"></i>
                        <div>
                            <div class="fs-3 fw-bold"><?php echo $total_stok_habis; ?></div>
                            <div>Stok Akan Habis</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?php echo BASE_URL; ?>pages/barang.php?filter=stok_habis">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <i class="fa fa-cash-register fa-2x"></i>
                        <div>
                            <div class="fs-3 fw-bold">Rp <?php echo number_format($total_penjualan_hari_ini, 0, ',', '.'); ?></div>
                            <div>Penjualan Hari Ini</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?php echo BASE_URL; ?>pages/laporan.php">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <i class="fa fa-calendar-day fa-2x"></i>
                        <div>
                            <div class="fs-3 fw-bold"><?php echo $jumlah_transaksi_hari_ini; ?></div>
                            <div>Transaksi Hari Ini</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?php echo BASE_URL; ?>pages/laporan.php?start_date=<?php echo date('Y-m-d'); ?>&end_date=<?php echo date('Y-m-d'); ?>">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-secondary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <i class="fa fa-calendar-alt fa-2x"></i>
                        <div>
                            <div class="fs-3 fw-bold">Rp <?php echo number_format($total_penjualan_bulan_ini, 0, ',', '.'); ?></div>
                            <div>Penjualan Bulan Ini</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?php echo BASE_URL; ?>pages/laporan.php?start_date=<?php echo date('Y-m-01'); ?>&end_date=<?php echo date('Y-m-t'); ?>">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-dark text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <i class="fa fa-calendar-check fa-2x"></i>
                        <div>
                            <div class="fs-3 fw-bold">Rp <?php echo number_format($total_penjualan_tahun_ini, 0, ',', '.'); ?></div>
                            <div>Penjualan Tahun Ini</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?php echo BASE_URL; ?>pages/laporan.php?start_date=<?php echo date('Y-01-01'); ?>&end_date=<?php echo date('Y-12-31'); ?>">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <i class="fa fa-receipt fa-2x"></i>
                        <div>
                            <div class="fs-3 fw-bold"><?php echo $jumlah_transaksi_bulan_ini; ?></div>
                            <div>Transaksi Bulan Ini</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?php echo BASE_URL; ?>pages/laporan.php?start_date=<?php echo date('Y-m-01'); ?>&end_date=<?php echo date('Y-m-t'); ?>">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

    <?php else: // Kasir Dashboard Cards ?>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <i class="fa fa-cash-register fa-2x"></i>
                        <div>
                            <div class="fs-3 fw-bold">Rp <?php echo number_format($total_penjualan_kasir_hari_ini, 0, ',', '.'); ?></div>
                            <div>Penjualan Anda Hari Ini</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?php echo BASE_URL; ?>pages/laporan.php?start_date=<?php echo date('Y-m-d'); ?>&end_date=<?php echo date('Y-m-d'); ?>">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <i class="fa fa-receipt fa-2x"></i>
                        <div>
                            <div class="fs-3 fw-bold"><?php echo $jumlah_transaksi_kasir_hari_ini; ?></div>
                            <div>Transaksi Anda Hari Ini</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?php echo BASE_URL; ?>pages/laporan.php?start_date=<?php echo date('Y-m-d'); ?>&end_date=<?php echo date('Y-m-d'); ?>">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <i class="fa fa-shopping-cart fa-2x"></i>
                        <div>
                            <div class="fs-3 fw-bold">Ayo Jual!</div>
                            <div>Mulai Transaksi Baru</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?php echo BASE_URL; ?>pages/transaksi.php">Mulai</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Include layout footer -->
<?php require_once 'layouts/footer.php'; ?>