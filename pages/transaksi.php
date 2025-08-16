<?php
require_once '../layouts/header.php';
require_once '../layouts/sidebar.php';
require_once '../config/database.php';

$sale_success = false;
$transaction_id = null;
if (isset($_GET['sale_success']) && $_GET['sale_success'] == 'true' && isset($_GET['id'])) {
    $sale_success = true;
    $transaction_id = $_GET['id'];
}
?>

<h1 class="mt-4">Transaksi Penjualan</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Transaksi Penjualan</li>
</ol>

<div class="row">
    <!-- Kolom Kiri: Pencarian Barang dan Hasil -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-search me-1"></i>
                Pencarian Barang
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="search-barang" class="form-label">Cari berdasarkan Barcode atau Nama</label>
                    <input type="text" class="form-control" id="search-barang" placeholder="Ketik untuk mencari...">
                </div>
                <div id="search-results" style="max-height: 400px; overflow-y: auto;">
                    <!-- Hasil pencarian akan muncul di sini -->
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Keranjang Belanja -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-shopping-cart me-1"></i>
                Keranjang
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>pages/transaksi_action.php" method="POST">
                    <input type="hidden" name="action" value="process_sale">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th width="15%">Qty</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cart-items">
                            <!-- Item keranjang akan ditambahkan di sini oleh JavaScript -->
                        </tbody>
                    </table>

                    <hr>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="total" class="form-label">Total (Rp)</label>
                            <input type="text" class="form-control form-control-lg" id="total" name="total" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="bayar" class="form-label">Bayar (Rp)</label>
                            <input type="text" class="form-control form-control-lg" id="bayar" name="bayar">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <label for="kembalian" class="form-label">Kembalian (Rp)</label>
                            <input type="text" class="form-control form-control-lg" id="kembalian" name="kembalian" readonly>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg">Proses & Cetak Struk</button>
                        <button type="button" class="btn btn-danger" id="cancel-sale">Batalkan Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Template untuk item keranjang -->
<template id="cart-item-template">
    <tr>
        <td class="nama-barang">Nama</td>
        <td class="harga-barang">0</td>
        <td>
            <input type="hidden" class="id-barang" name="id_barang[]">
            <input type="hidden" class="harga-hidden" name="harga[]">
            <input type="number" class="form-control form-control-sm qty-input" name="qty[]" value="1" min="1">
        </td>
        <td class="subtotal">0</td>
        <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="fa fa-times"></i></button></td>
    </tr>
</template>


<script>
    const BASE_URL_JS = '<?php echo BASE_URL; ?>';
    const SALE_SUCCESS = <?php echo json_encode($sale_success); ?>;
    const TRANSACTION_ID = <?php echo json_encode($transaction_id); ?>;
</script>
<?php
// Menambahkan script JS khusus untuk halaman ini
echo '<script src="' . BASE_URL . 'assets/js/transaksi.js"></script>';
require_once '../layouts/footer.php';
?>
