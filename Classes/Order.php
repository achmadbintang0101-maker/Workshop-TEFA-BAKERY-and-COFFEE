<?php
class Order {
    private mysqli $conn;

    public function __construct(mysqli $db_connection) {
        $this->conn = $db_connection;
    }

    // 1. READ: Ambil Semua Data Pesanan
    public function getAllOrders() {
        $query = "SELECT 
                    t.id_transaction as id,
                    t.queue_number,
                    t.created_at as tanggal,
                    t.status,
                    c.nama_customer,
                    c.role_customer,
                    (SELECT SUM(qty) FROM transaction_details td WHERE td.id_transaction = t.id_transaction) as jumlah_item
                  FROM transactions t
                  LEFT JOIN customers c ON t.id_customer = c.id_customer
                  ORDER BY t.created_at DESC";
        
        return mysqli_query($this->conn, $query);
    }

    // 2. READ: Ambil Detail Spesifik 1 Pesanan
    public function getOrderDetails(int $id_transaction) {
        $query = "SELECT td.*, p.name 
                  FROM transaction_details td
                  JOIN products p ON td.id_product = p.id_product
                  WHERE td.id_transaction = ?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id_transaction);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    // =========================================================
    // 3. UPDATE: (DIPERBAIKI) Ubah status jadi 'selesai' & Potong Stok!
    // =========================================================
    public function markOrderAsDone(int $id_transaction) {
        // Cek status saat ini dulu untuk mencegah pemotongan stok ganda
        $cek_query = mysqli_query($this->conn, "SELECT status FROM transactions WHERE id_transaction = $id_transaction");
        $cek_data = mysqli_fetch_assoc($cek_query);
        
        if ($cek_data && $cek_data['status'] === 'selesai') {
            return true; // Jika sudah selesai, abaikan agar stok tidak terpotong 2x
        }

        mysqli_begin_transaction($this->conn);
        try {
            // A. Ambil detail barang untuk memotong stok
            $query_items = "SELECT id_product, qty FROM transaction_details WHERE id_transaction = ?";
            $stmt_items = mysqli_prepare($this->conn, $query_items);
            mysqli_stmt_bind_param($stmt_items, "i", $id_transaction);
            mysqli_stmt_execute($stmt_items);
            $result_items = mysqli_stmt_get_result($stmt_items);

            while ($row = mysqli_fetch_assoc($result_items)) {
                $id_p = $row['id_product'];
                $qty = $row['qty'];
                // Eksekusi potong stok
                mysqli_query($this->conn, "UPDATE products SET stok = stok - $qty WHERE id_product = $id_p");
            }
            mysqli_stmt_close($stmt_items);

            // B. Ubah status transaksi menjadi selesai
            $query_update = "UPDATE transactions SET status = 'selesai' WHERE id_transaction = ?";
            $stmt_update = mysqli_prepare($this->conn, $query_update);
            mysqli_stmt_bind_param($stmt_update, "i", $id_transaction);
            mysqli_stmt_execute($stmt_update);
            mysqli_stmt_close($stmt_update);

            mysqli_commit($this->conn);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            error_log("Gagal menyelesaikan order: " . $e->getMessage());
            return false;
        }
    }

    // =========================================================
    // 4. DELETE: (DIPERBAIKI) Hapus Data Pesanan dengan Aman
    // =========================================================
    public function deleteOrder(int $id_transaction) {
        // Hapus 'anak' (details) terlebih dahulu agar tidak error Foreign Key
        mysqli_query($this->conn, "DELETE FROM transaction_details WHERE id_transaction = $id_transaction");
        
        // Baru hapus 'induk' nya (transaksi)
        $query = "DELETE FROM transactions WHERE id_transaction = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id_transaction);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $berhasil;
    }

    // 5. CREATE: Buat Pesanan Manual dari Admin
    public function createManualOrder(string$nama, string$role, string$status, $items) {
        mysqli_begin_transaction($this->conn);
        try {
            // A. Simpan data pelanggan ke tabel customers
            $query_customer = "INSERT INTO customers (nama_customer, role_customer) VALUES (?, ?)";
            $stmt_customer = mysqli_prepare($this->conn, $query_customer);
            mysqli_stmt_bind_param($stmt_customer, "ss", $nama, $role);
            mysqli_stmt_execute($stmt_customer);
            $id_customer = mysqli_insert_id($this->conn);
            mysqli_stmt_close($stmt_customer);

            // B. Hitung Total Pembayaran
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += ($item['price'] * $item['qty']);
            }
            $tax = $subtotal * 0.10; // Pajak 10%
            $grand_total = $subtotal + $tax;
            $queue_number = 'M-' . rand(100, 999); // 'M-' untuk pesanan Manual

            // C. Simpan ke tabel transactions
            $query_trans = "INSERT INTO transactions (id_customer, queue_number, total_price, tax, grand_total, status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_trans = mysqli_prepare($this->conn, $query_trans);
            mysqli_stmt_bind_param($stmt_trans, "isddds", $id_customer, $queue_number, $subtotal, $tax, $grand_total, $status);
            mysqli_stmt_execute($stmt_trans);
            $id_transaction = mysqli_insert_id($this->conn);
            mysqli_stmt_close($stmt_trans);

            // D. Simpan rincian barang ke transaction_details
            $query_detail = "INSERT INTO transaction_details (id_transaction, id_product, qty, price_at_time, subtotal) VALUES (?, ?, ?, ?, ?)";
            $stmt_detail = mysqli_prepare($this->conn, $query_detail);

            foreach ($items as $item) {
                $id_product = (int)$item['id'];
                $qty = (int)$item['qty'];
                $price = (float)$item['price'];
                $item_subtotal = $price * $qty;

                mysqli_stmt_bind_param($stmt_detail, "iiidd", $id_transaction, $id_product, $qty, $price, $item_subtotal);
                mysqli_stmt_execute($stmt_detail);

                // E. Jika status langsung LUNAS (Selesai), potong stok produk
                if ($status === 'selesai') {
                    mysqli_query($this->conn, "UPDATE products SET stok = stok - $qty WHERE id_product = $id_product");
                }
            }
            mysqli_stmt_close($stmt_detail);

            mysqli_commit($this->conn);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            error_log("Gagal buat pesanan manual: " . $e->getMessage());
            return false;
        }
    }
}
?>