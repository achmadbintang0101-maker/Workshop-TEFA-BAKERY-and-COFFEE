<?php
// 1. Mulai sesi jika belum ada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ==============================================================================
// 2. KODE SAKTI ANTI-CACHE (Mencegah user menekan tombol Back setelah Logout)
// ==============================================================================
header("Cache-Control: no-cache, no-store, must-revalidate"); // Standar HTTP 1.1
header("Pragma: no-cache"); // Standar HTTP 1.0
header("Expires: 0"); // Proxies

// 3. Pengecekan Login
// Jika variabel session belum diset ATAU statusnya bukan "login"
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    // Melempar user kembali ke halaman utama (Index.php) jika belum login
    header("location: ../Index.php?pesan=belum_login");
    exit;
}
?>