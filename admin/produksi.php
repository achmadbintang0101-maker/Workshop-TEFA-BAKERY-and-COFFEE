<?php 
$page = 'produksi'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tefa Bakery & Coffee – Produksi</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../Assets/CSS/globaladmin.css" />
  <link rel="stylesheet" href="../Assets/CSS/produksi.css" />
</head>
<body>

<!-- ── SIDEBAR ── -->
<?php include '../include/sidebar.php'; ?>
<!-- MAIN -->
<div class="main">
  <div class="topbar">
    <h1>Produksi</h1>
    <div class="topbar-right">
      <button class="bell-btn" onclick="toggleNotif()" id="bellBtn">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
        <span class="bell-badge" id="bellBadge"></span>
      </button>
      <div class="user-chip">
        <div class="user-avatar"><svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
        Pak Dwiki
      </div>
    </div>
  </div>

  <div class="content-area">
    <!-- Center Panel -->
    <div class="center-panel">
      <div class="section-header">
        <span class="section-title">Jadwal Produksi</span>
        <button class="btn-input" onclick="openInputModal()">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Input Data
        </button>
      </div>

      <div class="filter-row">
        <div class="search-wrap">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input class="search-input" id="searchInput" type="text" placeholder="Cari produk..." />
        </div>
        <div class="date-filter" onclick="showToast('Filter tanggal belum tersedia','info')">
          Hari Ini: 20 April 2026
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>No.</th><th>Produk</th><th>Tanggal</th><th>Jumlah</th><th>Proses</th><th>Aksi</th></tr>
          </thead>
          <tbody id="tableBody"></tbody>
        </table>
        <div class="pagination">
          <span class="page-info" id="pageInfo"></span>
          <div class="page-controls" id="pageControls"></div>
        </div>
      </div>
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
      <div class="activity-card">
        <div class="activity-header">Aktivitas Terbaru</div>
        <div class="activity-body" id="activityBody"></div>
        <div class="activity-footer">
          <button class="btn-lihat" onclick="openAllActivity()">Lihat Semua</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ══════ NOTIF PANEL ══════ -->
<div class="notif-overlay" id="notifOverlay" onclick="closeNotif()"></div>
<div class="notif-panel" id="notifPanel">
  <div class="notif-header">
    <span class="notif-title-text">Notifikasi</span>
    <button class="notif-close" onclick="closeNotif()"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
  </div>
  <div class="notif-list" id="notifList"></div>
  <div class="notif-footer"><span class="notif-mark-all" onclick="markAllRead()">Tandai Semua Dibaca</span></div>
</div>

<!-- ══════ OVERLAY ══════ -->
<div class="overlay" id="overlay" onclick="closeOverlay(event)">

  <!-- INPUT / EDIT MODAL -->
  <div class="modal" id="inputModal" onclick="event.stopPropagation()">
    <div class="modal-header">
      <span class="modal-title" id="modalTitle">Tambah Jadwal Produksi</span>
      <button class="modal-close" onclick="closeModal()"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="form-grid">
      <div class="form-group full">
        <label class="form-label">Nama Produk</label>
        <input class="form-input" id="fNama" type="text" placeholder="Masukkan nama produk..." />
      </div>
      <div class="form-group">
        <label class="form-label">Tanggal Produksi</label>
        <input class="form-input" id="fTanggal" type="date" />
      </div>
      <div class="form-group">
        <label class="form-label">Jumlah (pcs)</label>
        <input class="form-input" id="fJumlah" type="number" min="1" placeholder="Contoh: 100" />
      </div>
      <div class="form-group">
        <label class="form-label">Status Proses</label>
        <select class="form-select" id="fStatus">
          <option value="Terjadwal">Terjadwal</option>
          <option value="Sedang Diproses">Sedang Diproses</option>
          <option value="Selesai">Selesai</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Penanggung Jawab</label>
        <input class="form-input" id="fPJ" type="text" placeholder="Nama PJ..." />
      </div>
      <div class="form-group full">
        <label class="form-label">Catatan</label>
        <textarea class="form-textarea" id="fCatatan" placeholder="Catatan tambahan (opsional)..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-save" id="btnSave" onclick="saveData()">Simpan</button>
    </div>
  </div>

  <!-- DETAIL MODAL -->
  <div class="modal" id="detailModal" style="display:none" onclick="event.stopPropagation()">
    <div class="modal-header">
      <span class="modal-title" id="detailTitle">Detail Produksi</span>
      <button class="modal-close" onclick="closeModal()"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div id="detailContent"></div>
    <div class="modal-footer" id="detailFooter"></div>
  </div>

  <!-- DELETE CONFIRM -->
  <div class="modal modal-sm" id="deleteModal" style="display:none" onclick="event.stopPropagation()">
    <div class="confirm-icon"><svg width="26" height="26" fill="none" stroke="#e53935" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg></div>
    <div class="confirm-title">Hapus Produksi?</div>
    <div class="confirm-desc">Kamu yakin ingin menghapus jadwal produksi <strong id="deleteItemName">–</strong>?</div>
    <div class="modal-footer" style="justify-content:center">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-danger" onclick="confirmDelete()">Ya, Hapus</button>
    </div>
  </div>

  <!-- KONFIRMASI PROSES (centang) -->
  <div class="modal" id="prosesModal" style="display:none; width:400px" onclick="event.stopPropagation()">
    <div class="modal-header">
      <span class="modal-title">Konfirmasi Hasil Produksi</span>
      <button class="modal-close" onclick="closeModal()"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div style="background:#f9f5f0;border-radius:10px;padding:14px 16px;margin-bottom:18px;font-size:13.5px;color:var(--text-sub);line-height:1.6">
      Produk: <strong id="prosesItemName" style="color:var(--text-main)">–</strong><br>
      Target: <strong id="prosesTarget" style="color:var(--text-main)">–</strong> pcs
    </div>
    <div class="form-group" style="margin-bottom:14px">
      <label class="form-label">Jumlah Hasil Produksi</label>
      <div class="quantity-row">
        <button class="qty-btn" onclick="changeProsesQty(-5)">−</button>
        <input class="qty-input" id="prosesQty" type="number" value="30" min="0" />
        <button class="qty-btn" onclick="changeProsesQty(5)">+</button>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Catatan Hasil</label>
      <textarea class="form-textarea" id="prosesNote" placeholder="Contoh: Selesai tepat waktu, kualitas baik..." style="min-height:60px"></textarea>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-selesai" onclick="confirmProses()">✓ Selesaikan Produksi</button>
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

  <!-- ALL ACTIVITY MODAL -->
  <div class="modal all-activity-modal" id="allActivityModal" style="display:none" onclick="event.stopPropagation()">
    <div class="modal-header">
      <span class="modal-title">Semua Aktivitas</span>
      <button class="modal-close" onclick="closeModal()"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="modal-body" id="allActivityBody"></div>
    <div class="modal-footer"><button class="btn-cancel" onclick="closeModal()">Tutup</button></div>
  </div>

</div>

<!-- Toast -->
<div class="toast-container" id="toastContainer"></div>

<script src="../Assets/JS/produksi.js"></script>
</body>
</html>
