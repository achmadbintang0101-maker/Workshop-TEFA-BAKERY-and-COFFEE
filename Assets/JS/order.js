// DATA
let allData = [
  { id:1, no:'01', orderId:'Mahasiswa', tanggal:'2026-03-17', jumlah:10,  status:'Proses',  aksiNum:10 },
  { id:2, no:'02', orderId:'Dosen TI',  tanggal:'2026-03-17', jumlah:100, status:'Proses',  aksiNum:null },
  { id:3, no:'03', orderId:'Mahasiswa', tanggal:'2026-03-17', jumlah:5,   status:'Selesai', aksiNum:null },
  { id:4, no:'04', orderId:'Mahasiswa', tanggal:'2026-03-17', jumlah:15,  status:'Selesai', aksiNum:null },
  { id:5, no:'05', orderId:'Staff TU',  tanggal:'2026-04-01', jumlah:20,  status:'Proses',  aksiNum:8 },
  { id:6, no:'06', orderId:'Karyawan',  tanggal:'2026-04-05', jumlah:30,  status:'Selesai', aksiNum:null },
  { id:7, no:'07', orderId:'Mahasiswa', tanggal:'2026-04-10', jumlah:12,  status:'Proses',  aksiNum:null },
  { id:8, no:'08', orderId:'Dosen FEB', tanggal:'2026-04-15', jumlah:50,  status:'Proses',  aksiNum:null },
];

let activities = [
  { time:'09:00', date:'21 April 2026', title:'Order Baru Masuk', sub:'Mahasiswa — 10 pcs', type:'orange' },
  { time:'09:30', date:'21 April 2026', title:'Order Diproses', sub:'Dosen TI — 100 pcs', type:'orange' },
  { time:'10:00', date:'21 April 2026', title:'Order Selesai', sub:'Mahasiswa — 5 pcs', type:'green' },
  { time:'10:20', date:'21 April 2026', title:'Order Selesai', sub:'Mahasiswa — 15 pcs', type:'green' },
  { time:'11:00', date:'20 April 2026', title:'Order Baru Masuk', sub:'Staff TU — 20 pcs', type:'orange' },
  { time:'13:00', date:'20 April 2026', title:'Order Selesai', sub:'Karyawan — 30 pcs', type:'green' },
  { time:'08:30', date:'19 April 2026', title:'Order Baru Masuk', sub:'Mahasiswa — 12 pcs', type:'orange' },
  { time:'14:00', date:'18 April 2026', title:'Order Baru Masuk', sub:'Dosen FEB — 50 pcs', type:'orange' },
];

let notifications = [
  { id:1, type:'orange', text:'<strong>Mahasiswa</strong> — order 10 pcs sedang diproses.', time:'5 menit lalu', read:false },
  { id:2, type:'orange', text:'<strong>Dosen TI</strong> — order 100 pcs sedang diproses.', time:'20 menit lalu', read:false },
  { id:3, type:'green',  text:'Order <strong>Mahasiswa</strong> selesai. 5 pcs.', time:'1 jam lalu', read:false },
  { id:4, type:'green',  text:'Order <strong>Mahasiswa</strong> selesai. 15 pcs.', time:'2 jam lalu', read:true },
  { id:5, type:'gray',   text:'Laporan order harian telah dibuat.', time:'Kemarin', read:true },
];

const PER_PAGE = 5;
let currentPage = 1;
let filteredData = [...allData];
let editingId = null;
let deletingId = null;
let konfirmasiId = null;
let nextId = 9;

// RENDER TABLE
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
            <button class="btn-icon" onclick="openEdit(${row.id})" title="Edit">
              <svg width="13" height="13" fill="none" stroke="#7a6a5a" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
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
  document.getElementById('pageInfo').textContent = `Menampilkan ${total===0?0:start+1} sampai ${end} dari ${total}`;
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

