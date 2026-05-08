<?php
class Product {
    private mysqli $conn;

    public function __construct(mysqli $db_connection) {
        $this->conn = $db_connection;
    }

    // 1. READ: Tampilkan Semua Produk (Digabung dengan tabel categories)
    public function getAllProducts() {
        $query = "SELECT p.*, c.category_name as category 
                  FROM products p 
                  LEFT JOIN categories c ON p.id_category = c.id_category 
                  ORDER BY p.id_product DESC";
        return mysqli_query($this->conn, $query);
    }

    // 1.BREAD KHUSUS KASIR: Tampilkan Produk yang stoknya > 0 (Digabung dengan tabel categories)
    public function getAvailableProducts() {
        $query = "SELECT p.*, c.category_name as category 
                  FROM products p 
                  LEFT JOIN categories c ON p.id_category = c.id_category 
                  WHERE p.stok > 0 
                  ORDER BY c.category_name ASC";
        return mysqli_query($this->conn, $query);
    }
    
    // 2. CREATE: Tambah Produk Baru 
    public function createProduct(string $nama, int $id_category, string $gambar, int $stok, int $harga, int $id_user) {
        // Kolom category diganti id_category
        $query = "INSERT INTO products (name, id_category, image, stok, price, id_user) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $query);
        
        // "sisiii" artinya: string (nama), integer (id_category), string (gambar), integer (stok), integer (harga), integer (id_user)
        mysqli_stmt_bind_param($stmt, "sisiii", $nama, $id_category, $gambar, $stok, $harga, $id_user);
        
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt); 
        
        return $berhasil;
    }

    // 3. UPDATE: Edit Data Produk 
    public function updateProduct(int $id_product, string $nama, int $id_category, int $stok, int $harga) {
        // Kolom category diganti id_category
        $query = "UPDATE products SET name=?, id_category=?, stok=?, price=? WHERE id_product=?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        
        // "siiii" -> string (nama), integer (id_category), integer (stok), integer (harga), integer (id_product)
        mysqli_stmt_bind_param($stmt, "siiii", $nama, $id_category, $stok, $harga, $id_product);
        
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $berhasil;
    }

    // 4. DELETE: Hapus 
    public function deleteProduct(int $id_product) {
        $query = "DELETE FROM products WHERE id_product=?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        
        // "i" -> integer (id_product)
        mysqli_stmt_bind_param($stmt, "i", $id_product);
        
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $berhasil;
    }
}
?>





























