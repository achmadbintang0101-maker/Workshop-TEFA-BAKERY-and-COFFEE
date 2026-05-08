<?php
// Controllers/ApiKasir.php
session_start();
header('Content-Type: application/json');

// Pastikan hanya Kasir yang bisa mengakses API ini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kasir') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once '../Classes/Database.php';
require_once '../Classes/Transaction.php';

$db = new Database();
$conn = $db->getConnection();
$transactionObj = new Transaction($conn);

$action = $_GET['action'] ?? '';

// API 1: Mengambil daftar notifikasi pesanan masuk
if ($action === 'get_notif') {
    $result = $transactionObj->getPendingOrders();
    $orders = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Format waktu agar lebih ramah dibaca
        $time_diff = time() - strtotime($row['created_at']);
        $minutes_ago = floor($time_diff / 60);
        $waktu_text = ($minutes_ago < 1) ? "Baru saja" : $minutes_ago . " menit lalu";

        $orders[] = [
            'id' => $row['id_transaction'],
            'queue' => $row['queue_number'],
            'waktu_text' => $waktu_text
        ];
    }
    echo json_encode(['status' => 'success', 'data' => $orders]);
    exit;
}

// API 2: Mengambil detail satu pesanan untuk ditampilkan di Modal
if ($action === 'get_detail') {
    $id_trans = (int)($_GET['id'] ?? 0);
    $data = $transactionObj->getOrderWithDetails($id_trans);
    
    if ($data) {
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }
    exit;
}

// API 3: Mengkonfirmasi pesanan dan memotong stok
if ($action === 'confirm_order') {
    $data_input = json_decode(file_get_contents("php://input"), true);
    $id_trans = (int)($data_input['id_transaction'] ?? 0);

    if ($id_trans > 0) {
        $isSuccess = $transactionObj->confirmAndReduceStock($id_trans);
        if ($isSuccess) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memproses pesanan']);
        }
    }
    exit;
}

// Jika action tidak dikenali
echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
?>