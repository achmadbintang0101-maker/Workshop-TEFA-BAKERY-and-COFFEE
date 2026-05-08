<?php
class User {
    private mysqli $conn;

    public function __construct(mysqli $db_connection) {
        $this->conn = $db_connection;
    }

    // --- READ: Tampilkan Semua Staff ---
    public function getAllUsers() {
        $query = "SELECT id_user, nama, email, role FROM users ORDER BY role ASC";
        return mysqli_query($this->conn, $query);
    }

    // --- CREATE: Tambah Staff Baru ---
    public function createUser(string $username, string $nama, string $email, string $password, string $role) {
        // Enkripsi Password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Cek apakah email sudah ada
        $stmt_check = mysqli_prepare($this->conn, "SELECT id_user FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            mysqli_stmt_close($stmt_check);
            return "email_exists"; // Kembalikan kode error
        }
        mysqli_stmt_close($stmt_check);

        // Jika email belum ada, lanjutkan insert
        $query = "INSERT INTO users (username, nama, email, password, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "sssss", $username, $nama, $email, $password_hash, $role);
        
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $berhasil ? "success" : "fail";
    }

    // --- DELETE: Hapus Staff ---
    public function deleteUser(int $id_user, int $current_user_id) {
        // Keamanan: Jangan biarkan Admin menghapus dirinya sendiri
        if ($id_user === $current_user_id) {
            return "self_delete";
        }

        $query = "DELETE FROM users WHERE id_user = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id_user);
        
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $berhasil ? "success" : "fail";
    }
}
?>