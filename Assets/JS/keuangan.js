  let allData = [
    { id:1,  tanggal:'17-03-2026', jenis:'Pemasukan',   keterangan:'Penjualan roti hari ini',  nominal:200000,  status:'Selesai'  },
    { id:2,  tanggal:'17-03-2026', jenis:'Pemasukan',   keterangan:'Order dari mahasiswa',     nominal:150000,  status:'Menunggu' },
    { id:3,  tanggal:'17-03-2026', jenis:'Pengeluaran', keterangan:'Membeli bahan baku',       nominal:500000,  status:'Menunggu' },
    { id:4,  tanggal:'17-03-2026', jenis:'Pemasukan',   keterangan:'Pesanan dosen TI',         nominal:250000,  status:'Selesai'  },
    { id:5,  tanggal:'17-03-2026', jenis:'Pemasukan',   keterangan:'Pesanan roti ulang tahun', nominal:100000,  status:'Selesai'  },
    { id:6,  tanggal:'16-03-2026', jenis:'Pengeluaran', keterangan:'Beli tepung & gula',       nominal:320000,  status:'Selesai'  },
    { id:7,  tanggal:'16-03-2026', jenis:'Pemasukan',   keterangan:'Penjualan kopi',           nominal:180000,  status:'Selesai'  },
    { id:8,  tanggal:'15-03-2026', jenis:'Pengeluaran', keterangan:'Bayar listrik',            nominal:250000,  status:'Selesai'  },
    { id:9,  tanggal:'15-03-2026', jenis:'Pemasukan',   keterangan:'Catering acara kampus',    nominal:750000,  status:'Menunggu' },
    { id:10, tanggal:'14-03-2026', jenis:'Pengeluaran', keterangan:'Beli kemasan',             nominal:85000,   status:'Selesai'  },
  ];

  let notifications = [
    { id:1, type:'green',  text:'<strong>Catering acara kampus</strong> Rp750.000 menunggu konfirmasi.', time:'10 menit lalu', read:false },
    { id:2, type:'orange', text:'<strong>Membeli bahan baku</strong> Rp500.000 belum selesai.', time:'1 jam lalu', read:false },
    { id:3, type:'green',  text:'Transaksi pemasukan baru ditambahkan.', time:'2 jam lalu', read:true },
  ];

  const PER_PAGE = 6;
  let currentPage = 1;
  let filteredData = [...allData];
  let editingId = null;
  let deletingId = null;
  let nextId = 11;

  function formatRupiah(num) {
    return 'Rp ' + num.toLocaleString('id-ID') + ',00';
  }

  function editSVG() {
    return `<svg width="13" height="13" fill="none" stroke="#7a6a5a" stroke-width="2" viewBox="0 0 24 24">
      <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
      <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
    </svg>`;
  }
  function trashSVG() {
    return `<svg width="13" height="13" fill="none" stroke="#7a6a5a" stroke-width="2" viewBox="0 0 24 24">
      <polyline points="3 6 5 6 21 6"/>
      <path d="M19 6l-1 14H6L5 6"/>
      <path d="M10 11v6M14 11v6M9 6V4h6v2"/>
    </svg>`;
  }

  function renderTable() {
    const tbody = document.getElementById('tableBody');
    const start = (currentPage - 1) * PER_PAGE;
    const pageData = filteredData.slice(start, start + PER_PAGE);

    tbody.innerHTML = pageData.map(row => {
      const badgeStatus = row.status === 'Selesai'
        ? '<span class="badge badge-green">Selesai</span>'
        : '<span class="badge badge-orange">Menunggu</span>';
      const badgeJenis = row.jenis === 'Pemasukan'
        ? `<span class="badge badge-pemasukan">${row.jenis}</span>`
        : `<span class="badge badge-pengeluaran">${row.jenis}</span>`;

      return `<tr>
        <td>${row.tanggal}</td>
        <td>${badgeJenis}</td>
        <td class="keterangan">${row.keterangan}</td>
        <td>${formatRupiah(row.nominal)}</td>
        <td>${badgeStatus}</td>
        <td>
          <div class="aksi-wrap">
            <button class="btn-icon" onclick="openEdit(${row.id})" title="Edit">${editSVG()}</button>
            <button class="btn-icon" onclick="openDelete(${row.id})" title="Hapus">${trashSVG()}</button>
          </div>
        </td>
      </tr>`;
    }).join('');

    const total = filteredData.length;
    const end = Math.min(start + PER_PAGE, total);
    document.getElementById('pageInfo').textContent =
      `Menampilkan ${total === 0 ? 0 : start + 1} sampai ${end} dari ${total}`;

    renderPagination();
  }

  function renderPagination() {
    const total = filteredData.length;
    const totalPages = Math.ceil(total / PER_PAGE);
    const controls = document.getElementById('pageControls');
    let html = `<button class="page-nav" onclick="prevPage()">Sebelumnya</button>`;
    for (let i = 1; i <= totalPages; i++) {
      html += `<button class="page-btn${i === currentPage ? ' active' : ''}" onclick="setPage(${i})">${i}</button>`;
    }
    html += `<button class="page-nav" onclick="nextPage()">Berikutnya</button>`;
    controls.innerHTML = html;
  }

  function setPage(p) {
    const totalPages = Math.ceil(filteredData.length / PER_PAGE);
    if (p < 1 || p > totalPages) return;
    currentPage = p;
    renderTable();
  }
  function prevPage() { setPage(currentPage - 1); }
  function nextPage() { setPage(currentPage + 1); }

  function applyFilters() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const kategori = document.getElementById('kategoriFilter').value;
    document.getElementById('kategoriLabel').textContent = kategori === 'all' ? 'Kategori' : kategori;

    filteredData = allData.filter(row => {
      const matchSearch = row.keterangan.toLowerCase().includes(q) ||
                          row.jenis.toLowerCase().includes(q) ||
                          row.tanggal.includes(q);
      const matchKategori = kategori === 'all' || row.jenis === kategori;
      return matchSearch && matchKategori;
    });
    currentPage = 1;
    renderTable();
  }

  document.getElementById('searchInput').addEventListener('input', applyFilters);

  // ── MODAL HELPERS ──
  function showModal(id) {
    document.querySelectorAll('.overlay .modal').forEach(m => m.style.display = 'none');
    document.getElementById(id).style.display = '';
    document.getElementById('overlay').classList.add('active');
  }
  function closeModal() {
    document.getElementById('overlay').classList.remove('active');
    editingId = null;
  }
  function closeOverlay(e) {
    if (e.target === document.getElementById('overlay')) closeModal();
  }

  // ── INPUT MODAL ──
  function openInputModal() {
    editingId = null;
    document.getElementById('modalTitle').textContent = 'Tambah Transaksi';
    document.getElementById('btnSave').textContent = 'Simpan';
    document.getElementById('fKeterangan').value = '';
    document.getElementById('fNominal').value = '';
    document.getElementById('fJenis').value = 'Pemasukan';
    document.getElementById('fStatus').value = 'Menunggu';
    document.getElementById('fTanggal').value = new Date().toISOString().split('T')[0];
    showModal('inputModal');
  }

  // ── EDIT ──
  function openEdit(id) {
    const row = allData.find(r => r.id === id);
    editingId = id;
    document.getElementById('modalTitle').textContent = 'Edit Transaksi';
    document.getElementById('btnSave').textContent = 'Perbarui';
    const parts = row.tanggal.split('-');
    document.getElementById('fTanggal').value = `${parts[2]}-${parts[1]}-${parts[0]}`;
    document.getElementById('fJenis').value = row.jenis;
    document.getElementById('fKeterangan').value = row.keterangan;
    document.getElementById('fNominal').value = row.nominal;
    document.getElementById('fStatus').value = row.status;
    showModal('inputModal');
  }

  function saveData() {
    const tanggalRaw = document.getElementById('fTanggal').value;
    const jenis = document.getElementById('fJenis').value;
    const keterangan = document.getElementById('fKeterangan').value.trim();
    const nominal = parseInt(document.getElementById('fNominal').value) || 0;
    const status = document.getElementById('fStatus').value;

    if (!tanggalRaw || !keterangan || !nominal) { showToast('Lengkapi semua field!', 'error'); return; }

    const [y, m, d] = tanggalRaw.split('-');
    const tanggal = `${d}-${m}-${y}`;

    if (editingId) {
      const idx = allData.findIndex(r => r.id === editingId);
      allData[idx] = { ...allData[idx], tanggal, jenis, keterangan, nominal, status };
      showToast(`Transaksi "${keterangan}" berhasil diperbarui`, 'success');
    } else {
      allData.unshift({ id: nextId++, tanggal, jenis, keterangan, nominal, status });
      showToast(`Transaksi "${keterangan}" berhasil ditambahkan`, 'success');
      addNotif('green', `Transaksi baru: <strong>${keterangan}</strong> (${jenis})`);
    }

    filteredData = [...allData];
    renderTable();
    closeModal();
  }

  // ── DELETE ──
  function openDelete(id) {
    const row = allData.find(r => r.id === id);
    deletingId = id;
    document.getElementById('deleteItemName').textContent = row.keterangan;
    showModal('deleteModal');
  }
  function confirmDelete() {
    const row = allData.find(r => r.id === deletingId);
    allData = allData.filter(r => r.id !== deletingId);
    filteredData = filteredData.filter(r => r.id !== deletingId);
    if (currentPage > Math.ceil(filteredData.length / PER_PAGE) && currentPage > 1) currentPage--;
    renderTable();
    showToast(`"${row.keterangan}" dihapus`, 'error');
    closeModal();
  }

  // ── LOGOUT ──
  function openLogout() { showModal('logoutModal'); }

  // ── NOTIFIKASI ──
  function renderNotifList() {
    const list = document.getElementById('notifList');
    list.innerHTML = notifications.map(n => `
      <div class="notif-item${n.read ? '' : ' unread'}" onclick="readNotif(${n.id})">
        <div class="notif-dot ${n.type}"></div>
        <div>
          <div class="notif-text">${n.text}</div>
          <div class="notif-time">${n.time}</div>
        </div>
      </div>
    `).join('');
  }
  function renderNotifBadge() {
    const u = notifications.filter(n => !n.read).length;
    document.getElementById('bellBadge').style.display = u > 0 ? 'block' : 'none';
  }
  function addNotif(type, text) {
    notifications.unshift({ id: Date.now(), type, text, time: 'Baru saja', read: false });
    renderNotifList(); renderNotifBadge();
  }
  function readNotif(id) { const n = notifications.find(x => x.id === id); if (n) n.read = true; renderNotifList(); renderNotifBadge(); }
  function markAllRead() { notifications.forEach(n => n.read = true); renderNotifList(); renderNotifBadge(); showToast('Semua notifikasi ditandai dibaca', 'info'); }
  function toggleNotif() {
    const panel = document.getElementById('notifPanel'), ov = document.getElementById('notifOverlay');
    const open = panel.classList.contains('open');
    panel.classList.toggle('open', !open); ov.classList.toggle('active', !open);
    if (!open) renderNotifList();
  }
  function closeNotif() { document.getElementById('notifPanel').classList.remove('open'); document.getElementById('notifOverlay').classList.remove('active'); }

  // ── TOAST ──
  function showToast(msg, type = 'info') {
    const icons = {
      success: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>',
      error:   '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
      info:    '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
    };
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.innerHTML = `<span class="toast-icon">${icons[type] || icons.info}</span>${msg}<div class="toast-bar"></div>`;
    document.getElementById('toastContainer').appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; el.style.transform = 'translateX(20px)'; el.style.transition = 'all 0.3s'; setTimeout(() => el.remove(), 300); }, 3000);
  }

  // ── INIT ──
  renderTable();
  renderNotifBadge();