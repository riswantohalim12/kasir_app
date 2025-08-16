<?php
require_once '../layouts/header.php';
require_once '../layouts/sidebar.php';
require_once '../config/database.php';

// Hanya Admin yang bisa mengakses halaman ini
if ($_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href = '<?php echo BASE_URL; ?>index.php';</script>";
    exit;
}

// Query untuk mengambil data pengguna
$sql = "SELECT id, username, role FROM users ORDER BY id ASC";
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h1 class="mt-4">Data Pengguna</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Data Pengguna</li>
</ol>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-users me-1"></i>
        Tabel Data Pengguna
        <a href="<?php echo BASE_URL; ?>pages/pengguna_tambah.php" class="btn btn-primary btn-sm float-end"><i class="fa fa-plus"></i> Tambah Pengguna</a>
    </div>
    <div class="card-body">
        <table id="datatablesSimple" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php $no = 1; foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>pages/pengguna_edit.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                            <?php // Jangan biarkan admin menghapus akunnya sendiri ?>
                            <?php if ($_SESSION['id'] !== $user['id']): ?>
                                <a href="<?php echo BASE_URL; ?>pages/pengguna_action.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')"><i class="fa fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data pengguna.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once '../layouts/footer.php';
?>
