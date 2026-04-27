<?php
$conn = mysqli_connect("localhost", "root", "", "pos_bread_coffee");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>
