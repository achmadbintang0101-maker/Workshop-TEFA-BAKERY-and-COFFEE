<?php
// =========================================================================
// 1. API UNTUK FRONTEND CUSTOMER (Diletakkan Paling Atas, Tanpa Auth)
// =========================================================================
if (isset($_GET['action']) && $_GET['action'] === 'api_get_products') {
    // Memberitahu browser bahwa response berupa JSON
    header('Content-Type: application/json');

    // Panggil database dan class secara mandiri khusus untuk API ini
    require_once '../Classes/Database.php';
    require_once '../Classes/Product.php';

    $database = new Database();
    $conn = $database->getConnection();
    $productObj = new Product($conn);

    // Memanggil method yang sudah ada di class Product
    $result = $productObj->getAvailableProducts();

    $products_arr = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            
            // Penyesuaian nama kategori DB (bread, snack) ke JS (bakery, snacks)
            $kategori_js = strtolower($row['category']);
            if ($kategori_js === 'bread') {
                $kategori_js = 'bakery';
            } elseif ($kategori_js === 'snack') {
                $kategori_js = 'snacks';
            }

            $products_arr[] = [
                'id' => (int)$row['id_product'],
                'name' => $row['name'],
                'price' => (int)$row['price'],
                // Path gambar dari DB
                'img' => '../Assets/IMG/' . ($row['image'] ? $row['image'] : 'default_product.jpg'),
                'category' => $kategori_js,
                'stock' => (int)$row['stok']
            ];
        }
    }

    // Kembalikan data JSON dan hentikan eksekusi script ke bawah
    echo json_encode($products_arr);
    exit();
}

// =========================================================================
// 2. KODE ADMIN & KASIR (Membutuhkan Login / Auth)
// =========================================================================
// PERBAIKAN: session_start() dihapus karena Config/auth.php sudah memanggilnya

// Panggil perlindungan halaman dan Class OOP
include '../Config/auth.php';
require_once '../Classes/Database.php';
require_once '../Classes/Product.php';

// Bangun Object untuk Admin
$database = new Database();
$conn = $database->getConnection();
$productObj = new Product($conn);

// ==========================================
// A. LOGIKA TAMBAH DATA (CREATE)
// ==========================================
if (isset($_POST['btnSimpan'])) {
    $nama        = (string)($_POST['nama'] ?? '');
    // PERBAIKAN: Tangkap nilai kategori sebagai INT (Angka) dari Form Admin
    $id_category = (int)($_POST['kategori'] ?? 0); 
    $stok        = (int)($_POST['stok'] ?? 0);
    $harga       = (int)str_replace(['.', ','], '', $_POST['harga'] ?? '0');
    $id_user     = (int)($_SESSION['id_user'] ?? 1); 

    // Handle Upload Gambar
    $nama_gambar = "default_product.jpg";
    if ($_FILES['gambar']['name'] != "") {
        $ekstensi = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $nama_gambar = time() . "_" . uniqid() . "." . $ekstensi;
        
        // PENTING: Karena Controller ada di luar folder admin, path gambarnya menyesuaikan
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../Assets/IMG/" . $nama_gambar);
    }

    // Gunakan variabel $id_category yang sudah berupa angka
    if ($productObj->createProduct($nama, $id_category, $nama_gambar, $stok, $harga, $id_user)) {
        echo "<script>alert('Produk berhasil disimpan!'); window.location='../admin/inventory.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan produk!'); window.location='../admin/inventory.php';</script>";
    }
}

// ==========================================
// B. LOGIKA UPDATE DATA (EDIT)
// ==========================================
if (isset($_POST['btnUpdate'])) {
    $id_product  = (int)($_POST['id_product'] ?? 0);
    $nama        = (string)($_POST['nama'] ?? '');
    // PERBAIKAN: Tangkap nilai kategori sebagai INT (Angka) dari Form Edit Admin
    $id_category = (int)($_POST['kategori'] ?? 0);
    $stok        = (int)($_POST['stok'] ?? 0);
    $harga       = (int)str_replace(['.', ','], '', $_POST['harga'] ?? '0');

    // Gunakan variabel $id_category yang sudah berupa angka
    if ($productObj->updateProduct($id_product, $nama, $id_category, $stok, $harga)) {
        echo "<script>alert('Data produk berhasil diperbarui!'); window.location='../admin/inventory.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data!'); window.location='../admin/inventory.php';</script>";
    }
}

// ==========================================
// C. LOGIKA HAPUS DATA (DELETE)
// ==========================================
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id_product = (int)($_GET['id'] ?? 0);
    
    if ($id_product > 0) {
        if ($productObj->deleteProduct($id_product)) {
            echo "<script>alert('Produk berhasil dihapus!'); window.location='../admin/inventory.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus produk!'); window.location='../admin/inventory.php';</script>";
        }
    }
}
?>