<?php 
$page = 'inventory'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tefa Bakery & Coffee – Inventory Produk</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../Assets/CSS/globaladmin.css" />
  <link rel="stylesheet" href="../Assets/CSS/inventory.css" />
</head>
<body>

<?php include '../include/sidebar.php'; ?>

<div class="main">
  <div class="topbar">
    <h1>Inventory Produk</h1>
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
    <div class="action-row">
      <div>
        <span class="section-title">Manajemen Produk & Stok</span>
        <p style="font-size: 12px; color: var(--text-sub); margin-top: 4px;">Atur data produk yang akan tampil di menu Kasir dan Customer.</p>
      </div>
      <div class="action-right">
        <button class="btn-input" onclick="openInputModal()">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Input Produk
        </button>
      </div>
    </div>

    <div class="search-wrap">
      <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input class="search-input" id="searchInput" type="text" placeholder="Cari nama produk..." />
    </div>

  <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>No.</th>
            <th>Gambar</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="tableBody">
        </tbody>
      </table>
      <div class="pagination">
        <span class="page-info" id="pageInfo"></span>
        <div class="page-controls" id="pageControls"></div>
      </div>
    </div>
  </div>
</div>

<div class="overlay" id="overlay" onclick="closeOverlay(event)">
  
  <div class="modal" id="inputModal" onclick="event.stopPropagation()">
    <div class="modal-header">
      <span class="modal-title" id="modalTitle">Input Produk Baru</span>
      <button class="modal-close" onclick="closeModal()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div style="margin-bottom: 20px; text-align: center;">
      <div class="img-upload-box" onclick="document.getElementById('fGambar').click()">
        <img id="imgPreview" src="" style="display:none; width:100%; height:100%; object-fit:cover; border-radius:10px;">
        <div id="imgPlaceholder">
          <svg width="24" height="24" fill="none" stroke="#8a7060" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          <span style="display:block; font-size:11px; margin-top:5px; color:var(--text-sub)">Upload Foto Produk</span>
        </div>
      </div>
      <input type="file" id="fGambar" hidden accept="image/*" onchange="previewImage(event)">
    </div>

    <div class="form-grid">
      <div class="form-group full">
        <label class="form-label">Nama Produk</label>
        <input class="form-input" id="fNama" type="text" placeholder="Contoh: Roti Tawar Gandum" />
      </div>
      <div class="form-group">
        <label class="form-label">Kategori</label>
        <select class="form-select" id="fKategori">
          <option value="Bread">Bread</option>
          <option value="Coffee">Coffee</option>
          <option value="Snacks">Snacks</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Stok Awal</label>
        <input class="form-input" id="fStok" type="number" placeholder="0" />
      </div>
      <div class="form-group full">
        <label class="form-label">Harga Jual (Rp)</label>
        <input class="form-input" id="fHarga" type="text" onkeyup="formatHarga(this)" placeholder="0" />
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-save" id="btnSave" onclick="saveData()">Simpan & Publikasikan</button>
    </div>
  </div>

  <div class="modal modal-sm" id="editHargaModal" style="display:none" onclick="event.stopPropagation()">
    <div class="modal-header">
      <span class="modal-title">Ubah Harga Produk</span>
    </div>
    <div style="text-align: left; margin-bottom: 15px;">
      <p id="editHargaNama" style="font-weight: 700; color: var(--text-main);"></p>
      <p style="font-size: 12px; color: var(--text-sub);">Harga saat ini: <span id="editHargaSekarang"></span></p>
    </div>
    <input class="form-input" id="editHargaBaru" type="text" onkeyup="formatHarga(this)" placeholder="Masukkan harga baru..." style="width: 100%;">
    <div class="modal-footer" style="margin-top: 20px;">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-save" onclick="confirmEditHarga()">Update Harga</button>
    </div>
  </div>

</div>

<div class="toast-container" id="toastContainer"></div>

<script src="../Assets/JS/inventory.js"></script>
</body>
</html>