<?php
session_start();

include '../Config/auth.php';
include '../Classes/Database.php';
include '../Classes/User.php';

$database = new Database();
$conn = $database->getConnection();
$userObj = new User($conn);

// ==========================================
// A. LOGIKA TAMBAH STAFF
// ==========================================
if (isset($_POST['simpan_staff'])) {
    $username = (string)($_POST['username'] ?? '');
    $nama     = (string)($_POST['nama'] ?? '');
    $email    = (string)($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $role     = (string)($_POST['role'] ?? 'kasir');

    $result = $userObj->createUser($username, $nama, $email, $password, $role);

    if ($result === "success") {
        echo "<script>alert('Staff baru berhasil didaftarkan!'); window.location='../admin/manajemen_staff.php';</script>";
    } elseif ($result === "email_exists") {
        echo "<script>alert('Error: Email sudah digunakan staff lain!'); window.location='../admin/manajemen_staff.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan staff!'); window.location='../admin/manajemen_staff.php';</script>";
    }
}

// ==========================================
// B. LOGIKA HAPUS STAFF
// ==========================================
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id_user = (int)($_GET['id'] ?? 0);
    $current_user_id = (int)($_SESSION['id_user']);
    
    if ($id_user > 0) {
        $result = $userObj->deleteUser($id_user, $current_user_id);
        
        if ($result === "success") {
            echo "<script>alert('Staff berhasil dihapus!'); window.location='../admin/manajemen_staff.php';</script>";
        } elseif ($result === "self_delete") {
            echo "<script>alert('Anda tidak bisa menghapus akun sendiri!'); window.location='../admin/manajemen_staff.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus staff!'); window.location='../admin/manajemen_staff.php';</script>";
        }
    }
}
?>