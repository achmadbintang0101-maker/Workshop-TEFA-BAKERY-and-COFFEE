// DATA PENAMPUNG
let allData = [];
let activities = [];

const PER_PAGE = 5;
let currentPage = 1;
let filteredData = [...allData];
let editingId = null;
let deletingId = null;
let konfirmasiId = null;

// =========================================================
// 1. FUNGSI FETCH: MENGAMBIL DATA DARI DATABASE (API)
// =========================================================
async function fetchOrders() {
    try {
        const response = await fetch('../Controllers/OrderController.php?action=get_all_orders');
        const data = await response.json();
        
        allData = data;
        
        const q = document.getElementById('searchInput').value.toLowerCase();
        if (q) {
            filteredData = allData.filter(r => r.orderId.toLowerCase().includes(q) || r.status.toLowerCase().includes(q));
        } else {
            filteredData = [...allData];
        }
        
        renderTable();
    } catch (error) {
        console.error("Gagal mengambil data dari database:", error);
        showToast("Gagal memuat data pesanan dari server", "error");
    }
}

// =========================================================
// 2. RENDER TABLE & UI
// =========================================================
function badgeHtml(s) {
  const map = { 'Selesai':'badge-green', 'Proses':'badge-orange' };
  return `<span class="badge ${map[s]||'badge-gray'}">${s}</span>`;
}

function fmtDate(d) {
  const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const dt = new Date(d);
  return `${dt.getDate()} ${months[dt.getMonth()]} ${dt.getFullYear()}`;
}

function renderTable() {
  const tbody = document.getElementById('tableBody');
  const start = (currentPage-1)*PER_PAGE;
  const pageData = filteredData.slice(start, start+PER_PAGE);

  if (pageData.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" style="padding: 50px; text-align: center; color: #999;">Belum ada data pesanan di database.</td></tr>';
      document.getElementById('pageInfo').textContent = 'Menampilkan 0 data';
      document.getElementById('pageControls').innerHTML = '';
      return;
  }

  tbody.innerHTML = pageData.map(row => `
    <tr>
      <td class="no">${row.no}</td>
      <td class="bold">${row.orderId}</td>
      <td>${fmtDate(row.tanggal)}</td>
      <td>${row.jumlah}</td>
      <td>${badgeHtml(row.status)}</td>
      <td>
        <div class="aksi-wrap">
          ${row.status === 'Proses' && row.aksiNum !== null ? `
            <span class="aksi-num">${row.aksiNum}</span>
            <button class="btn-icon check" onclick="openKonfirmasi(${row.id})" title="Selesaikan">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </button>
          ` : `
            <button class="btn-icon view-btn" onclick="openDetail(${row.id})" title="Lihat Detail">
              <svg width="13" height="13" fill="none" stroke="#4a80b5" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
            <button class="btn-icon" onclick="openDelete(${row.id})" title="Hapus">
              <svg width="13" height="13" fill="none" stroke="#7a6a5a" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg>
            </button>
          `}
        </div>
      </td>
    </tr>
  `).join('');

  const total = filteredData.length;
  const end = Math.min(start+PER_PAGE, total);
  document.getElementById('pageInfo').textContent = `Menampilkan ${start+1} sampai ${end} dari ${total}`;
  renderPagination(total);
}

function renderPagination(total) {
  const totalPages = Math.ceil(total/PER_PAGE);
  const ctrl = document.getElementById('pageControls');
  let html = '';
  for (let i=1; i<=totalPages; i++) {
    html += `<button class="page-btn${i===currentPage?' active':''}" onclick="setPage(${i})">${i}</button>`;
  }
  html += `<button class="page-nav" onclick="prevPage()">Sebelumnya</button><button class="page-nav" onclick="nextPage()">Berikutnya</button>`;
  ctrl.innerHTML = html;
}

function setPage(p) { const t=Math.ceil(filteredData.length/PER_PAGE); if(p<1||p>t)return; currentPage=p; renderTable(); }
function prevPage() { setPage(currentPage-1); }
function nextPage() { setPage(currentPage+1); }

