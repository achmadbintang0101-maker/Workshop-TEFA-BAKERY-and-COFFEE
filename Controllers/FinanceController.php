<?php
session_start();
require_once '../Classes/Database.php';
require_once '../Classes/Finance.php';

$database = new Database();
$conn = $database->getConnection();
$financeObj = new Finance($conn);

$action = $_GET['action'] ?? '';

// API: AMBIL SEMUA DATA & RINGKASAN
if ($action === 'get_all') {
    header('Content-Type: application/json');
    
    $summary = $financeObj->getFinancialSummary();
    $result = $financeObj->getAllFinancialRecords();
    $records = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Format tanggal jadi DD-MM-YYYY
            $dateObj = new DateTime($row['tanggal']);
            $records[] = [
                'id' => (int)$row['id'],
                'tanggalRaw' => $dateObj->format('Y-m-d'),
                'tanggal' => $dateObj->format('d-m-Y'),
                'jenis' => $row['jenis'],
                'keterangan' => $row['keterangan'],
                'nominal' => (float)$row['nominal'],
                'status' => $row['status'],
                'source' => $row['source'] // 'auto' (dari transaksi) atau 'manual'
            ];
        }
    }
    
    echo json_encode(['summary' => $summary, 'records' => $records]);
    exit;
}

// API: SIMPAN PENARIKAN/PEMASUKAN MANUAL
if ($action === 'create') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Konversi tanggal DD-MM-YYYY ke format Database YYYY-MM-DD
    $tanggalParts = explode('-', $data['tanggal']);
    $tanggalDB = $tanggalParts[2] . '-' . $tanggalParts[1] . '-' . $tanggalParts[0];

    if ($financeObj->createManualFinance($tanggalDB, $data['jenis'], $data['keterangan'], $data['nominal'], $data['status'])) {
        echo json_encode(['status' => 'success', 'message' => 'Data keuangan berhasil disimpan']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data']);
    }
    exit;
}

// API: HAPUS DATA MANUAL
if ($action === 'delete') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents("php://input"), true);
    
    if ($financeObj->deleteManualFinance((int)$data['id'])) {
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
    exit;
}

echo "Akses ditolak!";
?>