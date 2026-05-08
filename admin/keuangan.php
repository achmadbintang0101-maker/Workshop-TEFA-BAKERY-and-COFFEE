<?php 
$page = 'keuangan'; 
include '../Config/auth.php';
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

<?php include '../include/sidebar.php'; ?>

<div class="main">
  <div class="topbar">
    <h1>Keuangan</h1>
    <div class="topbar-right">
      <div class="user-chip">
        <div class="user-avatar">
          <svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
        Administrator TEFA
      </div>
    </div>
  </div>

  <div class="content-area">
    <div class="summary-label">Ringkasan Keuangan</div>
    <div class="summary-row">
      <div class="summary-card">
        <div class="summary-card-label">Total Saldo</div>
        <div class="summary-card-value" id="totalSaldo">Rp 0</div>
      </div>
      <div class="summary-card">
        <div class="summary-card-label">Pemasukan Bulan Ini</div>
        <div class="summary-card-value" id="totalPemasukan">Rp 0</div>
      </div>
      <div class="summary-card">
        <div class="summary-card-label">Penarikan Bulan Ini</div>
        <div class="summary-card-value" id="totalPenarikan">Rp 0</div>
      </div>
    </div>

    <div class="transaksi-header">
      <span class="transaksi-title">Data Transaksi</span>
      <button class="btn-input" onclick="openInputModal()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Input Data
      </button>
    </div>

    <div class="filter-row">
      <div class="search-wrap">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input class="search-input" id="searchInput" type="text" placeholder="Cari..." />
      </div>
      <div class="dropdown-filter">
        <span id="dateLabel">Bulan Ini</span>
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
        <select id="dateFilter" onchange="applyFilters()">
          <option value="all">Semua</option>
          <option value="today" selected>Bulan Ini</option>
        </select>
      </div>
      <div class="dropdown-filter">
        <span id="kategoriLabel">Kategori</span>
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
        <select id="kategoriFilter" onchange="applyFilters()">
          <option value="all">Semua</option>
          <option value="Pemasukan">Pemasukan</option>
          <option value="Penarikan">Penarikan</option>
        </select>
      </div>
    </div>

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
        <span class="page-info" id="pageInfo">Menampilkan 0 data</span>
        <div class="page-controls" id="pageControls"></div>
      </div>
    </div>
  </div>
</div>

<div class="overlay" id="overlay" onclick="closeOverlay(event)">
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
          <option value="Penarikan">Penarikan Uang</option>
          <option value="Pemasukan">Pemasukan Manual</option>
        </select>
      </div>
      <div class="form-group full">
        <label class="form-label">Keterangan</label>
        <input class="form-input" id="fKeterangan" type="text" placeholder="Contoh: Beli bahan baku, Setor kas kampus..." />
      </div>
      <div class="form-group">
        <label class="form-label">Nominal (Rp)</label>
        <input class="form-input" id="fNominal" type="number" min="0" placeholder="Contoh: 200000" />
      </div>
      <div class="form-group">
        <label class="form-label">Status</label>
        <select class="form-select" id="fStatus">
          <option value="Selesai">Selesai</option>
          <option value="Menunggu">Menunggu</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-save" id="btnSave" onclick="saveFinanceData()">Simpan</button>
    </div>
  </div>

  <div class="modal modal-sm" id="deleteModal" style="display:none" onclick="event.stopPropagation()">
    <div class="confirm-icon"><svg width="26" height="26" fill="none" stroke="#e53935" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg></div>
    <div class="confirm-title">Hapus Transaksi?</div>
    <div class="confirm-desc">Kamu yakin ingin menghapus transaksi <strong id="deleteItemName">–</strong>?</div>
    <div class="modal-footer" style="justify-content:center">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-danger" onclick="confirmDelete()">Ya, Hapus</button>
    </div>
  </div>
</div>

<div class="toast-container" id="toastContainer"></div>
<script src="../Assets/JS/keuangan.js"></script>
</body>
</html>