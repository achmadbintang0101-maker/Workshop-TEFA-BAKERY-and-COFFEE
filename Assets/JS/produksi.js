// ════════════════════════════
// DATA
// ════════════════════════════
let allData = [
  { id:1,  no:'01', nama:'Roti Kering',     tanggal:'2026-03-16', jumlah:35,  status:'Sedang Diproses', pj:'Ibu Sari',    catatan:'Batch pagi',           aksiNum:30 },
  { id:2,  no:'02', nama:'Roti Sisir',      tanggal:'2026-04-17', jumlah:150, status:'Terjadwal',       pj:'Pak Budi',    catatan:'-',                    aksiNum:null },
  { id:3,  no:'03', nama:'Roti Coklat',     tanggal:'2026-04-17', jumlah:100, status:'Terjadwal',       pj:'Ibu Rina',    catatan:'Gunakan coklat blok',  aksiNum:null },
  { id:4,  no:'04', nama:'Roti Pisang',     tanggal:'2026-04-17', jumlah:20,  status:'Selesai',         pj:'Pak Hendra',  catatan:'Selesai tepat waktu',  aksiNum:null },
  { id:5,  no:'05', nama:'Roti Kacang',     tanggal:'2026-04-17', jumlah:10,  status:'Selesai',         pj:'Ibu Sari',    catatan:'Kualitas baik',        aksiNum:null },
  { id:6,  no:'06', nama:'Croissant',       tanggal:'2026-04-18', jumlah:60,  status:'Terjadwal',       pj:'Pak Dwiki',   catatan:'Perlu mentega ekstra', aksiNum:null },
  { id:7,  no:'07', nama:'Donat Gula',      tanggal:'2026-04-18', jumlah:80,  status:'Terjadwal',       pj:'Ibu Rina',    catatan:'-',                    aksiNum:null },
  { id:8,  no:'08', nama:'Roti Tawar',      tanggal:'2026-04-19', jumlah:200, status:'Selesai',         pj:'Pak Budi',    catatan:'Produksi besar',       aksiNum:null },
  { id:9,  no:'09', nama:'Roti Keju',       tanggal:'2026-04-20', jumlah:45,  status:'Sedang Diproses', pj:'Ibu Sari',    catatan:'Batch siang',          aksiNum:20 },
  { id:10, no:'10', nama:'Muffin Coklat',   tanggal:'2026-04-20', jumlah:30,  status:'Terjadwal',       pj:'Pak Hendra',  catatan:'Order khusus',         aksiNum:null },
];

let activities = [
  { time:'09:00', date:'20 April 2026', title:'Pesanan Baru', sub:'Diproses 3 Produk', type:'orange' },
  { time:'09:30', date:'20 April 2026', title:'Produksi Dimulai', sub:'Roti Keju batch siang', type:'orange' },
  { time:'10:00', date:'20 April 2026', title:'Produksi Selesai', sub:'Roti Tawar — 200 pcs', type:'green' },
  { time:'10:15', date:'20 April 2026', title:'Jadwal Baru Ditambah', sub:'Muffin Coklat 30 pcs', type:'gray' },
  { time:'11:00', date:'20 April 2026', title:'Pesanan Baru', sub:'Diproses 10 Produk', type:'orange' },
  { time:'11:05', date:'20 April 2026', title:'Pesanan Diterima', sub:'Konfirmasi customer', type:'green' },
  { time:'12:30', date:'19 April 2026', title:'Produksi Selesai', sub:'Roti Tawar 200 pcs', type:'green' },
  { time:'12:40', date:'19 April 2026', title:'Laporan Harian', sub:'Dibuat otomatis', type:'gray' },
  { time:'08:00', date:'18 April 2026', title:'Jadwal Baru', sub:'Croissant & Donat', type:'gray' },
  { time:'15:00', date:'17 April 2026', title:'Produksi Selesai', sub:'Roti Pisang & Kacang', type:'green' },
];

