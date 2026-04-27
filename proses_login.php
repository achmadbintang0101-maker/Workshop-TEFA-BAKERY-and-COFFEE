<?php
// Memulai session
session_start();

// Memanggil file koneksi 
include 'Config/koneksi.php';

// Mengecek apakah file ini diakses melalui form (metode POST)
// Mencegah akses langsung via pengetikan URL (metode GET)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Menangkap inputan dari form
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    // Mencari user berdasarkan email dan role yang dipilih
    $query  = "SELECT * FROM users WHERE email = '$email' AND role = '$role'";
    $result = mysqli_query($conn, $query);

    // Jika email dan role ditemukan di database
    if (mysqli_num_rows($result) === 1) {
        
        $row = mysqli_fetch_assoc($result);

        // Memeriksa kecocokan password ketikan dengan password hash di database
        if (password_verify($password, $row['password'])) {
            
            // Jika COCOK, buat Session
            $_SESSION['id_user']  = $row['id_user'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama']     = $row['nama'];
            $_SESSION['role']     = $row['role'];
            $_SESSION['status']   = "login";

            // Arahkan ke halaman masing-masing sesuai role
            if ($row['role'] == "admin") {
                header("location: admin/dashboard.php");
            } else {
                // Pastikan penulisan folder Kasir sesuai huruf besar/kecil di foldermu
                header("location: Kasir/index.php"); 
            }
            exit;
            
        } else {
            // Jika PASSWORD SALAH, lempar kembali ke landing page dengan pesan gagal
            header("location: Index.php?pesan=gagal");
            exit;
        }
        
    } else {
        // Jika EMAIL / ROLE TIDAK DITEMUKAN, lempar kembali ke landing page
        header("location: Index.php?pesan=gagal");
        exit;
    }
    
} else {
    // Jika file ini diakses langsung (diketik manual di URL), tendang kembali ke Index
    header("location: Index.php");
    exit;
}
?>