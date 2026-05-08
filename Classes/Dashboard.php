<?php
class Dashboard {
    private mysqli $conn;

    public function __construct(mysqli $db_connection) {
        $this->conn = $db_connection;
    }

    // 1. Ambil Ringkasan Statistik
    public function getStats() {
        // A. Total Pendapatan (Penjualan + Pemasukan Manual)
        $query_in_1 = "SELECT SUM(grand_total) as total FROM transactions WHERE status = 'selesai'";
        $query_in_2 = "SELECT SUM(nominal) as total FROM finances WHERE jenis = 'Pemasukan' AND status = 'Selesai'";
        $res_1 = mysqli_query($this->conn, $query_in_1);
        $res_2 = mysqli_query($this->conn, $query_in_2);
        $total_pendapatan = ($res_1 ? (float)mysqli_fetch_assoc($res_1)['total'] : 0) + 
                            ($res_2 ? (float)mysqli_fetch_assoc($res_2)['total'] : 0);

        // B. Total Pesanan Hari Ini (24 Jam)
        $query_pesanan = "SELECT COUNT(id_transaction) as total FROM transactions WHERE DATE(created_at) = CURDATE()";
        $res_pesanan = mysqli_query($this->conn, $query_pesanan);
        $pesanan_hari_ini = $res_pesanan ? (int)mysqli_fetch_assoc($res_pesanan)['total'] : 0;

        // C. Total Stok Tersedia di Inventory
        $query_stok = "SELECT SUM(stok) as total FROM products";
        $res_stok = mysqli_query($this->conn, $query_stok);
        $total_stok = $res_stok ? (int)mysqli_fetch_assoc($res_stok)['total'] : 0;

        return [
            'pendapatan' => $total_pendapatan,
            'pesanan_hari_ini' => $pesanan_hari_ini,
            'total_stok' => $total_stok
        ];
    }

    // 2. Ambil Aktivitas Hari Ini Saja (Reset 24 Jam)
    public function getAktivitasHariIni() {
        $query = "SELECT t.queue_number, c.nama_customer, c.role_customer, t.created_at, t.status
                  FROM transactions t
                  LEFT JOIN customers c ON t.id_customer = c.id_customer
                  WHERE DATE(t.created_at) = CURDATE()
                  ORDER BY t.created_at DESC LIMIT 10";
        return mysqli_query($this->conn, $query);
    }
}
?>