let notifications = [
  { id:1, type:'orange', text:'<strong>Roti Kering</strong> sedang diproses. Progress 30/35 pcs.', time:'5 menit lalu', read:false },
  { id:2, type:'orange', text:'<strong>Roti Keju</strong> sedang diproses. Progress 20/45 pcs.', time:'20 menit lalu', read:false },
  { id:3, type:'gray',   text:'Jadwal produksi baru: <strong>Muffin Coklat</strong> hari ini.', time:'1 jam lalu', read:false },
  { id:4, type:'green',  text:'<strong>Roti Tawar</strong> berhasil selesai. 200 pcs.', time:'2 jam lalu', read:true },
  { id:5, type:'green',  text:'Laporan produksi harian telah dibuat.', time:'Kemarin', read:true },
];

const PER_PAGE = 5;
let currentPage = 1;
let filteredData = [...allData];
let editingId = null;
let deletingId = null;
let prosesId = null;
let nextId = 11;

// ════════════════════════════
// RENDER TABLE
// ════════════════════════════
function badgeHtml(s) {
  const map = { 'Selesai':'badge-green', 'Sedang Diproses':'badge-orange', 'Terjadwal':'badge-gray' };
  const label = s === 'Sedang Diproses' ? 'Sedang<br>Diproses' : s;
  return `<span class="badge ${map[s]||'badge-gray'}">${label}</span>`;
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
      <td class="bold">${row.nama}</td>
      <td>${fmtDate(row.tanggal)}</td>
      <td>${row.jumlah}</td>
      <td>${badgeHtml(row.status)}</td>
      <td>
        <div class="aksi-wrap">
          ${row.status === 'Sedang Diproses' && row.aksiNum !== null ? `
            <span class="aksi-num">${row.aksiNum}</span>
            <button class="btn-icon check" onclick="openProses(${row.id})" title="Selesaikan">
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
  filteredData = allData.filter(r => r.nama.toLowerCase().includes(q) || r.status.toLowerCase().includes(q));
  currentPage = 1;
  renderTable();
});

// ════════════════════════════
// MODALS
// ════════════════════════════
function showModal(id) {
  document.querySelectorAll('.modal').forEach(m => m.style.display='none');
  document.getElementById(id).style.display='';
  document.getElementById('overlay').classList.add('active');
}
function closeModal() { document.getElementById('overlay').classList.remove('active'); editingId=null; deletingId=null; prosesId=null; }
function closeOverlay(e) { if(e.target===document.getElementById('overlay')) closeModal(); }

// INPUT / EDIT
function openInputModal() {
  editingId = null;
  document.getElementById('modalTitle').textContent = 'Tambah Jadwal Produksi';
  document.getElementById('btnSave').textContent = 'Simpan';
  ['fNama','fPJ','fCatatan'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('fJumlah').value = '';
  document.getElementById('fStatus').value = 'Terjadwal';
  document.getElementById('fTanggal').value = new Date().toISOString().split('T')[0];
  showModal('inputModal');
}

function openEdit(id) {
  const item = allData.find(r=>r.id===id);
  editingId = id;
  document.getElementById('modalTitle').textContent = 'Edit Jadwal Produksi';
  document.getElementById('btnSave').textContent = 'Perbarui';
  document.getElementById('fNama').value = item.nama;
  document.getElementById('fTanggal').value = item.tanggal;
  document.getElementById('fJumlah').value = item.jumlah;
  document.getElementById('fStatus').value = item.status;
  document.getElementById('fPJ').value = item.pj;
  document.getElementById('fCatatan').value = item.catatan;
  showModal('inputModal');
}

function saveData() {
  const nama = document.getElementById('fNama').value.trim();
  const tanggal = document.getElementById('fTanggal').value;
  const jumlah = parseInt(document.getElementById('fJumlah').value)||0;
  const status = document.getElementById('fStatus').value;
  const pj = document.getElementById('fPJ').value.trim();
  const catatan = document.getElementById('fCatatan').value.trim();
  if (!nama||!tanggal||!jumlah) { showToast('Lengkapi semua field!','error'); return; }
  if (editingId) {
    const idx = allData.findIndex(r=>r.id===editingId);
    allData[idx] = {...allData[idx], nama, tanggal, jumlah, status, pj, catatan};
    showToast(`Jadwal "${nama}" berhasil diperbarui`, 'success');
    addActivity('green', `Jadwal diperbarui: ${nama}`, status);
  } else {
    const noStr = String(nextId).padStart(2,'0');
    allData.push({ id:nextId, no:noStr, nama, tanggal, jumlah, status, pj, catatan, aksiNum:null });
    nextId++;
    showToast(`Jadwal "${nama}" berhasil ditambahkan`, 'success');
    addActivity('gray', `Jadwal baru ditambah: ${nama}`, status);
    addNotif('gray', `Jadwal produksi baru: <strong>${nama}</strong> ditambahkan.`);
  }
  filteredData = [...allData];
  renderTable();
  renderActivity();
  closeModal();
}

// DETAIL
function openDetail(id) {
  const item = allData.find(r=>r.id===id);
  document.getElementById('detailTitle').textContent = item.nama;
  const pct = item.status==='Selesai'?100:item.status==='Sedang Diproses'?Math.round((item.aksiNum||0)/item.jumlah*100):0;
  const fillClass = item.status==='Selesai'?'green':item.status==='Sedang Diproses'?'orange':'gray';
  document.getElementById('detailContent').innerHTML = `
    <div class="detail-grid">
      <div class="detail-item"><div class="detail-label">Tanggal</div><div class="detail-value">${fmtDate(item.tanggal)}</div></div>
      <div class="detail-item"><div class="detail-label">Jumlah Target</div><div class="detail-value">${item.jumlah} pcs</div></div>
      <div class="detail-item"><div class="detail-label">Status</div><div class="detail-value">${badgeHtml(item.status)}</div></div>
      <div class="detail-item"><div class="detail-label">Penanggung Jawab</div><div class="detail-value">${item.pj||'-'}</div></div>
      ${item.catatan && item.catatan!=='-' ? `<div class="detail-item detail-full"><div class="detail-label">Catatan</div><div class="detail-value" style="font-weight:400;font-size:13px">${item.catatan}</div></div>` : ''}
    </div>
    <div class="progress-wrap">
      <div class="progress-label"><span>Progress Produksi</span><span>${pct}%</span></div>
      <div class="progress-bar"><div class="progress-fill ${fillClass}" style="width:${pct}%"></div></div>
    </div>
    <div class="stepper" style="margin-top:18px">
      <div class="step">
        <div class="step-circle done">✓</div>
        <div class="step-label done">Terjadwal</div>
      </div>
      <div class="step-line ${item.status==='Sedang Diproses'||item.status==='Selesai'?'done':''}"></div>
      <div class="step">
        <div class="step-circle ${item.status==='Sedang Diproses'?'active':item.status==='Selesai'?'done':''}">${item.status==='Selesai'?'✓':item.status==='Sedang Diproses'?'●':'2'}</div>
        <div class="step-label ${item.status==='Sedang Diproses'?'active':item.status==='Selesai'?'done':''}">Diproses</div>
      </div>
      <div class="step-line ${item.status==='Selesai'?'done':''}"></div>
      <div class="step">
        <div class="step-circle ${item.status==='Selesai'?'done':''}">${item.status==='Selesai'?'✓':'3'}</div>
        <div class="step-label ${item.status==='Selesai'?'done':''}">Selesai</div>
      </div>
    </div>
  `;
  // footer buttons based on status
  let footer = `<button class="btn-cancel" onclick="closeModal()">Tutup</button>`;
  if (item.status==='Terjadwal') footer += `<button class="btn-start" onclick="startProses(${item.id})">▶ Mulai Proses</button>`;
  if (item.status==='Sedang Diproses') footer += `<button class="btn-selesai" onclick="openProses(${item.id}); closeModal()">✓ Selesaikan</button>`;
  document.getElementById('detailFooter').innerHTML = footer;
  showModal('detailModal');
}

function startProses(id) {
  const idx = allData.findIndex(r=>r.id===id);
  allData[idx].status = 'Sedang Diproses';
  allData[idx].aksiNum = 0;
  filteredData = [...allData];
  renderTable();
  addActivity('orange', `Produksi dimulai: ${allData[idx].nama}`, 'Sedang Diproses');
  addNotif('orange', `<strong>${allData[idx].nama}</strong> mulai diproses.`);
  renderActivity();
  showToast(`Produksi "${allData[idx].nama}" dimulai!`, 'info');
  closeModal();
}

// DELETE
function openDelete(id) {
  const item = allData.find(r=>r.id===id);
  deletingId = id;
  document.getElementById('deleteItemName').textContent = item.nama;
  showModal('deleteModal');
}
function confirmDelete() {
  const item = allData.find(r=>r.id===deletingId);
  allData = allData.filter(r=>r.id!==deletingId);
  filteredData = filteredData.filter(r=>r.id!==deletingId);
  if (currentPage > Math.ceil(filteredData.length/PER_PAGE) && currentPage>1) currentPage--;
  renderTable();
  showToast(`"${item.nama}" dihapus`, 'error');
  closeModal();
}

// PROSES (centang)
function openProses(id) {
  const item = allData.find(r=>r.id===id);
  prosesId = id;
  document.getElementById('prosesItemName').textContent = item.nama;
  document.getElementById('prosesTarget').textContent = item.jumlah;
  document.getElementById('prosesQty').value = item.aksiNum || item.jumlah;
  document.getElementById('prosesNote').value = '';
  showModal('prosesModal');
}
function changeProsesQty(delta) {
  const inp = document.getElementById('prosesQty');
  inp.value = Math.max(0, parseInt(inp.value||0)+delta);
}
function confirmProses() {
  const item = allData.find(r=>r.id===prosesId);
  const hasil = parseInt(document.getElementById('prosesQty').value)||0;
  const note = document.getElementById('prosesNote').value.trim();
  const idx = allData.findIndex(r=>r.id===prosesId);
  allData[idx].status = 'Selesai';
  allData[idx].aksiNum = null;
  allData[idx].catatan = note || allData[idx].catatan;
  filteredData = [...allData];
  renderTable();
  addActivity('green', `Selesai: ${item.nama} — ${hasil} pcs`, 'Selesai');
  addNotif('green', `Produksi <strong>${item.nama}</strong> selesai. ${hasil} pcs.`);
  renderActivity();
  renderNotifBadge();
  showToast(`"${item.nama}" selesai diproduksi! (${hasil} pcs)`, 'success');
  closeModal();
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
          <div class="act-title">${a.title} <span class="act-badge ${a.type}">${a.type==='green'?'Selesai':a.type==='orange'?'Proses':'Info'}</span></div>
          ${a.sub?`<div class="act-sub">${a.sub}</div>`:''}
        </div>
        <span class="act-arrow">›</span>
      </div>`;
    });
  });
  document.getElementById('allActivityBody').innerHTML = html;
  showModal('allActivityModal');
}

// LOGOUT
function openLogout() { showModal('logoutModal'); }

// ════════════════════════════
// ACTIVITY PANEL
// ════════════════════════════
function addActivity(type, title, sub) {
  const now = new Date();
  const h = String(now.getHours()).padStart(2,'0');
  const m = String(now.getMinutes()).padStart(2,'0');
  activities.unshift({ time:`${h}:${m}`, date:'20 April 2026', title, sub, type });
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

// ════════════════════════════
// NOTIFIKASI
// ════════════════════════════
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

// ════════════════════════════
// TOAST
// ════════════════════════════
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

// ════════════════════════════
// INIT
// ════════════════════════════
renderTable();
renderActivity();
renderNotifBadge();