<?php
// Memulai session
session_start();

// Memanggil file koneksi 
include 'Classes/Database.php'; 

// Bangun Object Database
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Menangkap inputan dari form (Hanya Email & Password)
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // 2. Mencari user berdasarkan email SAJA
    $query  = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    // Jika email ditemukan di database
    if (mysqli_num_rows($result) === 1) {
        
        $row = mysqli_fetch_assoc($result);

        // 3. Memeriksa kecocokan password
        if (password_verify($password, $row['password'])) {
            
            // Jika COCOK, buat Session
            $_SESSION['id_user']  = $row['id_user'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama']     = $row['nama'];
            
            // 4. Ambil role murni dari Database (Single Source of Truth)
            $_SESSION['role']     = $row['role']; 
            $_SESSION['status']   = "login";

            // 5. Routing Otomatis: Arahkan ke halaman masing-masing sesuai role dari DB
            if ($row['role'] == "admin") {
                header("location: admin/dashboard.php");
            } else if ($row['role'] == "kasir") {
                header("location: Kasir/index.php"); 
            } else {
                // Jika role tidak dikenali (mencegah error)
                header("location: Index.php?pesan=gagal");
            }
            exit;
            
        } else {
            // Jika PASSWORD SALAH
            header("location: Index.php?pesan=gagal");
            exit;
        }
        
    } else {
        // Jika EMAIL TIDAK DITEMUKAN
        header("location: Index.php?pesan=gagal");
        exit;
    }
    
} else {
    // Jika diakses manual via URL
    header("location: Index.php");
    exit;
}
?>