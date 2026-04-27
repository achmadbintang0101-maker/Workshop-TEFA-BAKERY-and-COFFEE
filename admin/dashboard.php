<?php 
$page = 'dashboard'; 
include '../Config/auth.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tefa Bakery & Coffee – Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
  <link rel="stylesheet" href="../Assets/CSS/globaladmin.css" />
  <link rel="stylesheet" href="../Assets/CSS/dashboard.css" />
</head>
<body>

<!-- ── SIDEBAR ── -->
<?php include '../include/sidebar.php'; ?>
<!-- ── MAIN ── -->
<div class="main">
  <div class="topbar">
    <h1>Dashboard</h1>
    <div class="topbar-right">
      <button class="bell-btn" onclick="toggleNotif()" id="bellBtn">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/>
        </svg>
        <span class="bell-badge" id="bellBadge"></span>
      </button>
      <div class="user-chip">
        <div class="user-avatar">
          <svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
        Pak Dwiki
      </div>
    </div>
  </div>

  <div class="content-area">

    <div class="section-label">Ringkasan Operasi</div>

    <!-- Stat Cards — klik membuka modal ringkasan -->
    <div class="stat-row">
      <div class="stat-card" onclick="openStatDetail('pesanan')">
        <div class="stat-icon">
          <svg viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="8" y="6" width="36" height="40" rx="4" stroke="#3b1414" stroke-width="2.5" fill="none"/>
            <path d="M18 6v4a2 2 0 002 2h12a2 2 0 002-2V6" stroke="#3b1414" stroke-width="2.5" fill="none"/>
            <line x1="16" y1="22" x2="36" y2="22" stroke="#3b1414" stroke-width="2"/>
            <line x1="16" y1="28" x2="36" y2="28" stroke="#3b1414" stroke-width="2"/>
            <line x1="16" y1="34" x2="28" y2="34" stroke="#3b1414" stroke-width="2"/>
            <circle cx="14" cy="22" r="2" fill="#3b1414"/>
            <circle cx="14" cy="28" r="2" fill="#3b1414"/>
            <circle cx="14" cy="34" r="2" fill="#3b1414"/>
          </svg>
        </div>
        <div class="stat-info">
          <div class="stat-label">Total Pesanan</div>
          <div class="stat-value">25</div>
          <div class="stat-unit">Pesanan</div>
        </div>
      </div>

      <div class="stat-card" onclick="openStatDetail('produksi')">
        <div class="stat-icon">
          <svg viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="6" y="14" width="40" height="28" rx="4" stroke="#3b1414" stroke-width="2.5" fill="none"/>
            <path d="M16 14V10a2 2 0 012-2h16a2 2 0 012 2v4" stroke="#3b1414" stroke-width="2.5" fill="none"/>
            <circle cx="26" cy="28" r="6" stroke="#3b1414" stroke-width="2.5" fill="none"/>
            <path d="M23 28l2 2 4-4" stroke="#3b1414" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="stat-info">
          <div class="stat-label">Produksi <span>12-03-2026</span></div>
          <div class="stat-value">50</div>
          <div class="stat-unit">Produk</div>
        </div>
      </div>

      <div class="stat-card" onclick="openStatDetail('stok')">
        <div class="stat-icon">
          <svg viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 28c0-8.84 7.16-16 16-16s16 7.16 16 16" stroke="#3b1414" stroke-width="2.5" fill="none" stroke-linecap="round"/>
            <rect x="8" y="28" width="36" height="12" rx="3" stroke="#3b1414" stroke-width="2.5" fill="none"/>
            <path d="M20 28v-4a6 6 0 0112 0v4" stroke="#3b1414" stroke-width="2" fill="none"/>
          </svg>
        </div>
        <div class="stat-info">
          <div class="stat-label">Stok Tersedia</div>
          <div class="stat-value">150</div>
          <div class="stat-unit">item</div>
        </div>
      </div>
    </div>

    <!-- Middle Row -->
    <div class="middle-row">
      <div class="chart-card">
        <div class="chart-header">
          <span class="chart-title">Diagram Penjualan Produk: Kopi vs Roti</span>
          <div class="chart-legend">
            <div class="legend-item"><div class="legend-dot kopi"></div> Kopi</div>
            <div class="legend-item"><div class="legend-dot roti"></div> Roti</div>
          </div>
        </div>
        <canvas id="salesChart" height="140"></canvas>
      </div>

      <div class="admin-card">
        <div class="admin-title">Admin Saat Ini</div>
        <div class="admin-profile">
          <div class="admin-avatar">
            <svg width="22" height="22" fill="none" stroke="#8a7060" stroke-width="2" viewBox="0 0 24 24">
              <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <div>
            <div class="admin-name">Pak Dwiki</div>
            <div class="admin-role">Admin Tefa Bakery &amp; Coffee</div>
          </div>
        </div>
        <div class="admin-stats">
          <div class="admin-stat">
            <div class="admin-stat-label">Target</div>
            <div class="admin-stat-value">50</div>
            <div class="admin-stat-unit">Produk</div>
          </div>
          <div class="admin-stat">
            <div class="admin-stat-label">Sale</div>
            <div class="admin-stat-value">30</div>
            <div class="admin-stat-unit">Produk</div>
          </div>
        </div>
        <button class="btn-view" onclick="openAdminDetail()">View Details</button>
      </div>
    </div>

    <!-- Activity Table -->
    <div class="activity-section">
      <div class="activity-section-header">Riwayat Aktivitas</div>
      <table class="act-table">
        <thead>
          <tr><th>Waktu</th><th>Tipe Aktivitas</th></tr>
        </thead>
        <tbody>
          <tr onclick="openActivityDetail('12-03-2026 09:00','Pesanan Baru Diterima','Pesanan masuk dari pelanggan baru. Total 5 item roti dan 2 kopi.','green')">
            <td>12-03-2026 09:00</td><td>Pesanan Baru Diterima</td>
          </tr>
          <tr onclick="openActivityDetail('12-03-2026 10:00','Pesanan Baru Diproduksi','Proses produksi dimulai untuk 5 item roti. Estimasi selesai 11:00.','orange')">
            <td>12-03-2026 10:00</td><td>Pesanan Baru Diproduksi</td>
          </tr>
          <tr onclick="openActivityDetail('12-03-2026 10:15','Pesanan Selesai','Produksi selesai lebih cepat dari estimasi. 5 item siap dikirim.','green')">
            <td>12-03-2026 10:15</td><td>Pesanan selesai</td>
          </tr>
          <tr onclick="openActivityDetail('12-03-2026 10:45','Pesanan Baru Diterima','3 pesanan baru masuk sekaligus. Total 12 item campuran roti & kopi.','green')">
            <td>12-03-2026 10:45</td><td>Pesanan Baru Diterima 3 Pesanan</td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</div>

