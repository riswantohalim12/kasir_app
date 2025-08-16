<?php
require_once '../layouts/header.php';
require_once '../layouts/sidebar.php';

// Hanya Admin yang bisa mengakses halaman ini
if ($_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href = '<?php echo BASE_URL; ?>index.php';</script>";
    exit;
}
?>

<h1 class="mt-4">Tambah Pengguna Baru</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>pages/pengguna.php">Data Pengguna</a></li>
    <li class="breadcrumb-item active">Tambah Pengguna</li>
</ol>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-user-plus me-1"></i>
        Form Tambah Pengguna
    </div>
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>pages/pengguna_action.php" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="form-text">Password default jika tidak diisi adalah: 123456</div>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="Admin">Admin</option>
                    <option value="Kasir">Kasir</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo BASE_URL; ?>pages/pengguna.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php
require_once '../layouts/footer.php';
?>
