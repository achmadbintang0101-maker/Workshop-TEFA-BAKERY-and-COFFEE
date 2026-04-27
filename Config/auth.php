<?php
session_start();
if ($_SESSION['status'] != "login") {
    // Melempar user kembali ke halaman utama (Index.php) jika belum login
    header("location: ../Index.php?pesan=belum_login");
    exit;
}
?>