// MODALS
function showModal(id) {
  document.querySelectorAll('.modal').forEach(m => m.style.display='none');
  document.getElementById(id).style.display='';
  document.getElementById('overlay').classList.add('active');
}
function closeModal() { document.getElementById('overlay').classList.remove('active'); editingId=null; deletingId=null; konfirmasiId=null; }
function closeOverlay(e) { if(e.target===document.getElementById('overlay')) closeModal(); }

// INPUT / EDIT
function openInputModal() {
  editingId = null;
  document.getElementById('modalTitle').textContent = 'Tambah Order';
  document.getElementById('btnSave').textContent = 'Simpan';
  document.getElementById('fOrderId').value = '';
  document.getElementById('fJumlah').value = '';
  document.getElementById('fStatus').value = 'Proses';
  document.getElementById('fTanggal').value = new Date().toISOString().split('T')[0];
  showModal('inputModal');
}

function openEdit(id) {
  const item = allData.find(r=>r.id===id);
  editingId = id;
  document.getElementById('modalTitle').textContent = 'Edit Order';
  document.getElementById('btnSave').textContent = 'Perbarui';
  document.getElementById('fOrderId').value = item.orderId;
  document.getElementById('fTanggal').value = item.tanggal;
  document.getElementById('fJumlah').value = item.jumlah;
  document.getElementById('fStatus').value = item.status;
  showModal('inputModal');
}

function saveData() {
  const orderId = document.getElementById('fOrderId').value.trim();
  const tanggal = document.getElementById('fTanggal').value;
  const jumlah = parseInt(document.getElementById('fJumlah').value)||0;
  const status = document.getElementById('fStatus').value;
  if (!orderId||!tanggal||!jumlah) { showToast('Lengkapi semua field!','error'); return; }
  if (editingId) {
    const idx = allData.findIndex(r=>r.id===editingId);
    allData[idx] = {...allData[idx], orderId, tanggal, jumlah, status};
    showToast(`Order "${orderId}" berhasil diperbarui`, 'success');
    addActivity('green', `Order diperbarui: ${orderId}`, status);
  } else {
    const noStr = String(nextId).padStart(2,'0');
    allData.push({ id:nextId, no:noStr, orderId, tanggal, jumlah, status, aksiNum: status==='Proses' ? jumlah : null });
    nextId++;
    showToast(`Order "${orderId}" berhasil ditambahkan`, 'success');
    addActivity('gray', `Order baru: ${orderId}`, `${jumlah} pcs`);
    addNotif('gray', `Order baru dari <strong>${orderId}</strong> ditambahkan.`);
  }
  filteredData = [...allData];
  renderTable();
  renderActivity();
  closeModal();
}

// DETAIL
function openDetail(id) {
  const item = allData.find(r=>r.id===id);
  document.getElementById('detailTitle').textContent = item.orderId;
  document.getElementById('detailContent').innerHTML = `
    <div class="detail-grid">
      <div class="detail-item"><div class="detail-label">Tanggal</div><div class="detail-value">${fmtDate(item.tanggal)}</div></div>
      <div class="detail-item"><div class="detail-label">Jumlah</div><div class="detail-value">${item.jumlah} pcs</div></div>
      <div class="detail-item detail-full"><div class="detail-label">Status</div><div class="detail-value">${badgeHtml(item.status)}</div></div>
    </div>
  `;
  let footer = `<button class="btn-cancel" onclick="closeModal()">Tutup</button>`;
  if (item.status==='Proses') footer += `<button class="btn-selesai" onclick="openKonfirmasi(${item.id}); closeModal()">✓ Selesaikan</button>`;
  document.getElementById('detailFooter').innerHTML = footer;
  showModal('detailModal');
}

// DELETE
function openDelete(id) {
  const item = allData.find(r=>r.id===id);
  deletingId = id;
  document.getElementById('deleteItemName').textContent = item.orderId;
  showModal('deleteModal');
}
function confirmDelete() {
  const item = allData.find(r=>r.id===deletingId);
  allData = allData.filter(r=>r.id!==deletingId);
  filteredData = filteredData.filter(r=>r.id!==deletingId);
  if (currentPage > Math.ceil(filteredData.length/PER_PAGE) && currentPage>1) currentPage--;
  renderTable();
  showToast(`Order "${item.orderId}" dihapus`, 'error');
  addActivity('red', `Order dihapus: ${item.orderId}`, '-');
  renderActivity();
  closeModal();
}