document.getElementById('searchInput').addEventListener('input', function() {
  const q = this.value.toLowerCase();
  filteredData = allData.filter(r => r.orderId.toLowerCase().includes(q) || r.status.toLowerCase().includes(q));
  currentPage = 1;
  renderTable();
});

// =========================================================
// 3. MODALS LOGIC UMUM
// =========================================================
function showModal(id) {
  document.querySelectorAll('.modal').forEach(m => m.style.display='none');
  document.getElementById(id).style.display='';
  document.getElementById('overlay').classList.add('active');
}
function closeModal() { document.getElementById('overlay').classList.remove('active'); editingId=null; deletingId=null; konfirmasiId=null; }
function closeOverlay(e) { if(e.target===document.getElementById('overlay')) closeModal(); }

// =========================================================
// 4. FUNGSI INPUT MANUAL (REAL DARI DATABASE)
// =========================================================
let availableProducts = []; // Tempat menyimpan data roti asli dari database

// Tarik data roti asli dengan memanfaatkan API yang sudah ada di ProductController
async function fetchProductsForManual() {
    try {
        const response = await fetch('../Controllers/ProductController.php?action=api_get_products');
        availableProducts = await response.json();
    } catch (error) {
        console.error("Gagal memuat daftar produk:", error);
    }
}

function openInputModal() {
  document.getElementById('mNama').value = '';
  document.getElementById('mRole').value = 'umum';
  document.getElementById('mStatus').value = 'pending';
  document.getElementById('produkContainer').innerHTML = ''; 
  
  tambahBarisProduk(); // Otomatis munculkan 1 baris keranjang kosong
  showModal('inputModal');
}

function tambahBarisProduk() {
    const container = document.getElementById('produkContainer');
    const row = document.createElement('div');
    row.style.display = 'flex';
    row.style.gap = '10px';
    row.style.alignItems = 'flex-end';
    row.style.marginBottom = '12px';
    row.className = 'product-row';

    // Loop data asli dari database ke dalam opsi dropdown
    let optionsHtml = availableProducts.map(p => 
        `<option value="${p.id}" data-price="${p.price}">${p.name} - Rp ${p.price.toLocaleString('id-ID')}</option>`
    ).join('');

    row.innerHTML = `
        <div class="form-group" style="flex: 2;">
            <label class="form-label" style="font-size: 10px;">Pilih Produk</label>
            <select class="form-select product-select">
                <option value="">-- Pilih Roti / Minuman --</option>
                ${optionsHtml}
            </select>
        </div>
        <div class="form-group" style="flex: 1;">
            <label class="form-label" style="font-size: 10px;">Qty</label>
            <input class="form-input product-qty" type="number" min="1" value="1" />
        </div>
        <button class="btn-icon" style="color: #e53935; height: 38px; width: 38px; flex-shrink: 0;" onclick="this.parentElement.remove()" title="Hapus Baris">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
        </button>
    `;
    container.appendChild(row);
}

async function simpanPesananManual() {
    const btn = document.getElementById('btnSave');
    const nama = document.getElementById('mNama').value.trim();
    const role = document.getElementById('mRole').value;
    const status = document.getElementById('mStatus').value;
    
    if (!nama) { showToast("Nama pemesan wajib diisi!", "error"); return; }
    
    // Kumpulkan semua data produk dari setiap baris yang ditambahkan
    const rows = document.querySelectorAll('.product-row');
    let items = [];
    let isValid = true;
    
    rows.forEach(row => {
        const select = row.querySelector('.product-select');
        const qtyInput = row.querySelector('.product-qty');
        
        const id_product = select.value;
        const qty = parseInt(qtyInput.value) || 0;
        
        if (!id_product || qty <= 0) {
            isValid = false; // Tandai error jika ada baris yang belum dipilih rotinya
        } else {
            const price = parseFloat(select.options[select.selectedIndex].getAttribute('data-price'));
            items.push({ id: id_product, qty: qty, price: price });
        }
    });
    
    if (!isValid || items.length === 0) {
        showToast("Pastikan semua roti dipilih dan Qty lebih dari 0!", "error");
        return;
    }
    
    // Ubah tombol jadi "Menyimpan..."
    btn.textContent = "Menyimpan...";
    btn.disabled = true;
    
    try {
        // Kirim data ke API backend
        const response = await fetch('../Controllers/OrderController.php?action=create_manual_order', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nama, role, status, items })
        });
        
        const resData = await response.json();
        
        if (resData.status === 'success') {
            showToast(resData.message, 'success');
            closeModal();
            // Refresh tabel & log aktivitas secara realtime!
            await fetchOrders();
            await fetchActivities();
        } else {
            showToast(resData.message, 'error');
        }
    } catch (error) {
        showToast('Terjadi kesalahan jaringan saat menyimpan data', 'error');
    } finally {
        // Kembalikan tombol ke kondisi semula
        btn.textContent = "Simpan Pesanan";
        btn.disabled = false;
    }
}