<!-- ══════ NOTIF PANEL ══════ -->
<div class="notif-overlay" id="notifOverlay" onclick="closeNotif()"></div>
<div class="notif-panel" id="notifPanel">
  <div class="notif-hdr">
    <span class="notif-hdr-title">Notifikasi</span>
    <button class="notif-x" onclick="closeNotif()">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
  <div class="notif-list" id="notifList"></div>
  <div class="notif-ftr">
    <span class="notif-mark-all" onclick="markAllRead()">Tandai Semua Dibaca</span>
  </div>
</div>

<!-- ══════ OVERLAY / MODALS ══════ -->
<div class="overlay" id="overlay" onclick="closeOverlay(event)">

  <!-- STAT DETAIL MODAL -->
  <div class="modal" id="statModal" onclick="event.stopPropagation()">
    <div class="modal-header">
      <span class="modal-title" id="statModalTitle">Detail</span>
      <button class="modal-close" onclick="closeModal()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div id="statModalContent"></div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeModal()">Tutup</button>
    </div>
  </div>

  <!-- ADMIN DETAIL MODAL -->
  <div class="modal" id="adminModal" style="display:none" onclick="event.stopPropagation()">
    <div class="modal-header">
      <span class="modal-title">Detail Admin</span>
      <button class="modal-close" onclick="closeModal()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="detail-grid">
      <div class="detail-item full" style="display:flex;align-items:center;gap:14px;background:#f5ead8">
        <div style="width:50px;height:50px;background:#e8ddd5;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <svg width="24" height="24" fill="none" stroke="#8a7060" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div>
          <div style="font-size:16px;font-weight:700;color:#3b1414">Pak Dwiki</div>
          <div style="font-size:12px;color:#8a7060">Admin Tefa Bakery & Coffee</div>
        </div>
      </div>
      <div class="detail-item"><div class="detail-label">Jabatan</div><div class="detail-value">Administrator</div></div>
      <div class="detail-item"><div class="detail-label">Status</div><div class="detail-value" style="color:#3a9c4e">● Aktif</div></div>
      <div class="detail-item"><div class="detail-label">Target Produksi</div><div class="detail-value big">50 <span style="font-size:14px;font-weight:500">produk</span></div></div>
      <div class="detail-item"><div class="detail-label">Realisasi Sale</div><div class="detail-value big">30 <span style="font-size:14px;font-weight:500">produk</span></div></div>
    </div>
    <div class="prog-wrap">
      <div class="prog-labels"><span>Pencapaian Target</span><span>60%</span></div>
      <div class="prog-bar"><div class="prog-fill" style="width:60%"></div></div>
    </div>
    <div class="modal-footer"><button class="btn-cancel" onclick="closeModal()">Tutup</button></div>
  </div>

  <!-- ACTIVITY DETAIL MODAL -->
  <div class="modal" id="activityModal" style="display:none;width:400px" onclick="event.stopPropagation()">
    <div class="modal-header">
      <span class="modal-title" id="actModalTitle">Detail Aktivitas</span>
      <button class="modal-close" onclick="closeModal()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div id="actModalContent"></div>
    <div class="modal-footer"><button class="btn-cancel" onclick="closeModal()">Tutup</button></div>
  </div>

  <!-- LOGOUT CONFIRM -->
  <div class="modal modal-sm" id="logoutModal" style="display:none" onclick="event.stopPropagation()">
    <div class="confirm-icon" style="background:#fff8e1">
      <svg width="26" height="26" fill="none" stroke="#e8760a" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
    </div>
    <div class="confirm-title">Log Out?</div>
    <div class="confirm-desc">Apakah kamu yakin ingin keluar dari akun <strong>Pak Dwiki</strong>?</div>
    <div class="modal-footer" style="justify-content:center;gap:12px">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-save" onclick="showToast('Berhasil log out!','success');closeModal()">Ya, Keluar</button>
    </div>
  </div>

</div>

<div class="toast-container" id="toastContainer"></div>


<script src="../Assets/JS/dashboard.js"></script>
</body>
</html>
