<?php
require_once '../config/database.php';
session_start();

// Pastikan ada ID penjualan yang diberikan
$idpenjualan = $_GET['id'] ?? null;
if (!$idpenjualan) {
    $_SESSION['error_message'] = 'ID Penjualan tidak ditemukan.';
    header("Location: " . BASE_URL . "pages/transaksi.php");
    exit;
}

// Ambil data penjualan utama
$sql_penjualan = "SELECT p.*, u.username 
                  FROM penjualan p 
                  JOIN users u ON p.iduser = u.id 
                  WHERE p.idpenjualan = :idpenjualan";
$stmt_penjualan = $pdo->prepare($sql_penjualan);
$stmt_penjualan->execute(['idpenjualan' => $idpenjualan]);
$penjualan = $stmt_penjualan->fetch(PDO::FETCH_ASSOC);

if (!$penjualan) {
    $_SESSION['error_message'] = 'Transaksi tidak ditemukan.';
    header("Location: " . BASE_URL . "pages/transaksi.php");
    exit;
}

// Ambil detail penjualan
$sql_detail = "SELECT dp.*, b.nama as nama_barang 
               FROM detail_penjualan dp 
               JOIN barang b ON dp.idbarang = b.idbarang 
               WHERE dp.idpenjualan = :idpenjualan";
$stmt_detail = $pdo->prepare($sql_detail);
$stmt_detail->execute(['idpenjualan' => $idpenjualan]);
$details = $stmt_detail->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Penjualan #<?php echo $idpenjualan; ?></title>
    <style>
        body {
            font-family: 'Consolas', 'monospace';
            font-size: 12px;
            width: 80mm; /* Lebar standar untuk printer thermal */
            margin: 0 auto;
            padding: 5mm;
        }
        .struk-header, .struk-footer {
            text-align: center;
            margin-bottom: 5mm;
        }
        .struk-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1mm;
        }
        .struk-item .qty-nama {
            flex-grow: 1;
        }
        .struk-item .harga-total {
            text-align: right;
        }
        .garis {
            border-top: 1px dashed #000;
            margin: 3mm 0;
        }
        .total-section {
            text-align: right;
            margin-top: 5mm;
        }
        .total-section div {
            margin-bottom: 1mm;
        }
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="struk-header">
        <h3>TOKO KASIRKU</h3>
        <p>Jl. Contoh No. 123, Kota Contoh</p>
        <p>Telp: 0812-3456-7890</p>
    </div>

    <div class="garis"></div>

    <div>
        <p>Tanggal: <?php echo date('d-m-Y H:i:s', strtotime($penjualan['tanggal'])); ?></p>
        <p>Kasir: <?php echo htmlspecialchars($penjualan['username']); ?></p>
        <p>No. Transaksi: #<?php echo htmlspecialchars($penjualan['idpenjualan']); ?></p>
    </div>

    <div class="garis"></div>

    <?php foreach ($details as $item): ?>
        <div class="struk-item">
            <span class="qty-nama"><?php echo htmlspecialchars($item['qty']); ?> x <?php echo htmlspecialchars($item['nama_barang']); ?></span>
            <span class="harga-total">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></span>
        </div>
        <div class="struk-item" style="justify-content: flex-end;">
            <span class="harga-total">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></span>
        </div>
    <?php endforeach; ?>

    <div class="garis"></div>

    <div class="total-section">
        <div>Total: Rp <?php echo number_format($penjualan['total'], 0, ',', '.'); ?></div>
        <div>Bayar: Rp <?php echo number_format($penjualan['bayar'], 0, ',', '.'); ?></div>
        <div>Kembali: Rp <?php echo number_format($penjualan['kembalian'], 0, ',', '.'); ?></div>
    </div>

    <div class="garis"></div>

    <div class="struk-footer">
        <p>Terima Kasih Atas Kunjungan Anda!</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>
