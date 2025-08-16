<?php
session_start();
require_once '../config/database.php';

// Pastikan hanya Admin yang bisa mengakses aksi ini
if ($_SESSION['role'] !== 'Admin') {
    die("Akses ditolak.");
}

// Ambil aksi dari POST atau GET untuk fleksibilitas
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $barcode = $_POST['barcode'];
            $nama = $_POST['nama'];
            $idkategori = $_POST['idkategori'];
            $harga_beli = $_POST['harga_beli'];
            $harga_jual = $_POST['harga_jual'];
            $stok = $_POST['stok'];

            if (!empty($nama) && !empty($idkategori) && !empty($harga_beli) && !empty($harga_jual) && isset($stok)) {
                try {
                    $sql = "INSERT INTO barang (barcode, nama, idkategori, harga_beli, harga_jual, stok) VALUES (:barcode, :nama, :idkategori, :harga_beli, :harga_jual, :stok)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':barcode' => $barcode,
                        ':nama' => $nama,
                        ':idkategori' => $idkategori,
                        ':harga_beli' => $harga_beli,
                        ':harga_jual' => $harga_jual,
                        ':stok' => $stok
                    ]);
                    $_SESSION['message'] = 'Barang berhasil ditambahkan.';
                    header("Location: " . BASE_URL . "pages/barang.php");
                    exit();
                } catch (PDOException $e) {
                    die("Error: " . $e->getMessage());
                }
            }
        }
        break;

    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idbarang = $_POST['idbarang'];
            $barcode = $_POST['barcode'];
            $nama = $_POST['nama'];
            $idkategori = $_POST['idkategori'];
            $harga_beli = $_POST['harga_beli'];
            $harga_jual = $_POST['harga_jual'];
            $stok = $_POST['stok'];

            if (!empty($idbarang) && !empty($nama) && !empty($idkategori) && !empty($harga_beli) && !empty($harga_jual) && isset($stok)) {
                try {
                    $sql = "UPDATE barang SET barcode = :barcode, nama = :nama, idkategori = :idkategori, harga_beli = :harga_beli, harga_jual = :harga_jual, stok = :stok WHERE idbarang = :idbarang";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':idbarang' => $idbarang,
                        ':barcode' => $barcode,
                        ':nama' => $nama,
                        ':idkategori' => $idkategori,
                        ':harga_beli' => $harga_beli,
                        ':harga_jual' => $harga_jual,
                        ':stok' => $stok
                    ]);
                    $_SESSION['message'] = 'Data barang berhasil diperbarui.';
                    header("Location: " . BASE_URL . "pages/barang.php");
                    exit();
                } catch (PDOException $e) {
                    die("Error: " . $e->getMessage());
                }
            }
        }
        break;

    case 'delete':
        $idbarang = $_GET['id'] ?? null;
        if ($idbarang) {
            try {
                $sql = "DELETE FROM barang WHERE idbarang = :idbarang";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':idbarang', $idbarang, PDO::PARAM_INT);
                $stmt->execute();

                $_SESSION['message'] = 'Barang berhasil dihapus.';
                header("Location: " . BASE_URL . "pages/barang.php");
                exit();
            } catch (PDOException $e) {
                $_SESSION['message'] = 'Gagal menghapus barang. Mungkin barang ini sudah digunakan dalam transaksi.';
                header("Location: " . BASE_URL . "pages/barang.php");
                exit();
            }
        }
        break;

    default:
        header("Location: " . BASE_URL . "pages/barang.php");
        exit();
}
?>