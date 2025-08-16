<?php
require_once '../layouts/header.php';
require_once '../layouts/sidebar.php';
require_once '../config/database.php';

// Hanya Admin yang bisa mengakses halaman ini
if ($_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href = '" . BASE_URL . "index.php';</script>";
    exit;
}

// Ambil daftar barang untuk dropdown
$barang_sql = "SELECT idbarang, nama, stok FROM barang ORDER BY nama ASC";
$barang_stmt = $pdo->query($barang_sql);
$barangs = $barang_stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data barang keluar
$barang_keluar_sql = "SELECT bk.*, b.nama as nama_barang 
                     FROM barang_keluar bk 
                     JOIN barang b ON bk.idbarang = b.idbarang 
                     ORDER BY bk.tanggal DESC";
$barang_keluar_stmt = $pdo->query($barang_keluar_sql);
$barang_keluar_records = $barang_keluar_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h1 class="mt-4">Barang Keluar</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Barang Keluar</li>
</ol>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-minus-square me-1"></i>
        Tambah Barang Keluar
    </div>
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>pages/stock_action.php" method="POST">
            <input type="hidden" name="action" value="add_keluar">
            <div class="mb-3">
                <label for="idbarang" class="form-label">Nama Barang</label>
                <select class="form-select" id="idbarang" name="idbarang" required>
                    <option value="">-- Pilih Barang --</option>
                    <?php foreach ($barangs as $barang): ?>
                        <option value="<?php echo $barang['idbarang']; ?>"> <?php echo htmlspecialchars($barang['nama']); ?> (Stok: <?php echo $barang['stok']; ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="qty" class="form-label">Jumlah Keluar</label>
                <input type="number" class="form-control" id="qty" name="qty" min="1" required>
            </div>
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Barang Keluar</button>
        </form>
    </div>
</div>

<div class="card mb-4 mt-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Riwayat Barang Keluar
    </div>
    <div class="card-body">
        <table id="datatablesSimple" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Jumlah Keluar</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($barang_keluar_records) > 0): ?>
                    <?php $no = 1; foreach ($barang_keluar_records as $record): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars(date('d-m-Y H:i:s', strtotime($record['tanggal']))); ?></td>
                        <td><?php echo htmlspecialchars($record['nama_barang']); ?></td>
                        <td><?php echo htmlspecialchars($record['qty']); ?></td>
                        <td><?php echo htmlspecialchars($record['keterangan']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada riwayat barang keluar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once '../layouts/footer.php';
?>
