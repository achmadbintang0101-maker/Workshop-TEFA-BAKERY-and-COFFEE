<?php
// Memulai session
session_start();

// Memanggil file koneksi 
include 'Classes/Database.php'; 

// Bangun Object Database
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $query  = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (password_verify($password, $row['password'])) {
            // Jika COCOK, buat Session
            $_SESSION['id_user']  = $row['id_user'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama']     = $row['nama'];
            $_SESSION['role']     = $row['role']; 
            $_SESSION['status']   = "login";

            if ($row['role'] == "admin") {
                header("location: admin/dashboard.php");
            } else if ($row['role'] == "kasir") {
                header("location: Kasir/index.php"); 
            } else {
                header("location: Index.php?error=role");
            }
            exit;
        } else {
            // PERBAIKAN: Lempar parameter error sandi & simpan email yang diketik
            header("Location: Index.php?error=sandi&email=" . urlencode($email));
            exit;
        }
    } else {
        mysqli_stmt_close($stmt);
        // PERBAIKAN: Lempar parameter error email & simpan email yang diketik
        header("Location: Index.php?error=email&email=" . urlencode($email));
        exit;
    }
} else {
    header("location: Index.php");
    exit;
}
?>