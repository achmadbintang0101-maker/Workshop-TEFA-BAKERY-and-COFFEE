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

    // 2. MENGHITUNG RINGKASAN
    public function getFinancialSummary() {
        // Total Pemasukan (Otomatis + Manual)
        $query_in = "SELECT SUM(nominal) as total FROM (
                        SELECT grand_total as nominal FROM transactions WHERE status = 'selesai'
                        UNION ALL
                        SELECT nominal FROM finances WHERE jenis = 'Pemasukan' AND status = 'Selesai'
                    ) as combined_in";
        $res_in = mysqli_query($this->conn, $query_in);
        $total_in = mysqli_fetch_assoc($res_in)['total'] ?? 0;

        // Total Penarikan
        $query_out = "SELECT SUM(nominal) as total FROM finances WHERE jenis = 'Penarikan' AND status = 'Selesai'";
        $res_out = mysqli_query($this->conn, $query_out);
        $total_out = mysqli_fetch_assoc($res_out)['total'] ?? 0;

        return [
            'pemasukan' => $total_in,
            'penarikan' => $total_out,
            'saldo' => $total_in - $total_out
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