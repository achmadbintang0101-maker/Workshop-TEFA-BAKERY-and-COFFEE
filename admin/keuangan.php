<?php 
$page = 'keuangan'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tefa Bakery & Coffee – Keuangan</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../Assets/CSS/globaladmin.css" />
  <link rel="stylesheet" href="../Assets/CSS/keuangan.css" />
</head>
<body>

<!-- ── SIDEBAR ── -->
<?php include '../include/sidebar.php'; ?>
<!-- ── MAIN ── -->
<div class="main">
  <div class="topbar">
    <h1>Keuangan</h1>
    <div class="topbar-right">
      <button class="bell-btn" onclick="toggleNotif()" style="position:relative">
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

    <!-- Summary -->
    <div class="summary-label">Ringkasan Keuangan</div>
    <div class="summary-row">
      <div class="summary-card">
        <div class="summary-card-label">Total Saldo</div>
        <div class="summary-card-value" id="totalSaldo">Rp.5.000.000</div>
      </div>
      <div class="summary-card">
        <div class="summary-card-label">Pemasukan Bulan Ini</div>
        <div class="summary-card-value" id="totalPemasukan">Rp.3.500.000</div>
      </div>
      <div class="summary-card">
        <div class="summary-card-label">Pengeluaran Bulan Ini</div>
        <div class="summary-card-value" id="totalPengeluaran">Rp.2.855.000</div>
      </div>
    </div>

    <!-- Data Transaksi -->
    <div class="transaksi-header">
      <span class="transaksi-title">Data Transaksi</span>
      <button class="btn-input" onclick="openInputModal()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Input Data
      </button>
    </div>

    <!-- Filters -->
    <div class="filter-row">
      <div class="search-wrap">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input class="search-input" id="searchInput" type="text" placeholder="Cari..." />
      </div>
      <div class="dropdown-filter">
        <span id="dateLabel">Hari Ini</span>
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
        <select id="dateFilter" onchange="applyFilters()">
          <option value="all">Semua</option>
          <option value="today" selected>Hari Ini</option>
        </select>
      </div>
      <div class="dropdown-filter">
        <span id="kategoriLabel">Kategori</span>
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
        <select id="kategoriFilter" onchange="applyFilters()">
          <option value="all">Semua</option>
          <option value="Pemasukan">Pemasukan</option>
          <option value="Pengeluaran">Pengeluaran</option>
        </select>
      </div>
    </div>

    <!-- Table -->
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Jenis Transaksi</th>
            <th>Keterangan</th>
            <th>Nominal</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="tableBody"></tbody>
      </table>
      <div class="pagination">
        <span class="page-info" id="pageInfo">Menampilkan 1 sampai 5 dari 10</span>
        <div class="page-controls" id="pageControls"></div>
      </div>
    </div>

  </div>
</div>

<!-- ── NOTIF PANEL ── -->
<div class="notif-overlay" id="notifOverlay" onclick="closeNotif()"></div>
<div class="notif-panel" id="notifPanel">
  <div class="notif-header">
    <span class="notif-title-text">Notifikasi</span>
    <button class="notif-close" onclick="closeNotif()"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
  </div>
  <div class="notif-list" id="notifList"></div>
  <div class="notif-footer"><span class="notif-mark-all" onclick="markAllRead()">Tandai Semua Dibaca</span></div>
</div>

<!-- ── OVERLAY ── -->
<div class="overlay" id="overlay" onclick="closeOverlay(event)">

  <!-- INPUT / EDIT MODAL -->
  <div class="modal" id="inputModal" onclick="event.stopPropagation()">
    <div class="modal-header">
      <span class="modal-title" id="modalTitle">Tambah Transaksi</span>
      <button class="modal-close" onclick="closeModal()"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Tanggal</label>
        <input class="form-input" id="fTanggal" type="date" />
      </div>
      <div class="form-group">
        <label class="form-label">Jenis Transaksi</label>
        <select class="form-select" id="fJenis">
          <option value="Pemasukan">Pemasukan</option>
          <option value="Pengeluaran">Pengeluaran</option>
        </select>
      </div>
      <div class="form-group full">
        <label class="form-label">Keterangan</label>
        <input class="form-input" id="fKeterangan" type="text" placeholder="Contoh: Penjualan roti hari ini..." />
      </div>
      <div class="form-group">
        <label class="form-label">Nominal (Rp)</label>
        <input class="form-input" id="fNominal" type="number" min="0" placeholder="Contoh: 200000" />
      </div>
      <div class="form-group">
        <label class="form-label">Status</label>
        <select class="form-select" id="fStatus">
          <option value="Menunggu">Menunggu</option>
          <option value="Selesai">Selesai</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-save" id="btnSave" onclick="saveData()">Simpan</button>
    </div>
  </div>

  <!-- DELETE CONFIRM -->
  <div class="modal modal-sm" id="deleteModal" style="display:none" onclick="event.stopPropagation()">
    <div class="confirm-icon"><svg width="26" height="26" fill="none" stroke="#e53935" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg></div>
    <div class="confirm-title">Hapus Transaksi?</div>
    <div class="confirm-desc">Kamu yakin ingin menghapus transaksi <strong id="deleteItemName">–</strong>?</div>
    <div class="modal-footer" style="justify-content:center">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-danger" onclick="confirmDelete()">Ya, Hapus</button>
    </div>
  </div>

  <!-- LOGOUT -->
  <div class="modal modal-sm" id="logoutModal" style="display:none" onclick="event.stopPropagation()">
    <div class="confirm-icon" style="background:#fff8e1"><svg width="26" height="26" fill="none" stroke="#e8760a" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg></div>
    <div class="confirm-title">Log Out?</div>
    <div class="confirm-desc">Apakah kamu yakin ingin keluar dari akun <strong>Pak Dwiki</strong>?</div>
    <div class="modal-footer" style="justify-content:center">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-save" onclick="showToast('Berhasil log out!','success');closeModal()">Ya, Keluar</button>
    </div>
  </div>

</div>

<!-- Toast -->
<div class="toast-container" id="toastContainer"></div>


<script src="../Assets/JS/keuangan.js"></script>
</body>
</html>