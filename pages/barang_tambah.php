<?php
require_once '../layouts/header.php';
require_once '../layouts/sidebar.php';
require_once '../config/database.php';

// Hanya Admin yang bisa mengakses halaman ini
if ($_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href = '<?php echo BASE_URL; ?>index.php';</script>";
    exit;
}

// Ambil data kategori untuk dropdown
$kategori_sql = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$kategori_stmt = $pdo->query($kategori_sql);
$kategories = $kategori_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h1 class="mt-4">Tambah Barang Baru</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>pages/barang.php">Data Barang</a></li>
    <li class="breadcrumb-item active">Tambah Barang</li>
</ol>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-plus-circle me-1"></i>
        Form Tambah Barang
    </div>
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>pages/barang_action.php" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="mb-3">
                <label for="barcode" class="form-label">Barcode</label>
                <input type="text" class="form-control" id="barcode" name="barcode">
            </div>
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
            </div>
            <div class="mb-3">
                <label for="idkategori" class="form-label">Kategori</label>
                <select class="form-select" id="idkategori" name="idkategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategories as $kategori): ?>
                        <option value="<?php echo $kategori['idkategori']; ?>"><?php echo htmlspecialchars($kategori['nama_kategori']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="harga_beli" class="form-label">Harga Beli</label>
                        <input type="number" class="form-control" id="harga_beli" name="harga_beli" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="harga_jual" class="form-label">Harga Jual</label>
                        <input type="number" class="form-control" id="harga_jual" name="harga_jual" required>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok Awal</label>
                <input type="number" class="form-control" id="stok" name="stok" value="0" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo BASE_URL; ?>pages/barang.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php
require_once '../layouts/footer.php';
?>
