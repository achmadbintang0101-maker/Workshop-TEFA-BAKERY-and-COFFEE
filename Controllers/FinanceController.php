<?php
session_start();
require_once '../Classes/Database.php';
require_once '../Classes/Finance.php';

$database   = new Database();
$conn       = $database->getConnection();
$financeObj = new Finance($conn);

$action = $_GET['action'] ?? '';

// API: AMBIL SEMUA DATA & RINGKASAN
if ($action === 'get_all') {
    header('Content-Type: application/json');
    
    $summary = $financeObj->getFinancialSummary();
    $result  = $financeObj->getAllFinancialRecords();
    $records = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $dateObj = new DateTime($row['tanggal']);
            $records[] = [
                'id'         => (int)$row['id'],
                'tanggalRaw' => $dateObj->format('Y-m-d'),
                'tanggal'    => $dateObj->format('d-m-Y'),
                'jenis'      => $row['jenis'],
                'keterangan' => $row['keterangan'],
                'nominal'    => (float)$row['nominal'],
                'status'     => $row['status'],
                'source'     => $row['source'] // 'auto' atau 'manual'
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

    // ✅ FIX: Validasi input sebelum diproses
    $nominal = (float)($data['nominal'] ?? 0);
    if ($nominal <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Nominal harus lebih dari 0!']);
        exit;
    }

    $keterangan = trim($data['keterangan'] ?? '');
    if (empty($keterangan)) {
        echo json_encode(['status' => 'error', 'message' => 'Keterangan tidak boleh kosong!']);
        exit;
    }

    // ✅ FIX: Konversi tanggal yang lebih aman menggunakan DateTime
    // Mendukung format DD-MM-YYYY (dari frontend) maupun YYYY-MM-DD
    $tanggalInput = $data['tanggal'] ?? '';
    $tanggalDB    = null;

    // Coba parse format DD-MM-YYYY dulu
    $dateObj = DateTime::createFromFormat('d-m-Y', $tanggalInput);
    if ($dateObj) {
        $tanggalDB = $dateObj->format('Y-m-d');
    } else {
        // Fallback: coba parse format YYYY-MM-DD
        $dateObj = DateTime::createFromFormat('Y-m-d', $tanggalInput);
        if ($dateObj) {
            $tanggalDB = $dateObj->format('Y-m-d');
        }
    }

    if (!$tanggalDB) {
        echo json_encode(['status' => 'error', 'message' => 'Format tanggal tidak valid! Gunakan DD-MM-YYYY.']);
        exit;
    }

    $jenis  = $data['jenis']  ?? 'Pemasukan';
    $status = $data['status'] ?? 'Selesai';

    if ($financeObj->createManualFinance($tanggalDB, $jenis, $keterangan, $nominal, $status)) {
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

    $id = (int)($data['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        exit;
    }
    
    if ($financeObj->deleteManualFinance($id)) {
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
    exit;
}

echo "Akses ditolak!";
?>