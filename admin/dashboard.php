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
  <link rel="stylesheet" href="../Assets/CSS/globaladmin.css" />
  <link rel="stylesheet" href="../Assets/CSS/dashboard.css" />
</head>
<body>

<?php include '../include/sidebar.php'; ?>

<div class="main">
  <div class="topbar">
    <h1>Dashboard</h1>
    <div class="topbar-right">
        </div>
  </div>

  <div class="content-area">

    <div class="section-label">Ringkasan Operasi</div>

    <div class="stat-row">
      <div class="stat-card" onclick="window.location.href='keuangan.php'" title="Buka Detail Keuangan">
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
          <div class="stat-label">Total Pendapatan <span id="bulanPendapatan">Bulan Ini</span></div>
          <div class="stat-value" id="valPendapatan" style="font-size: 24px;">Rp 0</div>
          <div class="stat-unit">Rupiah</div>
        </div>
      </div>

      <div class="stat-card" onclick="window.location.href='order.php'" title="Buka Detail Pesanan">
        <div class="stat-icon">
          <svg viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="6" y="14" width="40" height="28" rx="4" stroke="#3b1414" stroke-width="2.5" fill="none"/>
            <path d="M16 14V10a2 2 0 012-2h16a2 2 0 012 2v4" stroke="#3b1414" stroke-width="2.5" fill="none"/>
            <circle cx="26" cy="28" r="6" stroke="#3b1414" stroke-width="2.5" fill="none"/>
            <path d="M23 28l2 2 4-4" stroke="#3b1414" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="stat-info">
          <div class="stat-label">Total Pesanan <span id="tanggalPesanan">Hari Ini</span></div>
          <div class="stat-value" id="valPesanan">0</div>
          <div class="stat-unit">Transaksi</div>
        </div>
      </div>

      <div class="stat-card" onclick="window.location.href='inventory.php'" title="Buka Detail Inventory">
        <div class="stat-icon">
          <svg viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 28c0-8.84 7.16-16 16-16s16 7.16 16 16" stroke="#3b1414" stroke-width="2.5" fill="none" stroke-linecap="round"/>
            <rect x="8" y="28" width="36" height="12" rx="3" stroke="#3b1414" stroke-width="2.5" fill="none"/>
            <path d="M20 28v-4a6 6 0 0112 0v4" stroke="#3b1414" stroke-width="2" fill="none"/>
          </svg>
        </div>
        <div class="stat-info">
          <div class="stat-label">Stok Tersedia</div>
          <div class="stat-value" id="valStok">0</div>
          <div class="stat-unit">Item Produk</div>
        </div>
      </div>
    </div>

    <div class="middle-row" style="align-items: flex-start;">
      
      <div class="activity-section" style="flex: 1;">
        <div class="activity-section-header">Riwayat Aktivitas</div>
        <table class="act-table">
          <thead>
            <tr><th>Waktu</th><th>Tipe Aktivitas</th><th>Keterangan</th></tr>
          </thead>
          <tbody id="activityBody">
            <tr><td colspan="3" style="text-align:center; padding:30px; color:#999;">Memuat aktivitas...</td></tr>
          </tbody>
        </table>
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
            <div class="admin-name"><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></div>
            <div class="admin-role">Tefa Bakery &amp; Coffee</div>
          </div>
        </div>
        
        <div class="admin-stats">
          <div class="admin-stat">
            <div class="admin-stat-label">Target Penjualan</div>
            <div class="admin-stat-value" id="uiTarget1">25</div>
            <div class="admin-stat-unit">Produk</div>
          </div>
          <div class="admin-stat">
            <div class="admin-stat-label">Realisasi Penjualan</div>
            <div class="admin-stat-value" id="uiRealisasi1">0</div>
            <div class="admin-stat-unit">Produk</div>
          </div>
        </div>
        <button class="btn-view" onclick="openAdminDetail()">View Details</button>
      </div>
      
    </div>

  </div>
</div>

<div class="overlay" id="overlay" onclick="closeOverlay(event)">

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
          <div style="font-size:16px;font-weight:700;color:#3b1414"><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></div>
          <div style="font-size:12px;color:#8a7060">Admin Tefa Bakery & Coffee</div>
        </div>
      </div>
      <div class="detail-item"><div class="detail-label">Jabatan</div><div class="detail-value">Administrator</div></div>
      <div class="detail-item"><div class="detail-label">Status</div><div class="detail-value" style="color:#3a9c4e">● Aktif</div></div>
      
      <div class="detail-item"><div class="detail-label">Target Penjualan</div><div class="detail-value big" id="uiTarget2">25 <span style="font-size:14px;font-weight:500">produk</span></div></div>
      <div class="detail-item"><div class="detail-label">Realisasi Penjualan</div><div class="detail-value big" id="uiRealisasi2">0 <span style="font-size:14px;font-weight:500">produk</span></div></div>
    </div>
    
    <div class="prog-wrap">
      <div class="prog-labels"><span>Pencapaian Target Hari Ini</span><span id="uiPersentase">0%</span></div>
      <div class="prog-bar"><div class="prog-fill" id="uiBar" style="width:0%"></div></div>
    </div>
    <div class="modal-footer"><button class="btn-cancel" onclick="closeModal()">Tutup</button></div>
  </div>

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

</div>

<div class="toast-container" id="toastContainer"></div>
<script src="../Assets/JS/dashboard.js"></script>
</body>
</html>