function openEdit(id) {
  showToast("Pesanan yang sudah masuk tidak bisa diedit untuk mencegah manipulasi data. Silakan Hapus/Void dan buat baru.", "error");
}

// =========================================================
// 5. DETAIL DAN DELETE (SUDAH DIPERBARUI)
// =========================================================
async function openDetail(id) {
  const item = allData.find(r => r.id === id);
  document.getElementById('detailTitle').textContent = item.orderId;
  
  // Tampilkan efek loading sementara ke Admin
  document.getElementById('detailContent').innerHTML = '<div style="padding: 30px; text-align: center; color: #888;">Memuat rincian pesanan...</div>';
  showModal('detailModal'); // Buka modalnya dulu

  try {
      // Minta data ke Back-End
      const response = await fetch(`../Controllers/OrderController.php?action=get_detail&id=${id}`);
      const resData = await response.json();
      
      let listBarangHtml = '';
      
      // Jika sukses ambil data barang, buat susunan HTML-nya
      if (resData.status === 'success' && resData.items.length > 0) {
          listBarangHtml = '<div style="margin-top: 20px; border-top: 1px dashed #d3c5bd; padding-top: 15px;"><div style="font-size: 11px; font-weight: 700; color: #8a7060; margin-bottom: 12px; letter-spacing: 0.5px;">RINCIAN BARANG:</div>';
          
          resData.items.forEach(brg => {
              listBarangHtml += `
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 13.5px;">
                      <div>
                          <div style="font-weight: 600; color: #3b1414;">${brg.name}</div>
                          <div style="font-size: 12px; color: #888; margin-top: 2px;">${brg.qty} x Rp ${brg.price.toLocaleString('id-ID')}</div>
                      </div>
                      <div style="font-weight: 700; color: #3b1414;">Rp ${brg.subtotal.toLocaleString('id-ID')}</div>
                  </div>
              `;
          });
          listBarangHtml += '</div>';
      }

      // Gabungkan info Header (Tanggal, Jumlah) dengan Rincian Barang
      document.getElementById('detailContent').innerHTML = `
        <div class="detail-grid">
          <div class="detail-item"><div class="detail-label">Tanggal</div><div class="detail-value">${fmtDate(item.tanggal)}</div></div>
          <div class="detail-item"><div class="detail-label">Total Qty</div><div class="detail-value">${item.jumlah} pcs</div></div>
          <div class="detail-item detail-full"><div class="detail-label">Status</div><div class="detail-value">${badgeHtml(item.status)}</div></div>
        </div>
        ${listBarangHtml}
      `;
      
      // Atur Tombol Footer
      let footer = `<button class="btn-cancel" onclick="closeModal()">Tutup</button>`;
      if (item.status === 'Proses') footer += `<button class="btn-selesai" onclick="openKonfirmasi(${item.id}); closeModal()">✓ Selesaikan</button>`;
      document.getElementById('detailFooter').innerHTML = footer;
      
  } catch (error) {
      document.getElementById('detailContent').innerHTML = '<div style="padding: 20px; text-align: center; color: #e53935; font-weight: 600;">Terjadi kesalahan sistem saat memuat barang.</div>';
  }
}

function openDelete(id) {
  const item = allData.find(r=>r.id===id);
  deletingId = id;
  document.getElementById('deleteItemName').textContent = item.orderId;
  showModal('deleteModal');
}

