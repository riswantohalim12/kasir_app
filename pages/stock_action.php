<?php
session_start();
require_once '../config/database.php';

// Pastikan hanya Admin yang bisa mengakses aksi ini
if ($_SESSION['role'] !== 'Admin') {
    die("Akses ditolak.");
}

$action = $_POST['action'] ?? '';

$idbarang = $_POST['idbarang'] ?? null;
$qty = $_POST['qty'] ?? null;
$keterangan = $_POST['keterangan'] ?? null;

if (empty($idbarang) || empty($qty) || $qty <= 0) {
    $_SESSION['error_message'] = 'Data tidak lengkap atau jumlah tidak valid.';
    header("Location: " . BASE_URL . "pages/barang_masuk.php"); // Default redirect
    exit();
}

try {
    $pdo->beginTransaction();

    // Ambil stok barang saat ini
    $sql_get_stok = "SELECT stok, nama FROM barang WHERE idbarang = :idbarang";
    $stmt_get_stok = $pdo->prepare($sql_get_stok);
    $stmt_get_stok->execute(['idbarang' => $idbarang]);
    $barang = $stmt_get_stok->fetch(PDO::FETCH_ASSOC);

    if (!$barang) {
        throw new Exception('Barang tidak ditemukan.');
    }

    $current_stok = $barang['stok'];
    $nama_barang = $barang['nama'];

    switch ($action) {
        case 'add_masuk':
            // Insert ke barang_masuk
            $sql_insert_masuk = "INSERT INTO barang_masuk (idbarang, qty, keterangan) VALUES (:idbarang, :qty, :keterangan)";
            $stmt_insert_masuk = $pdo->prepare($sql_insert_masuk);
            $stmt_insert_masuk->execute([
                ':idbarang' => $idbarang,
                ':qty' => $qty,
                ':keterangan' => $keterangan
            ]);

            // Update stok barang
            $new_stok = $current_stok + $qty;
            $sql_update_stok = "UPDATE barang SET stok = :new_stok WHERE idbarang = :idbarang";
            $stmt_update_stok = $pdo->prepare($sql_update_stok);
            $stmt_update_stok->execute([
                ':new_stok' => $new_stok,
                ':idbarang' => $idbarang
            ]);

            $_SESSION['message'] = 'Barang masuk berhasil dicatat. Stok ' . $nama_barang . ' menjadi ' . $new_stok . '.';
            header("Location: " . BASE_URL . "pages/barang_masuk.php");
            break;

        case 'add_keluar':
            // Validasi stok
            if ($qty > $current_stok) {
                throw new Exception('Stok ' . $nama_barang . ' tidak mencukupi. Stok tersedia: ' . $current_stok . '.');
            }

            // Insert ke barang_keluar
            $sql_insert_keluar = "INSERT INTO barang_keluar (idbarang, qty, keterangan) VALUES (:idbarang, :qty, :keterangan)";
            $stmt_insert_keluar = $pdo->prepare($sql_insert_keluar);
            $stmt_insert_keluar->execute([
                ':idbarang' => $idbarang,
                ':qty' => $qty,
                ':keterangan' => $keterangan
            ]);

            // Update stok barang
            $new_stok = $current_stok - $qty;
            $sql_update_stok = "UPDATE barang SET stok = :new_stok WHERE idbarang = :idbarang";
            $stmt_update_stok = $pdo->prepare($sql_update_stok);
            $stmt_update_stok->execute([
                ':new_stok' => $new_stok,
                ':idbarang' => $idbarang
            ]);

            $_SESSION['message'] = 'Barang keluar berhasil dicatat. Stok ' . $nama_barang . ' menjadi ' . $new_stok . '.';
            header("Location: " . BASE_URL . "pages/barang_keluar.php");
            break;

        default:
            $_SESSION['error_message'] = 'Aksi tidak valid.';
            header("Location: " . BASE_URL . "index.php");
            break;
    }

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error_message'] = 'Gagal mencatat stok: ' . $e->getMessage();
    // Redirect berdasarkan aksi yang gagal
    if ($action == 'add_masuk') {
        header("Location: " . BASE_URL . "pages/barang_masuk.php");
    } elseif ($action == 'add_keluar') {
        header("Location: " . BASE_URL . "pages/barang_keluar.php");
    } else {
        header("Location: " . BASE_URL . "index.php");
    }
}
exit();
?>