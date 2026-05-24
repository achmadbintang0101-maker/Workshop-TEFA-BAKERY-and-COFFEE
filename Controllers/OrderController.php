<?php
session_start();

require_once '../Classes/Database.php';
require_once '../Classes/Order.php';

$database = new Database();
$conn     = $database->getConnection();
$orderObj = new Order($conn);

$action = $_GET['action'] ?? '';

// ==========================================================
// 1. API: AMBIL SEMUA DATA PESANAN UNTUK TABEL ADMIN
// ==========================================================
if ($action === 'get_all_orders') {
    header('Content-Type: application/json');
    
    $result     = $orderObj->getAllOrders();
    $orders_arr = [];
    $no         = 1;

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (empty($row['nama_customer'])) {
                $orderIdText = "Walk-in (Kasir)";
            } else {
                $orderIdText = $row['nama_customer'] . " (" . ucfirst($row['role_customer']) . ")";
            }
            if (!empty($row['queue_number'])) {
                $orderIdText .= " - " . $row['queue_number'];
            }

            $status_ui = ucfirst($row['status']);
            if ($status_ui === 'Pending') $status_ui = 'Proses';

            $orders_arr[] = [
                'id'      => (int)$row['id'],
                'no'      => str_pad($no++, 2, "0", STR_PAD_LEFT),
                'orderId' => $orderIdText,
                'tanggal' => $row['tanggal'],
                'jumlah'  => (int)$row['jumlah_item'],
                'status'  => $status_ui,
                'aksiNum' => ($status_ui === 'Proses') ? (int)$row['jumlah_item'] : null
            ];
        }
    }

    echo json_encode($orders_arr);
    exit;
}

// ==========================================================
// 1.5 API: AMBIL DETAIL BARANG UNTUK MODAL ADMIN
// ==========================================================
if ($action === 'get_detail') {
    header('Content-Type: application/json');
    $id_transaction = (int)($_GET['id'] ?? 0);
    
    if ($id_transaction > 0) {
        $result = $orderObj->getOrderDetails($id_transaction);
        $items  = [];
        
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $items[] = [
                    'name'     => $row['name'],
                    'qty'      => (int)$row['qty'],
                    'price'    => (float)$row['price_at_time'],
                    'subtotal' => (float)$row['subtotal']
                ];
            }
        }
        echo json_encode(['status' => 'success', 'items' => $items]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID Transaksi tidak valid']);
    }
    exit;
}

// ==========================================================
// 2. API: UBAH STATUS PESANAN JADI 'SELESAI'
// ==========================================================
if ($action === 'selesai') {
    header('Content-Type: application/json');
    
    $data           = json_decode(file_get_contents("php://input"), true);
    $id_transaction = (int)($data['id'] ?? 0);

    if ($id_transaction > 0) {
        if ($orderObj->markOrderAsDone($id_transaction)) {
            echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil diselesaikan']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal: Stok tidak mencukupi untuk menyelesaikan pesanan ini.']);
        }
    }
    exit;
}

// ==========================================================
// 3. API: HAPUS PESANAN (DELETE)
// ==========================================================
if ($action === 'delete') {
    header('Content-Type: application/json');
    
    $data           = json_decode(file_get_contents("php://input"), true);
    $id_transaction = (int)($data['id'] ?? 0);

    if ($id_transaction > 0) {
        if ($orderObj->deleteOrder($id_transaction)) {
            echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil dihapus']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus pesanan']);
        }
    }
    exit;
}

// ==========================================================
// 4. API: AMBIL AKTIVITAS TERBARU
// ==========================================================
if ($action === 'get_activities') {
    header('Content-Type: application/json');
    
    $query = "SELECT t.queue_number, c.nama_customer, c.role_customer, t.created_at, t.status
              FROM transactions t
              LEFT JOIN customers c ON t.id_customer = c.id_customer
              ORDER BY t.created_at DESC LIMIT 10";
              
    $result         = mysqli_query($conn, $query);
    $activities_arr = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $time    = date('H:i', strtotime($row['created_at']));
            $date    = date('d M Y', strtotime($row['created_at']));
            $nama    = $row['nama_customer'] ? $row['nama_customer'] . ' (' . ucfirst($row['role_customer']) . ')' : 'Walk-in (Kasir)';
            $antrean = $row['queue_number'] ? ' - ' . $row['queue_number'] : '';
            $type    = ($row['status'] === 'selesai') ? 'green' : 'orange';
            $title   = ($row['status'] === 'selesai') ? 'Transaksi Selesai' : 'Order Baru Masuk';

            $activities_arr[] = [
                'time'  => $time,
                'date'  => $date,
                'title' => $title,
                'sub'   => $nama . $antrean,
                'type'  => $type
            ];
        }
    }
    echo json_encode($activities_arr);
    exit;
}

// ==========================================================
// 5. API: BUAT PESANAN MANUAL DARI ADMIN
// FIX: Sekarang mengembalikan pesan error stok yang spesifik
// ==========================================================
if ($action === 'create_manual_order') {
    header('Content-Type: application/json');
    
    $data   = json_decode(file_get_contents("php://input"), true);
    $nama   = trim($data['nama'] ?? '');
    $role   = $data['role']   ?? 'umum';
    $status = $data['status'] ?? 'pending';
    $items  = $data['items']  ?? [];

    if (empty($nama) || empty($items)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama pemesan dan minimal 1 produk wajib diisi!']);
        exit;
    }

    // Panggil createManualOrder yang sekarang mengembalikan array hasil
    $result = $orderObj->createManualOrder($nama, $role, $status, $items);

    if ($result['success']) {
        echo json_encode(['status' => 'success', 'message' => 'Pesanan manual berhasil disimpan!']);
    } else {
        // Pesan error spesifik dari backend (termasuk info stok kurang)
        echo json_encode(['status' => 'error', 'message' => $result['message']]);
    }
    exit;
}

echo "Akses ditolak!";
?>