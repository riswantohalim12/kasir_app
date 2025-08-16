<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'process_sale') {

    // Ambil data dari form
    $id_user = $_SESSION['id'];
    $total = $_POST['total_numeric'];
    $bayar = $_POST['bayar_numeric'];
    $kembalian = $bayar - $total;

    $id_barangs = $_POST['id_barang'];
    $qtys = $_POST['qty'];
    $hargas = $_POST['harga'];

    // Validasi dasar
    if (empty($id_barangs) || $total <= 0 || $bayar < $total) {
        $_SESSION['error_message'] = 'Keranjang kosong atau pembayaran tidak cukup.';
        header("Location: " . BASE_URL . "pages/transaksi.php");
        exit;
    }

    try {
        // Mulai transaksi database
        $pdo->beginTransaction();

        // 1. Simpan ke tabel `penjualan`
        $sql_penjualan = "INSERT INTO penjualan (iduser, tanggal, total, bayar, kembalian) VALUES (:iduser, NOW(), :total, :bayar, :kembalian)";
        $stmt_penjualan = $pdo->prepare($sql_penjualan);
        $stmt_penjualan->execute([
            ':iduser' => $id_user,
            ':total' => $total,
            ':bayar' => $bayar,
            ':kembalian' => $kembalian
        ]);

        // Ambil ID penjualan terakhir
        $id_penjualan_terakhir = $pdo->lastInsertId();

        // 2. Loop untuk simpan ke `detail_penjualan` dan update stok
        for ($i = 0; $i < count($id_barangs); $i++) {
            $id_barang = $id_barangs[$i];
            $qty = $qtys[$i];
            $harga = $hargas[$i];
            $subtotal = $qty * $harga;

            // Simpan ke `detail_penjualan`
            $sql_detail = "INSERT INTO detail_penjualan (idpenjualan, idbarang, qty, harga, subtotal) VALUES (:idpenjualan, :idbarang, :qty, :harga, :subtotal)";
            $stmt_detail = $pdo->prepare($sql_detail);
            $stmt_detail->execute([
                ':idpenjualan' => $id_penjualan_terakhir,
                ':idbarang' => $id_barang,
                ':qty' => $qty,
                ':harga' => $harga,
                ':subtotal' => $subtotal
            ]);

            // Update stok di tabel `barang`
            $sql_update_stok = "UPDATE barang SET stok = stok - :qty WHERE idbarang = :idbarang";
            $stmt_update_stok = $pdo->prepare($sql_update_stok);
            $stmt_update_stok->execute([
                ':qty' => $qty,
                ':idbarang' => $id_barang
            ]);
        }

        // Jika semua berhasil, commit transaksi
        $pdo->commit();

        // $_SESSION['success_message'] = "Transaksi berhasil disimpan. ID Penjualan: " . $id_penjualan_terakhir;
        header("Location: " . BASE_URL . "pages/transaksi.php?sale_success=true&id=" . $id_penjualan_terakhir);
        exit;

    } catch (Exception $e) {
        // Jika ada error, batalkan semua perubahan
        $pdo->rollBack();
        $_SESSION['error_message'] = "Transaksi Gagal: " . $e->getMessage();
        header("Location: " . BASE_URL . "pages/transaksi.php");
        exit;
    }

} else {
    header("Location: " . BASE_URL . "pages/transaksi.php");
    exit;
}
?>