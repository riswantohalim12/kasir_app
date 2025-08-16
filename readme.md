# Web Server (Apache, Nginx, XAMPP, WAMP, Laragon, dll.)
   -   PHP 7.4+ (dengan ekstensi PDO MySQL diaktifkan)
   -   MySQL / MariaDB

     ## Panduan Instalasi
    
  **Clone Repository:**
      git clone <URL_REPOSITORY_ANDA>
      cd kasir-app

  **Konfigurasi Database:**
    -   Buka phpMyAdmin atau klien MySQL favorit Anda.
    -   Buat database baru, misalnya `kasir_db`.
    -   Import file `database.sql` ke database yang baru Anda buat. File ini berisi skema tabel dan
      data pengguna awal.
   
 **Konfigurasi Aplikasi:**
    -   Buka file `config/database.php`.
    -   Sesuaikan kredensial database (`$host`, `$dbname`, `$user`, `$pass`) dengan pengaturan
      MySQL Anda.
    -   Pastikan `BASE_URL` di `config/database.php` sudah terdeteksi dengan benar. Jika Anda
      menempatkan folder `kasir-app` langsung di `htdocs` atau `www`, `BASE_URL` akan otomatis menjadi
      `/kasir-app/`. Jika Anda menempatkannya di sub-folder lain, sesuaikan path-nya.
   
     **Akses Aplikasi:**
      Tempatkan folder `kasir-app` di direktori root web server Anda (misalnya `htdocs` untuk
      XAMPP, `www` untuk WAMP).
    -   Buka browser Anda dan akses: `http://localhost/kasir-app/` (atau sesuai dengan konfigurasi
      server Anda).
   15
   16 ## Kredensial Login Default
   17
   18 Setelah mengimpor `database.sql`, Anda dapat login dengan kredensial berikut:
   19
   20 -   **Admin:**
   21     -   Username: `admin`
   22     -   Password: `123456`
   23 -   **Kasir:**
   24     -   Username: `kasir`
   25     -   Password: `123456`
   26
   27 ## Kontribusi
   28
   29 Kontribusi, laporan bug, dan saran sangat diterima! Silakan buka *issue* atau *pull request* di
      *repository* ini.