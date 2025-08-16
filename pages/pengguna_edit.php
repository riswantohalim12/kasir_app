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
    header("Location: " . BASE_URL . "pages/pengguna.php");
    exit;
}

// Ambil data pengguna yang akan diedit
$sql = "SELECT id, username, role FROM users WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Pengguna tidak ditemukan.";
    exit;
}
?>

<h1 class="mt-4">Edit Pengguna</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>pages/pengguna.php">Data Pengguna</a></li>
    <li class="breadcrumb-item active">Edit Pengguna</li>
</ol>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-user-edit me-1"></i>
        Form Edit Pengguna
    </div>
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>pages/pengguna_action.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password Baru (Opsional)</label>
                <input type="password" class="form-control" id="password" name="password">
                <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required <?php echo ($user['id'] == $_SESSION['id']) ? 'disabled' : ''; ?>>
                    <option value="Admin" <?php echo ($user['role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="Kasir" <?php echo ($user['role'] == 'Kasir') ? 'selected' : ''; ?>>Kasir</option>
                </select>
                 <?php if ($user['id'] == $_SESSION['id']): ?>
                    <div class="form-text">Anda tidak dapat mengubah role akun Anda sendiri.</div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="<?php echo BASE_URL; ?>pages/pengguna.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php
require_once '../layouts/footer.php';
?>
