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
    // [BARU] CEK STOK: Dipakai sebelum input manual disimpan
    // Mengembalikan array produk yang stoknya TIDAK MENCUKUPI
    // =========================================================
    public function validateStok(array $items): array {
        $kekurangan = [];

        $stmt = mysqli_prepare($this->conn, "SELECT id_product, name, stok FROM products WHERE id_product = ?");

        foreach ($items as $item) {
            $id_product = (int)$item['id'];
            $qty_diminta = (int)$item['qty'];

            mysqli_stmt_bind_param($stmt, "i", $id_product);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $produk  = mysqli_fetch_assoc($result);

            if ($produk && $produk['stok'] < $qty_diminta) {
                $kekurangan[] = [
                    'nama'       => $produk['name'],
                    'stok_ada'   => (int)$produk['stok'],
                    'qty_diminta'=> $qty_diminta
                ];
            }
        }

        mysqli_stmt_close($stmt);
        return $kekurangan; // Kosong = semua stok cukup
    }

    // =========================================================
    // 3. UPDATE: Ubah status jadi 'selesai' & Potong Stok
    // FIX: Semua query pakai prepared statement + cek stok negatif
    // =========================================================
    public function markOrderAsDone(int $id_transaction) {
        // Cek status saat ini untuk mencegah pemotongan stok ganda
        $stmt_cek = mysqli_prepare($this->conn, "SELECT status FROM transactions WHERE id_transaction = ?");
        mysqli_stmt_bind_param($stmt_cek, "i", $id_transaction);
        mysqli_stmt_execute($stmt_cek);
        $cek_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_cek));
        mysqli_stmt_close($stmt_cek);

        if ($cek_data && $cek_data['status'] === 'selesai') {
            return true; // Sudah selesai, tidak perlu potong stok lagi
        }

        mysqli_begin_transaction($this->conn);
        try {
            // A. Ambil detail barang untuk memotong stok
            $stmt_items = mysqli_prepare($this->conn, "SELECT id_product, qty FROM transaction_details WHERE id_transaction = ?");
            mysqli_stmt_bind_param($stmt_items, "i", $id_transaction);
            mysqli_stmt_execute($stmt_items);
            $result_items = mysqli_stmt_get_result($stmt_items);

            while ($row = mysqli_fetch_assoc($result_items)) {
                $id_p = (int)$row['id_product'];
                $qty  = (int)$row['qty'];

                // FIX: Prepared statement + cek stok tidak jadi negatif
                $stmt_stok = mysqli_prepare($this->conn, "UPDATE products SET stok = stok - ? WHERE id_product = ? AND stok >= ?");
                mysqli_stmt_bind_param($stmt_stok, "iii", $qty, $id_p, $qty);
                mysqli_stmt_execute($stmt_stok);

                if (mysqli_stmt_affected_rows($stmt_stok) == 0) {
                    throw new Exception("Stok tidak mencukupi untuk id_product: $id_p");
                }
                mysqli_stmt_close($stmt_stok);
            }
            mysqli_stmt_close($stmt_items);

            // B. Ubah status transaksi menjadi selesai
            $stmt_update = mysqli_prepare($this->conn, "UPDATE transactions SET status = 'selesai' WHERE id_transaction = ?");
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
    // 4. DELETE: Hapus Data Pesanan dengan Aman
    // FIX: Semua query pakai prepared statement
    // =========================================================
    public function deleteOrder(int $id_transaction) {
        mysqli_begin_transaction($this->conn);
        try {
            // Hapus detail dulu (CASCADE sudah ada di FK, tapi eksplisit lebih aman)
            $stmt_detail = mysqli_prepare($this->conn, "DELETE FROM transaction_details WHERE id_transaction = ?");
            mysqli_stmt_bind_param($stmt_detail, "i", $id_transaction);
            mysqli_stmt_execute($stmt_detail);
            mysqli_stmt_close($stmt_detail);

            $stmt_trans = mysqli_prepare($this->conn, "DELETE FROM transactions WHERE id_transaction = ?");
            mysqli_stmt_bind_param($stmt_trans, "i", $id_transaction);
            $berhasil = mysqli_stmt_execute($stmt_trans);
            mysqli_stmt_close($stmt_trans);

            mysqli_commit($this->conn);
            return $berhasil;

        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            error_log("Gagal menghapus order: " . $e->getMessage());
            return false;
        }
    }

    // =========================================================
    // 5. CREATE: Buat Pesanan Manual dari Admin
    // FIX: Ada validasi stok SEBELUM insert + prepared statement
    // =========================================================
    public function createManualOrder(string $nama, string $role, string $status, array $items) {
        mysqli_begin_transaction($this->conn);
        try {
            // [BARU] Validasi stok terlebih dahulu jika status langsung 'selesai'
            // Untuk status 'pending', stok dipotong nanti saat dikonfirmasi selesai
            if ($status === 'selesai') {
                $kekurangan = $this->validateStok($items);
                if (!empty($kekurangan)) {
                    $pesan = implode(', ', array_map(fn($k) => 
                        "{$k['nama']} (ada: {$k['stok_ada']}, diminta: {$k['qty_diminta']})", 
                        $kekurangan
                    ));
                    throw new Exception("STOK_KURANG: $pesan");
                }
            }

            // A. Simpan data pelanggan ke tabel customers
            $stmt_customer = mysqli_prepare($this->conn, "INSERT INTO customers (nama_customer, role_customer) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt_customer, "ss", $nama, $role);
            mysqli_stmt_execute($stmt_customer);
            $id_customer = mysqli_insert_id($this->conn);
            mysqli_stmt_close($stmt_customer);

            // B. Hitung Total Pembayaran
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += ((float)$item['price'] * (int)$item['qty']);
            }
            $tax         = $subtotal * 0.10;
            $grand_total = $subtotal + $tax;

            // FIX: Nomor antrean pakai timestamp agar tidak duplikat
            $queue_number = 'M-' . date('His') . rand(10, 99);

            // C. Simpan ke tabel transactions
            $stmt_trans = mysqli_prepare($this->conn, "INSERT INTO transactions (id_customer, queue_number, total_price, tax, grand_total, status) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_trans, "isddds", $id_customer, $queue_number, $subtotal, $tax, $grand_total, $status);
            mysqli_stmt_execute($stmt_trans);
            $id_transaction = mysqli_insert_id($this->conn);
            mysqli_stmt_close($stmt_trans);

            // D. Simpan rincian barang ke transaction_details
            $stmt_detail = mysqli_prepare($this->conn, "INSERT INTO transaction_details (id_transaction, id_product, qty, price_at_time, subtotal) VALUES (?, ?, ?, ?, ?)");

            foreach ($items as $item) {
                $id_product    = (int)$item['id'];
                $qty           = (int)$item['qty'];
                $price         = (float)$item['price'];
                $item_subtotal = $price * $qty;

                mysqli_stmt_bind_param($stmt_detail, "iiidd", $id_transaction, $id_product, $qty, $price, $item_subtotal);
                mysqli_stmt_execute($stmt_detail);

                // E. Jika status langsung LUNAS, potong stok (sudah dicek di atas)
                if ($status === 'selesai') {
                    // FIX: Prepared statement dengan double-check stok agar tidak minus
                    $stmt_stok = mysqli_prepare($this->conn, "UPDATE products SET stok = stok - ? WHERE id_product = ? AND stok >= ?");
                    mysqli_stmt_bind_param($stmt_stok, "iii", $qty, $id_product, $qty);
                    mysqli_stmt_execute($stmt_stok);

                    if (mysqli_stmt_affected_rows($stmt_stok) == 0) {
                        throw new Exception("STOK_KURANG: Stok berubah saat proses berlangsung untuk id_product: $id_product");
                    }
                    mysqli_stmt_close($stmt_stok);
                }
            }
            mysqli_stmt_close($stmt_detail);

            mysqli_commit($this->conn);
            return ['success' => true];

        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            $msg = $e->getMessage();
            error_log("Gagal buat pesanan manual: $msg");

            // Kembalikan pesan stok kurang agar bisa ditampilkan ke admin
            if (str_starts_with($msg, 'STOK_KURANG:')) {
                return ['success' => false, 'message' => str_replace('STOK_KURANG: ', '', $msg)];
            }
            return ['success' => false, 'message' => 'Gagal menyimpan pesanan ke database.'];
        }
    }
}
?>