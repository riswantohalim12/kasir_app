<?php
// Definisikan BASE_URL secara dinamis
// Ini akan menghasilkan sesuatu seperti /kasir-app/
$base_dir = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', dirname(__DIR__)));
define('BASE_URL', $base_dir . '/');

// Konfigurasi Database
$host = 'localhost'; // atau sesuaikan dengan host database Anda
$dbname = 'kasir_db'; // nama database yang akan Anda buat
$user = 'root'; // username database
$pass = '12345678'; // password database, kosongkan jika tidak ada

// Membuat koneksi PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    // Set a PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If connection fails, stop the script and show an error
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>