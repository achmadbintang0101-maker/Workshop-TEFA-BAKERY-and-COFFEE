<?php
include '../Config/auth.php';
include '../Config/koneksi.php';

// Pastikan hanya ID yang valid yang diproses
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Jangan biarkan Admin menghapus dirinya sendiri (keamanan dasar)
    if($id == $_SESSION['id_user']) {
        echo "<script>alert('Anda tidak bisa menghapus akun sendiri!'); window.location='manajemen_staff.php';</script>";
        exit;
    }

    $query = "DELETE FROM users WHERE id_user = '$id'";
    if(mysqli_query($conn, $query)) {
        echo "<script>alert('Staff berhasil dihapus!'); window.location='manajemen_staff.php';</script>";
    }
}
?>