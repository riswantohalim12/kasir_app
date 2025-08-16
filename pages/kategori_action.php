<?php
session_start();
require_once '../config/database.php';

// Pastikan hanya Admin yang bisa mengakses aksi ini
if ($_SESSION['role'] !== 'Admin') {
    die("Akses ditolak.");
}

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_kategori = $_POST['nama_kategori'];

            if (!empty($nama_kategori)) {
                try {
                    $sql = "INSERT INTO kategori (nama_kategori) VALUES (:nama_kategori)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['nama_kategori' => $nama_kategori]);

                    $_SESSION['message'] = 'Kategori berhasil ditambahkan.';
                    header("Location: " . BASE_URL . "pages/kategori.php");
                    exit();
                } catch (PDOException $e) {
                    die("Error: " . $e->getMessage());
                }
            } else {
                die("Nama kategori tidak boleh kosong.");
            }
        }
        break;

    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idkategori = $_POST['idkategori'];
            $nama_kategori = $_POST['nama_kategori'];

            if (!empty($idkategori) && !empty($nama_kategori)) {
                try {
                    $sql = "UPDATE kategori SET nama_kategori = :nama_kategori WHERE idkategori = :idkategori";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        'nama_kategori' => $nama_kategori,
                        'idkategori' => $idkategori
                    ]);

                    $_SESSION['message'] = 'Kategori berhasil diperbarui.';
                    header("Location: " . BASE_URL . "pages/kategori.php");
                    exit();
                } catch (PDOException $e) {
                    die("Error: " . $e->getMessage());
                }
            } else {
                die("Nama kategori atau ID tidak boleh kosong.");
            }
        }
        break;

    case 'delete':
        $idkategori = $_GET['id'] ?? null;
        if ($idkategori) {
            try {
                // Karena ada foreign key constraint ON DELETE CASCADE,
                // menghapus kategori akan otomatis menghapus barang di dalamnya.
                $sql = "DELETE FROM kategori WHERE idkategori = :idkategori";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['idkategori' => $idkategori]);

                $_SESSION['message'] = 'Kategori dan semua barang di dalamnya berhasil dihapus.';
                header("Location: " . BASE_URL . "pages/kategori.php");
                exit();
            } catch (PDOException $e) {
                $_SESSION['message'] = 'Gagal menghapus kategori.';
                header("Location: " . BASE_URL . "pages/kategori.php");
                exit();
            }
        }
        break;

    default:
        header("Location: " . BASE_URL . "pages/kategori.php");
        exit();
}
?>