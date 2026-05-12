<?php
// =========================================================================
// 1. API UNTUK CUSTOMER CHECKOUT (Paling Atas, Tanpa Auth)
// =========================================================================
if (isset($_GET['action']) && $_GET['action'] === 'api_checkout') {
    header('Content-Type: application/json');
    
    // Panggil Class yang dibutuhkan
    require_once '../Classes/Database.php';
    require_once '../Classes/Transaction.php';

    $db = new Database();
    $conn = $db->getConnection();
    $transactionObj = new Transaction($conn);

// Ambil data JSON dari JavaScript
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'Data pesanan tidak valid']);
        exit;
    }

    $nama = trim($data['nama'] ?? ''); // Tambahkan trim() di sini
    
    // --- [MASUKKAN VALIDASI LAPIS KEDUA DI SINI] ---
    if (!preg_match("/^[a-zA-Z\s]+$/", $nama)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama hanya boleh berisi huruf dan spasi!']);
        exit;
    }

    $nama = $data['nama'] ?? '';
    $role = $data['role'] ?? '';
    $total = (float)($data['total_price'] ?? 0);
    $tax = (float)($data['tax'] ?? 0);
    $grand_total = (float)($data['grand_total'] ?? 0);
    $items = $data['items'] ?? [];
    
    // Generate Nomor Antrean Random (Contoh: A-123)
    $queue_number = 'A-' . rand(100, 999);

    // Panggil method OOP yang sudah kita buat di Classes/Transaction.php
    $isSuccess = $transactionObj->createCustomerTransaction(
        $nama, $role, $queue_number, $total, $tax, $grand_total, $items
    );

    if ($isSuccess) {
        echo json_encode([
            'status' => 'success', 
            'queue_number' => $queue_number
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan transaksi ke database']);
    }
    
    // Hentikan script di sini agar tidak mengeksekusi logika Kasir di bawahnya
    exit();
}

// =========================================================================
// 2. KODE KASIR (Membutuhkan Login / Auth)
// =========================================================================
session_start();

// 1. Panggil Koneksi & Class (Gunakan include_once agar tidak error bentrok dengan API di atas)
include_once '../Classes/Database.php'; 

// Bangun Object Database
$database = new Database();
$conn = $database->getConnection();

// 2. Proteksi & Validasi Keamanan
if(!isset($_SESSION['status']) || $_SESSION['role'] !== 'kasir'){
    header("Location: ../index.php"); // Tendang keluar jika bukan kasir
    exit;
}

// 3. Tangkap Request Pembayaran (Hanya jika ada data yang dikirim via POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cartData']) && !empty($_POST['cartData'])) {
    
    global $conn; 

    $cart = json_decode($_POST['cartData'], true);
    $id_user = (int)($_SESSION['id_user']); 
    $subtotal = 0;

    // Hitung subtotal belanja (Looping item di keranjang)
    foreach($cart as $item){
        $subtotal += (float)$item['price'] * (int)$item['qty'];
    }

    $tax   = $subtotal * 0.10; // Pajak 10%
    $total = $subtotal + $tax; // Total bayar

    // ==========================================
    // LOGIKA PENYIMPANAN KE DATABASE (KASIR)
    // ==========================================
    
    // A. Simpan ke tabel TRANSACTIONS (Data Induk Transaksi)
    // Tambahkan kolom status dan berikan nilai langsung 'selesai'
    $stmt = mysqli_prepare($conn, "INSERT INTO transactions (id_user, total_price, tax, grand_total, status) VALUES (?, ?, ?, ?, 'selesai')");
    mysqli_stmt_bind_param($stmt, "iddd", $id_user, $subtotal, $tax, $total);
    mysqli_stmt_execute($stmt);
    
    // Ambil ID Transaksi yang baru saja dibuat oleh database
    $transaction_id = mysqli_insert_id($conn); 
    mysqli_stmt_close($stmt);

    // B. Simpan rincian barang (TRANSACTION_DETAILS) & Potong Stok
    foreach($cart as $item){
        $id_p    = (int)$item['id'];
        $qty     = (int)$item['qty'];
        $price   = (float)$item['price'];
        $itemSub = $price * $qty;

        // B1. Simpan detail barang apa saja yang dibeli
        $stmt2 = mysqli_prepare($conn, "INSERT INTO transaction_details (id_transaction, id_product, qty, price_at_time, subtotal) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt2, "iiidd", $transaction_id, $id_p, $qty, $price, $itemSub);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        // B2. Potong stok di tabel products
        // (Aman karena kita sudah pastikan inputnya adalah angka integer/float)
        mysqli_query($conn, "UPDATE products SET stok = stok - $qty WHERE id_product = $id_p");
    }

    // 4. Sukses! Arahkan ke halaman struk dengan membawa ID Transaksi
    header("Location: ../Kasir/struk.php?id=" . $transaction_id);
    exit;
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Jika tidak ada data cart, kembalikan ke halaman kasir
    header("Location: ../Kasir/index.php");
    exit;
}
?>