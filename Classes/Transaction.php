<?php
class Transaction {
    private mysqli $conn;

    public function __construct(mysqli $db_connection) {
        $this->conn = $db_connection;
    }

    // Mengambil data induk transaksi & nama kasir
    public function getTransactionById(int $id) {
        $query = "SELECT t.*, u.nama as nama_kasir 
                  FROM transactions t 
                  JOIN users u ON t.id_user = u.id_user 
                  WHERE t.id_transaction = ?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }

    // Mengambil rincian barang yang dibeli di transaksi tersebut
    public function getTransactionDetails(int $id) {
        $query = "SELECT td.*, p.name 
                  FROM transaction_details td 
                  JOIN products p ON p.id_product = td.id_product 
                  WHERE td.id_transaction = ?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        
        return mysqli_stmt_get_result($stmt);
    }

    // Method KHUSUS untuk Customer Checkout
 public function createCustomerTransaction($nama, $role, $queue_number, $total, $tax, $grand_total, $items) {
        mysqli_begin_transaction($this->conn);

        try {
            // 1. Simpan data pelanggan ke tabel customers
            $query_customer = "INSERT INTO customers (nama_customer, role_customer) VALUES (?, ?)";
            $stmt_customer = mysqli_prepare($this->conn, $query_customer);
            mysqli_stmt_bind_param($stmt_customer, "ss", $nama, $role);
            mysqli_stmt_execute($stmt_customer);
            
            // Ambil ID customer yang baru dibuat
            $id_customer = mysqli_insert_id($this->conn);
            mysqli_stmt_close($stmt_customer);

            // 2. Simpan ke tabel transactions dengan id_customer
            $query_trans = "INSERT INTO transactions (id_customer, queue_number, total_price, tax, grand_total, status) 
                            VALUES (?, ?, ?, ?, ?, 'pending')";
            
            $stmt = mysqli_prepare($this->conn, $query_trans);
            mysqli_stmt_bind_param($stmt, "isddd", $id_customer, $queue_number, $total, $tax, $grand_total);
            mysqli_stmt_execute($stmt);

            // Ambil ID transaksi yang baru dibuat
            $id_transaction = mysqli_insert_id($this->conn);
            mysqli_stmt_close($stmt);

            // 3. Simpan setiap barang ke transaction_details
            $query_detail = "INSERT INTO transaction_details (id_transaction, id_product, qty, price_at_time, subtotal) 
                             VALUES (?, ?, ?, ?, ?)";
            $stmt_detail = mysqli_prepare($this->conn, $query_detail);

            foreach ($items as $item) {
                $id_product = (int)$item['id'];
                $qty = (int)$item['qty'];
                $price = (float)$item['price'];
                $subtotal_item = $qty * $price;

                mysqli_stmt_bind_param($stmt_detail, "iiidd", $id_transaction, $id_product, $qty, $price, $subtotal_item);
                mysqli_stmt_execute($stmt_detail);
            }
            mysqli_stmt_close($stmt_detail);

            mysqli_commit($this->conn);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            error_log("Gagal checkout: " . $e->getMessage());
            return false;
        }
    }

    // ==============================================================
    // FUNGSI KHUSUS UNTUK API KASIR (NOTIFIKASI & KONFIRMASI)
    // ==============================================================

    // 1. Ambil semua pesanan yang statusnya masih 'pending'
    public function getPendingOrders() {
        $query = "SELECT * FROM transactions WHERE status = 'pending' AND queue_number IS NOT NULL ORDER BY created_at ASC";
        return mysqli_query($this->conn, $query);
    }

 // 2. Ambil data spesifik 1 pesanan beserta barang belanjaannya
    public function getOrderWithDetails(int $id_transaction) {
        // Ambil Data Induk (SEKARANG DI-JOIN DENGAN CUSTOMERS)
        $query_main = "SELECT t.*, c.nama_customer, c.role_customer 
                       FROM transactions t 
                       LEFT JOIN customers c ON t.id_customer = c.id_customer 
                       WHERE t.id_transaction = ?";
        $stmt_main = mysqli_prepare($this->conn, $query_main);
        mysqli_stmt_bind_param($stmt_main, "i", $id_transaction);
        mysqli_stmt_execute($stmt_main);
        $main_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_main));
        mysqli_stmt_close($stmt_main);

        if (!$main_data) return null;

        // Ambil Detail Barang
        $query_items = "SELECT td.*, p.name as product_name FROM transaction_details td 
                        JOIN products p ON p.id_product = td.id_product 
                        WHERE td.id_transaction = ?";
        $stmt_items = mysqli_prepare($this->conn, $query_items);
        mysqli_stmt_bind_param($stmt_items, "i", $id_transaction);
        mysqli_stmt_execute($stmt_items);
        $result_items = mysqli_stmt_get_result($stmt_items);
        
        $items = [];
        while ($row = mysqli_fetch_assoc($result_items)) {
            $items[] = $row;
        }
        mysqli_stmt_close($stmt_items);

        $main_data['items'] = $items;
        return $main_data;
    }

    // 3. Konfirmasi Pesanan: Ubah status jadi 'selesai' & Potong Stok
    public function confirmAndReduceStock(int $id_transaction) {
        mysqli_begin_transaction($this->conn);
        try {
            // Ambil detail barang untuk memotong stok
            $query_items = "SELECT id_product, qty FROM transaction_details WHERE id_transaction = ?";
            $stmt_items = mysqli_prepare($this->conn, $query_items);
            mysqli_stmt_bind_param($stmt_items, "i", $id_transaction);
            mysqli_stmt_execute($stmt_items);
            $result = mysqli_stmt_get_result($stmt_items);

            while ($row = mysqli_fetch_assoc($result)) {
                $id_p = $row['id_product'];
                $qty = $row['qty'];
                // Potong stok
                mysqli_query($this->conn, "UPDATE products SET stok = stok - $qty WHERE id_product = $id_p");
            }
            mysqli_stmt_close($stmt_items);

            // Ubah status transaksi menjadi selesai
            $query_update = "UPDATE transactions SET status = 'selesai' WHERE id_transaction = ?";
            $stmt_update = mysqli_prepare($this->conn, $query_update);
            mysqli_stmt_bind_param($stmt_update, "i", $id_transaction);
            mysqli_stmt_execute($stmt_update);
            mysqli_stmt_close($stmt_update);

            mysqli_commit($this->conn);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            return false;
        }
    }
}
?>