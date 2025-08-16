<!-- Sidebar -->
<div class="bg-light border-end" id="sidebar-wrapper">
    <div class="sidebar-heading">Kasir App</div>
    <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="<?php echo BASE_URL; ?>index.php">Dashboard</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="<?php echo BASE_URL; ?>pages/transaksi.php">Transaksi</a>
        
        <?php if($_SESSION['role'] == 'Admin'): ?>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="<?php echo BASE_URL; ?>pages/barang.php">Data Barang</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="<?php echo BASE_URL; ?>pages/kategori.php">Kategori</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="<?php echo BASE_URL; ?>pages/laporan.php">Laporan</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="<?php echo BASE_URL; ?>pages/barang_masuk.php">Barang Masuk</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="<?php echo BASE_URL; ?>pages/barang_keluar.php">Barang Keluar</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="<?php echo BASE_URL; ?>pages/pengguna.php">Pengguna</a>
        <?php endif; ?>

    </div>
</div>

<!-- Page Content Wrapper -->
<div id="page-content-wrapper">
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container-fluid">
            <!-- <button class="btn btn-primary" id="sidebarToggle">Toggle Menu</button> -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-user me-2"></i><?php echo $_SESSION['username']; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="#!">Profile</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>logout.php">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4">