async function confirmDelete() {
  const item = allData.find(r=>r.id===deletingId);
  
  try {
      const response = await fetch('../Controllers/OrderController.php?action=delete', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: deletingId })
      });
      const resData = await response.json();

      if (resData.status === 'success') {
          showToast(`Order "${item.orderId}" berhasil dihapus`, 'success');
          closeModal();
          await fetchOrders(); 
          await fetchActivities();
      } else {
          showToast(resData.message, 'error');
      }
  } catch (error) {
      showToast('Terjadi kesalahan saat menghapus pesanan', 'error');
  }
}

// =========================================================
// 6. KONFIRMASI SELESAI
// =========================================================
function openKonfirmasi(id) {
  const item = allData.find(r=>r.id===id);
  konfirmasiId = id;
  document.getElementById('konfirmasiOrderId').textContent = item.orderId;
  document.getElementById('konfirmasiJumlah').textContent = item.jumlah;
  document.getElementById('konfirmasiQty').value = item.aksiNum || item.jumlah;
  document.getElementById('konfirmasiNote').value = '';
  showModal('konfirmasiModal');
}
function changeKonfirmasiQty(delta) {
  const inp = document.getElementById('konfirmasiQty');
  inp.value = Math.max(0, parseInt(inp.value||0)+delta);
}

async function confirmSelesai() {
  const item = allData.find(r=>r.id===konfirmasiId);
  const hasil = parseInt(document.getElementById('konfirmasiQty').value)||0;

  try {
      const response = await fetch('../Controllers/OrderController.php?action=selesai', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: konfirmasiId })
      });
      const resData = await response.json();

      if (resData.status === 'success') {
          showToast(`Order "${item.orderId}" selesai! (${hasil} pcs)`, 'success');
          closeModal();
          await fetchOrders(); 
          await fetchActivities();
      } else {
          showToast(resData.message, 'error');
      }
  } catch (error) {
      showToast('Terjadi kesalahan saat mengupdate status', 'error');
  }
}

// =========================================================
// 7. ACTIVITY PANEL
// =========================================================
async function fetchActivities() {
    try {
        const response = await fetch('../Controllers/OrderController.php?action=get_activities');
        activities = await response.json();
        renderActivity();
    } catch (error) {
        console.error("Gagal memuat aktivitas:", error);
    }
}

function renderActivity() {
  const body = document.getElementById('activityBody');
  
  if (activities.length === 0) {
      body.innerHTML = '<div style="padding: 30px 20px; text-align: center; color: #999; font-size: 13px;">Belum ada riwayat transaksi.</div>';
      return;
  }

  const grouped = {};
  activities.forEach(a => {
    if (!grouped[a.date]) grouped[a.date] = [];
    grouped[a.date].push(a);
  });
  
  let html = '';
  Object.entries(grouped).forEach(([date, items]) => {
    html += `<div class="activity-date">${date}</div>`;
    items.forEach(a => {
      html += `<div class="activity-item">
        <span class="act-time">${a.time}</span>
        <div class="act-content">
          <div class="act-title">${a.title} <span class="act-badge ${a.type}">${a.type==='green'?'Selesai':'Proses'}</span></div>
          ${a.sub?`<div class="act-sub">${a.sub}</div>`:''}
        </div>
        <span class="act-arrow">›</span>
      </div>`;
    });
  });
  body.innerHTML = html;
}

function openAllActivity() {
    document.getElementById('allActivityBody').innerHTML = document.getElementById('activityBody').innerHTML;
    showModal('allActivityModal');
}

// =========================================================
// TOAST NOTIFICATION
// =========================================================
function showToast(msg, type='info') {
  const icons = {
    success:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>',
    error:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
    info:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
  };
  const el = document.createElement('div');
  el.className = `toast ${type}`;
  el.innerHTML = `<span class="toast-icon">${icons[type]||icons.info}</span>${msg}<div class="toast-bar"></div>`;
  document.getElementById('toastContainer').appendChild(el);
  setTimeout(()=>{ el.style.opacity='0'; el.style.transform='translateX(20px)'; el.style.transition='all 0.3s'; setTimeout(()=>el.remove(),300); },3000);
}

// =========================================================
// INIT
// =========================================================
async function initPage() {
    await fetchOrders();
    await fetchActivities();
    await fetchProductsForManual();
}

initPage();