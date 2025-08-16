<?php
require_once '../layouts/header.php';
require_once '../layouts/sidebar.php';
require_once '../config/database.php';

// Hanya Admin yang bisa mengakses halaman ini
if ($_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href = '<?php echo BASE_URL; ?>index.php';</script>";
    exit;
}

// Query untuk mengambil data barang beserta nama kategori
$sql = "SELECT b.*, k.nama_kategori FROM barang b JOIN kategori k ON b.idkategori = k.idkategori ORDER BY b.idbarang DESC";
$stmt = $pdo->query($sql);
$barangs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h1 class="mt-4">Data Barang</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Data Barang</li>
</ol>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Tabel Data Barang
        <a href="<?php echo BASE_URL; ?>pages/barang_tambah.php" class="btn btn-primary btn-sm float-end"><i class="fa fa-plus"></i> Tambah Barang</a>
    </div>
    <div class="card-body">
        <table id="datatablesSimple" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Barcode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($barangs) > 0): ?>
                    <?php $no = 1; foreach ($barangs as $barang): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($barang['barcode']); ?></td>
                        <td><?php echo htmlspecialchars($barang['nama']); ?></td>
                        <td><?php echo htmlspecialchars($barang['nama_kategori']); ?></td>
                        <td>Rp <?php echo number_format($barang['harga_beli'], 0, ',', '.'); ?></td>
                        <td>Rp <?php echo number_format($barang['harga_jual'], 0, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($barang['stok']); ?></td>
                        <td>
                            <a href="barang_edit.php?id=<?php echo $barang['idbarang']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                            <a href="barang_action.php?action=delete&id=<?php echo $barang['idbarang']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?')"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data barang.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once '../layouts/footer.php';
?>
