<?php
// =========================================================================
// 1. API UNTUK CUSTOMER CHECKOUT (Paling Atas, Tanpa Auth)
// =========================================================================
if (isset($_GET['action']) && $_GET['action'] === 'api_checkout') {
    header('Content-Type: application/json');
    
    require_once '../Classes/Database.php';
    require_once '../Classes/Transaction.php';

    $db   = new Database();
    $conn = $db->getConnection();
    $transactionObj = new Transaction($conn);

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'Data pesanan tidak valid']);
        exit;
    }

    $nama = trim($data['nama'] ?? '');

    // Validasi: nama hanya boleh huruf dan spasi
    if (empty($nama) || !preg_match("/^[a-zA-Z\s]+$/", $nama)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama hanya boleh berisi huruf dan spasi!']);
        exit;
    }

    $role        = $data['role'] ?? 'umum';
    $total       = (float)($data['total_price'] ?? 0);
    $tax         = (float)($data['tax'] ?? 0);
    $grand_total = (float)($data['grand_total'] ?? 0);
    $items       = $data['items'] ?? [];

    if (empty($items)) {
        echo json_encode(['status' => 'error', 'message' => 'Keranjang belanja kosong!']);
        exit;
    }

    // ✅ FIX: Nomor antrean menggunakan timestamp agar tidak duplikat
    // Format: A-143052XX (Jam:Menit:Detik + 2 digit random)
    $queue_number = 'A-' . date('His') . rand(10, 99);

    $isSuccess = $transactionObj->createCustomerTransaction(
        $nama, $role, $queue_number, $total, $tax, $grand_total, $items
    );

    if ($isSuccess) {
        echo json_encode([
            'status'       => 'success',
            'queue_number' => $queue_number
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan transaksi ke database']);
    }
    
    exit();
}

// =========================================================================
// 2. KODE KASIR (Membutuhkan Login / Auth)
// =========================================================================
session_start();

include_once '../Classes/Database.php';

$database = new Database();
$conn     = $database->getConnection();

// Proteksi: hanya kasir yang boleh akses
if (!isset($_SESSION['status']) || $_SESSION['role'] !== 'kasir') {
    header("Location: ../index.php");
    exit;
}

// Tangkap Request Pembayaran dari Kasir
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cartData']) && !empty($_POST['cartData'])) {
    
    $cart    = json_decode($_POST['cartData'], true);
    $id_user = (int)($_SESSION['id_user']);
    $subtotal = 0;

    foreach ($cart as $item) {
        $subtotal += (float)$item['price'] * (int)$item['qty'];
    }

    $tax   = $subtotal * 0.10;
    $total = $subtotal + $tax;

    mysqli_begin_transaction($conn);

    try {
        // A. Simpan ke tabel TRANSACTIONS
        $stmt = mysqli_prepare($conn, "INSERT INTO transactions (id_user, total_price, tax, grand_total, status) VALUES (?, ?, ?, ?, 'selesai')");
        mysqli_stmt_bind_param($stmt, "iddd", $id_user, $subtotal, $tax, $total);
        mysqli_stmt_execute($stmt);
        $transaction_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        // B. Simpan rincian barang & Potong Stok
        foreach ($cart as $item) {
            $id_p    = (int)$item['id'];
            $qty     = (int)$item['qty'];
            $price   = (float)$item['price'];
            $itemSub = $price * $qty;

            // Potong stok dengan pengecekan negatif
            $stmt_stok = mysqli_prepare($conn, "UPDATE products SET stok = stok - ? WHERE id_product = ? AND stok >= ?");
            mysqli_stmt_bind_param($stmt_stok, "iii", $qty, $id_p, $qty);
            mysqli_stmt_execute($stmt_stok);
            
            if (mysqli_stmt_affected_rows($stmt_stok) == 0) {
                throw new Exception("Stok tidak mencukupi untuk salah satu produk.");
            }
            mysqli_stmt_close($stmt_stok);

            // Simpan detail item
            $stmt2 = mysqli_prepare($conn, "INSERT INTO transaction_details (id_transaction, id_product, qty, price_at_time, subtotal) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt2, "iiidd", $transaction_id, $id_p, $qty, $price, $itemSub);
            mysqli_stmt_execute($stmt2);
            mysqli_stmt_close($stmt2);
        }

        mysqli_commit($conn);
        header("Location: ../Kasir/struk.php?id=" . $transaction_id);
        exit;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: ../Kasir/index.php?error=stok");
        exit;
    }

} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Location: ../Kasir/index.php");
    exit;
}
?>