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
            $username = $_POST['username'];
            $password = $_POST['password'];
            $role = $_POST['role'];

            // Gunakan password default jika input kosong
            if (empty($password)) {
                $password = '123456';
            }

            // Hash password untuk keamanan
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            if (!empty($username) && !empty($role)) {
                try {
                    // Cek jika username sudah ada
                    $check_sql = "SELECT id FROM users WHERE username = :username";
                    $check_stmt = $pdo->prepare($check_sql);
                    $check_stmt->execute(['username' => $username]);

                    if ($check_stmt->rowCount() > 0) {
                        $_SESSION['message'] = 'Username sudah digunakan. Silakan pilih username lain.';
                        header("Location: " . BASE_URL . "pages/pengguna_tambah.php");
                        exit();
                    }

                    $sql = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        'username' => $username,
                        'password' => $hashed_password,
                        'role' => $role
                    ]);

                    $_SESSION['message'] = 'Pengguna baru berhasil ditambahkan.';
                    header("Location: " . BASE_URL . "pages/pengguna.php");
                    exit();
                } catch (PDOException $e) {
                    die("Error: " . $e->getMessage());
                }
            } else {
                die("Username dan role tidak boleh kosong.");
            }
        }
        break;

    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $role = $_POST['role'] ?? null;

            if (empty($id) || empty($username)) {
                die("ID atau Username tidak boleh kosong.");
            }

            try {
                $check_sql = "SELECT id FROM users WHERE username = :username AND id != :id";
                $check_stmt = $pdo->prepare($check_sql);
                $check_stmt->execute(['username' => $username, 'id' => $id]);
                if ($check_stmt->rowCount() > 0) {
                    $_SESSION['message'] = 'Username sudah digunakan.';
                    header("Location: " . BASE_URL . "pages/pengguna_edit.php?id=" . $id);
                    exit();
                }

                $params = [
                    ':username' => $username,
                    ':id' => $id
                ];

                $sql_parts = ["username = :username"];

                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $sql_parts[] = "password = :password";
                    $params[':password'] = $hashed_password;
                }

                if ($role !== null) {
                    $sql_parts[] = "role = :role";
                    $params[':role'] = $role;
                }

                $sql = "UPDATE users SET " . implode(", ", $sql_parts) . " WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                $_SESSION['message'] = 'Data pengguna berhasil diperbarui.';
                header("Location: " . BASE_URL . "pages/pengguna.php");
                exit();

            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }
        }
        break;

    case 'delete':
        $id = $_GET['id'] ?? null;
        if ($id && $id != $_SESSION['id']) {
            try {
                $sql = "DELETE FROM users WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['id' => $id]);

                $_SESSION['message'] = 'Pengguna berhasil dihapus.';
                header("Location: " . BASE_URL . "pages/pengguna.php");
                exit();
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }
        } else {
            $_SESSION['message'] = 'Gagal: Anda tidak dapat menghapus akun Anda sendiri atau ID tidak valid.';
            header("Location: " . BASE_URL . "pages/pengguna.php");
            exit();
        }
        break;

    default:
        header("Location: " . BASE_URL . "pages/pengguna.php");
        exit();
}
?>