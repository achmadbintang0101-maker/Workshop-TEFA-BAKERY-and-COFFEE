<?php
class Database {
    // Property dengan Type Hinting 'string'
    private string $host = "localhost";
    private string $user = "root";
    private string $pass = "";
    private string $db_name = "pos_bread_coffee"; 

    // Gunakan '?mysqli' (Tanda tanya berarti variabel ini boleh diisi objek koneksi ATAU dibiarkan kosong/null terlebih dahulu)
    public ?mysqli $conn = null;

    // Method untuk mendapatkan koneksi
    public function getConnection() {
        $this->conn = null; // Kosongkan dulu
        
        // Coba lakukan koneksi
        $this->conn = mysqli_connect($this->host, $this->user, $this->pass, $this->db_name);
        
        if (!$this->conn) {
            die("Koneksi Database Gagal: " . mysqli_connect_error());
        }

        // Set charset
        mysqli_set_charset($this->conn, "utf8mb4");

        return $this->conn;
    }
}
?>