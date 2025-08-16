<?php
require_once '../layouts/header.php';
require_once '../layouts/sidebar.php';
require_once '../config/database.php';

// Hanya Admin yang bisa mengakses halaman ini
if ($_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href = '<?php echo BASE_URL; ?>index.php';</script>";
    exit;
}

// Query untuk mengambil data kategori
$sql = "SELECT * FROM kategori ORDER BY idkategori DESC";
$stmt = $pdo->query($sql);
$kategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h1 class="mt-4">Data Kategori</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Data Kategori</li>
</ol>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-tags me-1"></i>
        Tabel Data Kategori
        <a href="<?php echo BASE_URL; ?>pages/kategori_tambah.php" class="btn btn-primary btn-sm float-end"><i class="fa fa-plus"></i> Tambah Kategori</a>
    </div>
    <div class="card-body">
        <table id="datatablesSimple" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($kategories) > 0): ?>
                    <?php $no = 1; foreach ($kategories as $kategori): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($kategori['nama_kategori']); ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>pages/kategori_edit.php?id=<?php echo $kategori['idkategori']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                            <a href="<?php echo BASE_URL; ?>pages/kategori_action.php?action=delete&id=<?php echo $kategori['idkategori']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus kategori ini? Menghapus kategori akan menghapus semua barang di dalamnya.')"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data kategori.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once '../layouts/footer.php';
?>
