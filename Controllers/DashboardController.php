<?php
session_start();
require_once '../Classes/Database.php';
require_once '../Classes/Dashboard.php';

$database = new Database();
$conn = $database->getConnection();
$dashObj = new Dashboard($conn);

$action = $_GET['action'] ?? '';

if ($action === 'get_data') {
    header('Content-Type: application/json');
    
    // 1. Ambil Statistik Atas (Otomatis membawa data 'realisasi_hari_ini' dari Class)
    $stats = $dashObj->getStats();
    
    // 2. Ambil Data Aktivitas
    $activities = [];
    $result_act = $dashObj->getAktivitasHariIni();
    
    if ($result_act && mysqli_num_rows($result_act) > 0) {
        while ($row = mysqli_fetch_assoc($result_act)) {
            $time = date('H:i', strtotime($row['created_at']));
            $nama = $row['nama_customer'] ? $row['nama_customer'] . ' (' . ucfirst($row['role_customer']) . ')' : 'Walk-in (Kasir)';
            $antrean = $row['queue_number'] ? ' - ' . $row['queue_number'] : '';

            $title = ($row['status'] === 'selesai') ? 'Transaksi Selesai' : 'Pesanan Baru Masuk';
            $type = ($row['status'] === 'selesai') ? 'green' : 'orange';

            $activities[] = [
                'time'  => $time,
                'title' => $title,
                'desc'  => $nama . $antrean,
                'type'  => $type
            ];
        }
    }

    // 3. Kembalikan balasan JSON ke JavaScript Frontend
    echo json_encode([
        'status' => 'success',
        'stats' => $stats,
        'activities' => $activities
    ]);
    exit;
}

echo "Akses ditolak!";
?>