// KONFIRMASI SELESAI
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
function confirmSelesai() {
  const item = allData.find(r=>r.id===konfirmasiId);
  const hasil = parseInt(document.getElementById('konfirmasiQty').value)||0;
  const idx = allData.findIndex(r=>r.id===konfirmasiId);
  allData[idx].status = 'Selesai';
  allData[idx].aksiNum = null;
  filteredData = [...allData];
  renderTable();
  addActivity('green', `Order selesai: ${item.orderId} — ${hasil} pcs`, 'Selesai');
  addNotif('green', `Order <strong>${item.orderId}</strong> selesai. ${hasil} pcs.`);
  renderActivity();
  renderNotifBadge();
  showToast(`Order "${item.orderId}" selesai! (${hasil} pcs)`, 'success');
  closeModal();
}

// LOGOUT
function openLogout() { showModal('logoutModal'); }

// ACTIVITY PANEL
function addActivity(type, title, sub) {
  const now = new Date();
  const h = String(now.getHours()).padStart(2,'0');
  const m = String(now.getMinutes()).padStart(2,'0');
  activities.unshift({ time:`${h}:${m}`, date:'21 April 2026', title, sub, type });
}

function renderActivity() {
  const body = document.getElementById('activityBody');
  const recent = activities.slice(0,8);
  const grouped = {};
  recent.forEach(a => {
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
          <div class="act-title">${a.title}</div>
          ${a.sub?`<div class="act-sub">${a.sub}</div>`:''}
        </div>
        <span class="act-arrow">›</span>
      </div>`;
    });
  });
  body.innerHTML = html;
}

// ALL ACTIVITY
function openAllActivity() {
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
          <div class="act-title">${a.title} <span class="act-badge ${a.type}">${a.type==='green'?'Selesai':a.type==='orange'?'Proses':a.type==='red'?'Hapus':'Info'}</span></div>
          ${a.sub?`<div class="act-sub">${a.sub}</div>`:''}
        </div>
        <span class="act-arrow">›</span>
      </div>`;
    });
  });
  document.getElementById('allActivityBody').innerHTML = html;
  showModal('allActivityModal');
}

// NOTIFIKASI
function renderNotifList() {
  const list = document.getElementById('notifList');
  list.innerHTML = notifications.map(n => `
    <div class="notif-item${n.read?'':' unread'}" onclick="readNotif(${n.id})">
      <div class="notif-dot ${n.type}"></div>
      <div>
        <div class="notif-text">${n.text}</div>
        <div class="notif-time">${n.time}</div>
      </div>
    </div>
  `).join('');
}
function renderNotifBadge() {
  const u = notifications.filter(n=>!n.read).length;
  document.getElementById('bellBadge').style.display = u>0?'block':'none';
}
function addNotif(type, text) {
  notifications.unshift({ id:Date.now(), type, text, time:'Baru saja', read:false });
  renderNotifList(); renderNotifBadge();
}
function readNotif(id) { const n=notifications.find(x=>x.id===id); if(n) n.read=true; renderNotifList(); renderNotifBadge(); }
function markAllRead() { notifications.forEach(n=>n.read=true); renderNotifList(); renderNotifBadge(); showToast('Semua notifikasi ditandai dibaca','info'); }
function toggleNotif() {
  const panel=document.getElementById('notifPanel'), overlay=document.getElementById('notifOverlay');
  const open=panel.classList.contains('open');
  panel.classList.toggle('open',!open); overlay.classList.toggle('active',!open);
  if(!open) renderNotifList();
}
function closeNotif() { document.getElementById('notifPanel').classList.remove('open'); document.getElementById('notifOverlay').classList.remove('active'); }

// TOAST
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

// INIT
renderTable();
renderActivity();
renderNotifBadge();