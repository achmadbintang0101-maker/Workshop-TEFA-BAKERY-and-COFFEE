<?php
class Finance {
    private mysqli $conn;

    public function __construct(mysqli $db_connection) {
        $this->conn = $db_connection;
    }

    // 1. MENGAMBIL DATA GABUNGAN (Penjualan Otomatis + Manual)
    public function getAllFinancialRecords() {
        $query = "
            (SELECT 
                id_transaction as id,
                created_at as tanggal, 
                'Pemasukan' as jenis, 
                CONCAT('Penjualan - ', IFNULL(queue_number, 'Kasir')) as keterangan, 
                grand_total as nominal, 
                'Selesai' as status,
                'auto' as source
            FROM transactions 
            WHERE status = 'selesai')
            
            UNION ALL
            
            (SELECT 
                id_finance as id,
                tanggal, 
                jenis, 
                keterangan, 
                nominal, 
                status,
                'manual' as source
            FROM finances)
            
            ORDER BY tanggal DESC";
        
        return mysqli_query($this->conn, $query);
    }

// 2. MENGHITUNG RINGKASAN (REAL-TIME SESUAI BULAN & TAHUN)
    public function getFinancialSummary() {
        
        // ==========================================
        // A. TOTAL SALDO (DI-RESET PER TAHUN)
        // ==========================================
        $query_saldo_in = "SELECT SUM(nominal) as total FROM (
                        SELECT grand_total as nominal FROM transactions WHERE status = 'selesai' AND YEAR(created_at) = YEAR(CURDATE())
                        UNION ALL
                        SELECT nominal FROM finances WHERE jenis = 'Pemasukan' AND status = 'Selesai' AND YEAR(tanggal) = YEAR(CURDATE())
                    ) as combined_saldo_in";
        $res_saldo_in = mysqli_query($this->conn, $query_saldo_in);
        $saldo_in = mysqli_fetch_assoc($res_saldo_in)['total'] ?? 0;

        $query_saldo_out = "SELECT SUM(nominal) as total FROM finances WHERE jenis = 'Penarikan' AND status = 'Selesai' AND YEAR(tanggal) = YEAR(CURDATE())";
        $res_saldo_out = mysqli_query($this->conn, $query_saldo_out);
        $saldo_out = mysqli_fetch_assoc($res_saldo_out)['total'] ?? 0;
        
        $total_saldo = $saldo_in - $saldo_out;

        // ==========================================
        // B. TOTAL PEMASUKAN (DI-RESET PER BULAN)
        // ==========================================
        $query_in_bulan = "SELECT SUM(nominal) as total FROM (
                        SELECT grand_total as nominal FROM transactions WHERE status = 'selesai' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())
                        UNION ALL
                        SELECT nominal FROM finances WHERE jenis = 'Pemasukan' AND status = 'Selesai' AND MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())
                    ) as combined_in_bulan";
        $res_in_bulan = mysqli_query($this->conn, $query_in_bulan);
        $pemasukan_bulan = mysqli_fetch_assoc($res_in_bulan)['total'] ?? 0;

        // ==========================================
        // C. TOTAL PENARIKAN (DI-RESET PER BULAN)
        // ==========================================
        $query_out_bulan = "SELECT SUM(nominal) as total FROM finances WHERE jenis = 'Penarikan' AND status = 'Selesai' AND MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())";
        $res_out_bulan = mysqli_query($this->conn, $query_out_bulan);
        $penarikan_bulan = mysqli_fetch_assoc($res_out_bulan)['total'] ?? 0;

        // KEMBALIKAN DATA KE JAVASCRIPT
        return [
            'saldo' => $total_saldo,
            'pemasukan' => $pemasukan_bulan,
            'penarikan' => $penarikan_bulan
        ];
    }   

    // 3. MENAMBAH DATA MANUAL
    public function createManualFinance($tanggal, $jenis, $keterangan, $nominal, $status) {
        $query = "INSERT INTO finances (tanggal, jenis, keterangan, nominal, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $query);
        
        // PERBAIKAN diubah menjadi "sssds" (String, String, String, Double, String)
        mysqli_stmt_bind_param($stmt, "sssds", $tanggal, $jenis, $keterangan, $nominal, $status);
        
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    // 4. MENGHAPUS DATA MANUAL
    public function deleteManualFinance($id) {
        $query = "DELETE FROM finances WHERE id_finance = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
}
?>