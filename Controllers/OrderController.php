<?php
session_start();

// Panggil file koneksi dan class Order
require_once '../Classes/Database.php';
require_once '../Classes/Order.php';

// Bangun Object Database
$database = new Database();
$conn = $database->getConnection();
$orderObj = new Order($conn);

// Tangkap 'action' dari URL yang dikirim oleh JavaScript
$action = $_GET['action'] ?? '';

// ==========================================================
// 1. API: AMBIL SEMUA DATA PESANAN UNTUK TABEL ADMIN
// ==========================================================
if ($action === 'get_all_orders') {
    // Wajib memberi tahu browser bahwa ini adalah data JSON
    header('Content-Type: application/json');
    
    $result = $orderObj->getAllOrders();
    $orders_arr = [];
    $no = 1;

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            
            // LOGIKA CERDAS: Penamaan Order ID
            // Jika nama_customer kosong = Pembeli langsung ke meja kasir
            if (empty($row['nama_customer'])) {
                $orderIdText = "Walk-in (Kasir)";
            } else {
                // Jika pesanan online = Nama (Role)
                $orderIdText = $row['nama_customer'] . " (" . ucfirst($row['role_customer']) . ")";
            }

            // Tambahkan nomor antrean jika ada
            if (!empty($row['queue_number'])) {
                $orderIdText .= " - " . $row['queue_number'];
            }

            // LOGIKA CERDAS: Penyesuaian Status untuk UI Admin
            // Di database kita pakai 'pending', tapi di UI Admin desainnya 'Proses'
            $status_ui = ucfirst($row['status']);
            if ($status_ui === 'Pending') {
                $status_ui = 'Proses';
            }

            // Susun data agar persis seperti format dummy JS sebelumnya
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

    // Kembalikan output JSON
    echo json_encode($orders_arr);
    exit;
}

// ==========================================================
// 2. API: UBAH STATUS PESANAN JADI 'SELESAI'
// ==========================================================
if ($action === 'selesai') {
    header('Content-Type: application/json');
    
    // JS akan mengirim data via POST (body JSON)
    $data = json_decode(file_get_contents("php://input"), true);
    $id_transaction = (int)($data['id'] ?? 0);

    if ($id_transaction > 0) {
        if ($orderObj->markOrderAsDone($id_transaction)) {
            echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil diselesaikan']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah status pesanan di database']);
        }
    }
    exit;
}

// ==========================================================
// 3. API: HAPUS PESANAN (DELETE)
// ==========================================================
if ($action === 'delete') {
    header('Content-Type: application/json');
    
    $data = json_decode(file_get_contents("php://input"), true);
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
// 4. API: AMBIL AKTIVITAS TERBARU (REAL-TIME DARI DATABASE)
// ==========================================================
if ($action === 'get_activities') {
    header('Content-Type: application/json');
    
    // Ambil 10 transaksi terbaru dari database
    $query = "SELECT t.queue_number, c.nama_customer, c.role_customer, t.created_at, t.status
              FROM transactions t
              LEFT JOIN customers c ON t.id_customer = c.id_customer
              ORDER BY t.created_at DESC LIMIT 10";
              
    $result = mysqli_query($conn, $query);
    $activities_arr = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Format Waktu dan Tanggal
            $time = date('H:i', strtotime($row['created_at']));
            $date = date('d M Y', strtotime($row['created_at']));
            
            // Format Nama
            $nama = $row['nama_customer'] ? $row['nama_customer'] . ' (' . ucfirst($row['role_customer']) . ')' : 'Walk-in (Kasir)';
            $antrean = $row['queue_number'] ? ' - ' . $row['queue_number'] : '';

            // Logika UI berdasarkan status
            $type = ($row['status'] === 'selesai') ? 'green' : 'orange';
            $title = ($row['status'] === 'selesai') ? 'Transaksi Selesai' : 'Order Baru Masuk';

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
// ==========================================================
if ($action === 'create_manual_order') {
    header('Content-Type: application/json');
    
    // Tangkap data JSON yang dikirim dari form modal JavaScript
    $data = json_decode(file_get_contents("php://input"), true);
    
    $nama = trim($data['nama'] ?? '');

    // --- [MASUKKAN VALIDASI LAPIS KEDUA DI SINI] ---
    if (!preg_match("/^[a-zA-Z\s]+$/", $nama)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama pemesan hanya boleh berisi huruf dan spasi!']);
        exit;
    }
    
    $nama = trim($data['nama'] ?? '');
    $role = $data['role'] ?? 'umum';
    $status = $data['status'] ?? 'pending'; // 'pending' = Proses, 'selesai' = Lunas
    $items = $data['items'] ?? [];

    if (empty($nama) || empty($items)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama pemesan dan minimal 1 produk wajib diisi!']);
        exit;
    }

    // Panggil fungsi yang baru kita buat di Class Order
    if ($orderObj->createManualOrder($nama, $role, $status, $items)) {
        echo json_encode(['status' => 'success', 'message' => 'Pesanan manual berhasil disimpan ke database']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan pesanan ke database']);
    }
    exit;
}

// Jika diakses tanpa parameter action
echo "Akses ditolak!";
?>