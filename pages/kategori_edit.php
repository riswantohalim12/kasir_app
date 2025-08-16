<?php
require_once '../layouts/header.php';
require_once '../layouts/sidebar.php';
require_once '../config/database.php';

// Hanya Admin yang bisa mengakses halaman ini
if ($_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href = '<?php echo BASE_URL; ?>index.php';</script>";
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: " . BASE_URL . "pages/kategori.php");
    exit;
}

// Ambil data kategori yang akan diedit
$sql = "SELECT * FROM kategori WHERE idkategori = :idkategori";
$stmt = $pdo->prepare($sql);
$stmt->execute(['idkategori' => $id]);
$kategori = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kategori) {
    echo "Kategori tidak ditemukan.";
    exit;
}
?>

<h1 class="mt-4">Edit Kategori</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>pages/kategori.php">Data Kategori</a></li>
    <li class="breadcrumb-item active">Edit Kategori</li>
</ol>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-edit me-1"></i>
        Form Edit Kategori
    </div>
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>pages/kategori_action.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="idkategori" value="<?php echo $kategori['idkategori']; ?>">
            <div class="mb-3">
                <label for="nama_kategori" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" value="<?php echo htmlspecialchars($kategori['nama_kategori']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="<?php echo BASE_URL; ?>pages/kategori.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php
require_once '../layouts/footer.php';
?>
