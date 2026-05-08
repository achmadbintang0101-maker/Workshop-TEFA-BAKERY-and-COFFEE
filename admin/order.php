<?php 
$page = 'order'; 

// Panggil file auth untuk proteksi halaman dan mengambil session
include '../Config/auth.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tefa Bakery & Coffee – Order</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../Assets/CSS/globaladmin.css" />
  <link rel="stylesheet" href="../Assets/CSS/order.css" />
</head>
<body>

<?php include '../include/sidebar.php'; ?>

<div class="main">
  <div class="topbar">
    <h1>Order</h1>
    <div class="topbar-right">
      <div class="user-chip">
        <div class="user-avatar">
            <svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <?= htmlspecialchars($_SESSION['nama'] ?? 'Admin'); ?>
      </div>
    </div>
  </div>

  <div class="content-area">
    <div class="center-panel">
      <div class="section-header">
        <span class="section-title">Data Pesanan</span>
        <button class="btn-input" onclick="openInputModal()">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Input Data
        </button>
      </div>

      <div class="filter-row">
        <div class="search-wrap">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input class="search-input" id="searchInput" type="text" placeholder="Cari order ID..." />
        </div>
        <div class="date-filter" onclick="showToast('Filter tanggal belum tersedia','info')">
          Hari Ini: <?= date('d F Y'); ?> <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>No.</th><th>Order ID</th><th>Tanggal</th><th>Jumlah</th><th>Proses</th><th>Aksi</th></tr>
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

    <div class="right-panel">
      <div class="activity-card">
        <div class="activity-header">Aktivitas Terbaru</div>
        <div class="activity-body" id="activityBody">
            </div>
        <div class="activity-footer">
          <button class="btn-lihat" onclick="openAllActivity()">Lihat Semua</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="overlay" id="overlay" onclick="closeOverlay(event)">

  <div class="modal" id="inputModal" style="display:none; width: 600px;" onclick="event.stopPropagation()">
    <div class="modal-header">
      <span class="modal-title" id="modalTitle">Input Pesanan Manual</span>
      <button class="modal-close" onclick="closeModal()"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    
    <div style="max-height: 60vh; overflow-y: auto; padding-right: 5px;">
        <div style="font-size: 13px; font-weight: 700; color: var(--accent-brown); margin-bottom: 10px; text-transform: uppercase;">1. Data Pelanggan</div>
        <div class="form-grid" style="margin-bottom: 20px;">
            <div class="form-group full">
                <label class="form-label">Nama Pemesan</label>
                <input class="form-input" id="mNama" type="text" placeholder="Contoh: Panitia BEM / Pak Dosen" required />
            </div>
            <div class="form-group full">
                <label class="form-label">Kategori / Role</label>
                <select class="form-select" id="mRole">
                    <option value="umum">Umum</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                </select>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <div style="font-size: 13px; font-weight: 700; color: var(--accent-brown); text-transform: uppercase;">2. Detail Keranjang</div>
            <button type="button" class="btn-input" style="padding: 5px 12px; font-size: 11px;" onclick="tambahBarisProduk()">+ Tambah Produk</button>
        </div>
        <div id="produkContainer" style="margin-bottom: 20px; background: #f9f5f0; padding: 15px; border-radius: 10px; border: 1px solid var(--border);">
            </div>

        <div style="font-size: 13px; font-weight: 700; color: var(--accent-brown); margin-bottom: 10px; text-transform: uppercase;">3. Pembayaran</div>
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Status Pesanan</label>
                <select class="form-select" id="mStatus">
                    <option value="pending">Belum Lunas / Pre-Order (Masuk Proses)</option>
                    <option value="selesai">Sudah Lunas (Masuk Selesai)</option>
                </select>
            </div>
        </div>
    </div>

    <div class="modal-footer" style="margin-top: 20px;">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-save" id="btnSave" onclick="simpanPesananManual()">Simpan Pesanan</button>
    </div>
  </div>

  <div class="modal" id="detailModal" style="display:none" onclick="event.stopPropagation()">
    <div class="modal-header">
      <span class="modal-title" id="detailTitle">Detail Order</span>
      <button class="modal-close" onclick="closeModal()"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div id="detailContent"></div>
    <div class="modal-footer" id="detailFooter"></div>
  </div>

  <div class="modal modal-sm" id="deleteModal" style="display:none" onclick="event.stopPropagation()">
    <div class="confirm-icon"><svg width="26" height="26" fill="none" stroke="#e53935" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg></div>
    <div class="confirm-title">Hapus Order?</div>
    <div class="confirm-desc">Kamu yakin ingin menghapus order <strong id="deleteItemName">–</strong>?</div>
    <div class="modal-footer" style="justify-content:center">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-danger" onclick="confirmDelete()">Ya, Hapus</button>
    </div>
  </div>

  <div class="modal" id="konfirmasiModal" style="display:none; width:400px" onclick="event.stopPropagation()">
    <div class="modal-header">
      <span class="modal-title">Konfirmasi Order Selesai</span>
      <button class="modal-close" onclick="closeModal()"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div style="background:#f9f5f0;border-radius:10px;padding:14px 16px;margin-bottom:18px;font-size:13.5px;color:var(--text-sub);line-height:1.6">
      Order: <strong id="konfirmasiOrderId" style="color:var(--text-main)">–</strong><br>
      Jumlah: <strong id="konfirmasiJumlah" style="color:var(--text-main)">–</strong> pcs
    </div>
    <div class="form-group" style="margin-bottom:14px">
      <label class="form-label">Jumlah Terpenuhi</label>
      <div class="quantity-row">
        <button class="qty-btn" onclick="changeKonfirmasiQty(-1)">−</button>
        <input class="qty-input" id="konfirmasiQty" type="number" value="0" min="0" />
        <button class="qty-btn" onclick="changeKonfirmasiQty(1)">+</button>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Catatan</label>
      <textarea class="form-textarea" id="konfirmasiNote" placeholder="Contoh: Order selesai tepat waktu..." style="min-height:60px"></textarea>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-selesai" onclick="confirmSelesai()">✓ Selesaikan Order</button>
    </div>
  </div>

  <div class="modal all-activity-modal" id="allActivityModal" style="display:none" onclick="event.stopPropagation()">
    <div class="modal-header">
      <span class="modal-title">Semua Aktivitas</span>
      <button class="modal-close" onclick="closeModal()"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="modal-body" id="allActivityBody"></div>
    <div class="modal-footer"><button class="btn-cancel" onclick="closeModal()">Tutup</button></div>
  </div>

</div>

<div class="toast-container" id="toastContainer"></div>

<script src="../Assets/JS/order.js"></script>
</body>
</html>