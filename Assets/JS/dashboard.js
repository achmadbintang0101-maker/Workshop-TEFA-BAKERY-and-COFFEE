// ── CHART ──
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['Januari 2026','Februari 2026','Maret 2026','April 2026','Mei 2026','Juni 2026','Juli 2026'],
    datasets: [
      { label:'Kopi', data:[350,270,120,200,210,390,200], backgroundColor:'#5c3317', borderRadius:3, barPercentage:0.45, categoryPercentage:0.75 },
      { label:'Roti', data:[270,250,240,130,160,270,110], backgroundColor:'#c49a6c', borderRadius:3, barPercentage:0.45, categoryPercentage:0.75 }
    ]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: { backgroundColor:'#fff', titleColor:'#2c1a0e', bodyColor:'#8a7060', borderColor:'#e5ddd5', borderWidth:1 }
    }
  }
});

// ── NOTIF DATA ──
let notifications = [
  { id:1, type:'orange', text:'<strong>3 Pesanan baru</strong> menunggu konfirmasi produksi.', time:'10 menit lalu', read:false },
  { id:2, type:'green',  text:'Produksi <strong>Roti Kering</strong> selesai tepat waktu.', time:'1 jam lalu', read:false },
  { id:3, type:'red',    text:'Stok <strong>Minyak</strong> habis — segera restock.', time:'2 jam lalu', read:false },
  { id:4, type:'orange', text:'Target produksi hari ini baru tercapai <strong>60%</strong>.', time:'3 jam lalu', read:true },
  { id:5, type:'gray',   text:'Laporan harian otomatis telah dibuat.', time:'Kemarin', read:true },
];

// ── MODAL HELPERS ──
function showModal(id) {
  document.querySelectorAll('.modal').forEach(m => m.style.display='none');
  document.getElementById(id).style.display = '';
  document.getElementById('overlay').classList.add('active');
}
function closeModal() { document.getElementById('overlay').classList.remove('active'); }
function closeOverlay(e) { if (e.target === document.getElementById('overlay')) closeModal(); }

// ── STAT CARDS ──
const statData = {
  pesanan: {
    title: 'Detail Total Pesanan',
    items: [
      { label:'Total Pesanan', value:'25 Pesanan' },
      { label:'Selesai', value:'18 Pesanan' },
      { label:'Diproses', value:'5 Pesanan' },
      { label:'Menunggu', value:'2 Pesanan' },
    ],
    prog: { label:'Penyelesaian', pct: 72 }
  },
  produksi: {
    title: 'Detail Produksi Hari Ini',
    items: [
      { label:'Tanggal', value:'12 Maret 2026' },
      { label:'Total Diproduksi', value:'50 Produk' },
      { label:'Selesai', value:'35 Produk' },
      { label:'Sedang Diproses', value:'15 Produk' },
    ],
    prog: { label:'Progress Produksi', pct: 70 }
  },
  stok: {
    title: 'Detail Stok Tersedia',
    items: [
      { label:'Total Item', value:'150 Item' },
      { label:'Stok Aman', value:'120 Item' },
      { label:'Menipis', value:'20 Item' },
      { label:'Habis', value:'10 Item' },
    ],
    prog: { label:'Ketersediaan Stok', pct: 80 }
  }
};

function openStatDetail(type) {
  const d = statData[type];
  document.getElementById('statModalTitle').textContent = d.title;
  document.getElementById('statModalContent').innerHTML = `
    <div class="detail-grid">
      ${d.items.map(i => `<div class="detail-item"><div class="detail-label">${i.label}</div><div class="detail-value">${i.value}</div></div>`).join('')}
    </div>
    <div class="prog-wrap">
      <div class="prog-labels"><span>${d.prog.label}</span><span>${d.prog.pct}%</span></div>
      <div class="prog-bar"><div class="prog-fill" style="width:${d.prog.pct}%"></div></div>
    </div>
  `;
  showModal('statModal');
}

// ── ADMIN DETAIL ──
function openAdminDetail() { showModal('adminModal'); }

// ── ACTIVITY DETAIL ──
function openActivityDetail(waktu, judul, deskripsi, dotColor) {
  document.getElementById('actModalTitle').textContent = judul;
  document.getElementById('actModalContent').innerHTML = `
    <div class="act-detail-row">
      <div class="act-dot ${dotColor}"></div>
      <div class="act-detail-time">${waktu}</div>
      <div class="act-detail-desc">${judul}</div>
    </div>
    <div style="margin-top:14px;padding:14px;background:#f9f5f0;border-radius:10px;font-size:13.5px;color:var(--text-sub);line-height:1.7">${deskripsi}</div>
  `;
  showModal('activityModal');
}

// ── LOGOUT ──
function openLogout() { showModal('logoutModal'); }

// ── NOTIF ──
function renderNotifList() {
  document.getElementById('notifList').innerHTML = notifications.map(n => `
    <div class="notif-item${n.read?'':' unread'}" onclick="readNotif(${n.id})">
      <div class="n-dot ${n.type}"></div>
      <div><div class="n-text">${n.text}</div><div class="n-time">${n.time}</div></div>
    </div>
  `).join('');
}
function renderNotifBadge() {
  const u = notifications.filter(n => !n.read).length;
  document.getElementById('bellBadge').style.display = u > 0 ? 'block' : 'none';
}
function readNotif(id) {
  const n = notifications.find(x => x.id === id);
  if (n) n.read = true;
  renderNotifList(); renderNotifBadge();
}
function markAllRead() {
  notifications.forEach(n => n.read = true);
  renderNotifList(); renderNotifBadge();
  showToast('Semua notifikasi ditandai dibaca', 'info');
}
function toggleNotif() {
  const p = document.getElementById('notifPanel');
  const o = document.getElementById('notifOverlay');
  const open = p.classList.contains('open');
  p.classList.toggle('open', !open);
  o.classList.toggle('active', !open);
  if (!open) renderNotifList();
}
function closeNotif() {
  document.getElementById('notifPanel').classList.remove('open');
  document.getElementById('notifOverlay').classList.remove('active');
}

// ── TOAST ──
function showToast(msg, type='info') {
  const icons = {
    success: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>',
    error:   '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
    info:    '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
  };
  const el = document.createElement('div');
  el.className = `toast ${type}`;
  el.innerHTML = `<span class="t-icon">${icons[type]||icons.info}</span>${msg}<div class="toast-bar"></div>`;
  document.getElementById('toastContainer').appendChild(el);
  setTimeout(() => { el.style.opacity='0'; el.style.transform='translateX(20px)'; el.style.transition='all 0.3s'; setTimeout(()=>el.remove(),300); }, 3000);
}

// ── INIT ──
renderNotifBadge();