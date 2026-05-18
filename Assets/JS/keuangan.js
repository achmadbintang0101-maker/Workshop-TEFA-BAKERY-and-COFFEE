let allData = [];
const PER_PAGE = 6;
let currentPage = 1;
let filteredData = [...allData];
let deletingId = null;

function formatRupiah(num) {
  // Pastikan data dipaksa menjadi angka (Float) sebelum diformat
  const angka = parseFloat(num) || 0;
  return 'Rp ' + angka.toLocaleString('id-ID') + ',00';
}

function trashSVG() {
  return `<svg width="13" height="13" fill="none" stroke="#7a6a5a" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg>`;
}

// ── 1. FETCH DATA DARI DATABASE ──
async function fetchKeuangan() {
    try {
        const response = await fetch('../Controllers/FinanceController.php?action=get_all');
        const data = await response.json();
        
        // Update Kartu Ringkasan
        document.getElementById('totalSaldo').innerText = formatRupiah(data.summary.saldo);
        document.getElementById('totalPemasukan').innerText = formatRupiah(data.summary.pemasukan);
        document.getElementById('totalPenarikan').innerText = formatRupiah(data.summary.penarikan);
        
        // Update Tabel
        allData = data.records;
        applyFilters();
    } catch (error) {
        showToast("Gagal memuat data keuangan", "error");
    }
}

function renderTable() {
  const tbody = document.getElementById('tableBody');
  const start = (currentPage - 1) * PER_PAGE;
  const pageData = filteredData.slice(start, start + PER_PAGE);

  if (pageData.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" style="padding: 50px; text-align: center; color: #999;">Belum ada data transaksi keuangan.</td></tr>';
      document.getElementById('pageInfo').textContent = 'Menampilkan 0 data';
      document.getElementById('pageControls').innerHTML = '';
      return;
  }

  tbody.innerHTML = pageData.map(row => {
    const badgeStatus = row.status === 'Selesai' ? '<span class="badge badge-green">Selesai</span>' : '<span class="badge badge-orange">Menunggu</span>';
    const badgeJenis = row.jenis === 'Pemasukan' ? `<span class="badge badge-pemasukan">${row.jenis}</span>` : `<span class="badge badge-pengeluaran">${row.jenis}</span>`;
    
    // Hanya tampilkan tombol hapus jika data berasal dari input manual (tabel finances)
    const btnAksi = row.source === 'manual' 
        ? `<button class="btn-icon" onclick="openDelete(${row.id})" title="Hapus">${trashSVG()}</button>` 
        : `<span style="font-size:11px; color:#aaa">Otomatis</span>`;

    return `<tr>
      <td>${row.tanggal}</td>
      <td>${badgeJenis}</td>
      <td class="keterangan">${row.keterangan}</td>
      <td>${formatRupiah(row.nominal)}</td>
      <td>${badgeStatus}</td>
      <td><div class="aksi-wrap">${btnAksi}</div></td>
    </tr>`;
  }).join('');

  const total = filteredData.length;
  const end = Math.min(start + PER_PAGE, total);
  document.getElementById('pageInfo').textContent = `Menampilkan ${start + 1} sampai ${end} dari ${total}`;
  renderPagination();
}

function renderPagination() {
  const total = filteredData.length;
  if (total === 0) return;
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
    const matchSearch = row.keterangan.toLowerCase().includes(q) || row.jenis.toLowerCase().includes(q) || row.tanggal.includes(q);
    const matchKategori = kategori === 'all' || row.jenis === kategori;
    return matchSearch && matchKategori;
  });
  currentPage = 1;
  renderTable();
}

document.getElementById('searchInput').addEventListener('input', applyFilters);

// ── MODALS & API CALLS ──
function showModal(id) {
  document.querySelectorAll('.overlay .modal').forEach(m => m.style.display = 'none');
  document.getElementById(id).style.display = '';
  document.getElementById('overlay').classList.add('active');
}
function closeModal() { document.getElementById('overlay').classList.remove('active'); deletingId = null; }
function closeOverlay(e) { if (e.target === document.getElementById('overlay')) closeModal(); }

function openInputModal() {
  document.getElementById('fKeterangan').value = '';
  document.getElementById('fNominal').value = '';
  document.getElementById('fJenis').value = 'Penarikan';
  document.getElementById('fStatus').value = 'Selesai';
  document.getElementById('fTanggal').value = new Date().toISOString().split('T')[0];
  showModal('inputModal');
}

async function saveFinanceData() {
  const btn = document.getElementById('btnSave');
  const tanggalRaw = document.getElementById('fTanggal').value;
  let jenisRaw = document.getElementById('fJenis').value;
  const keterangan = document.getElementById('fKeterangan').value.trim();
  const nominal = parseFloat(document.getElementById('fNominal').value) || 0;
  let statusRaw = document.getElementById('fStatus').value;

  // PERBAIKAN BUG: Pastikan tipe data 100% sesuai dengan ENUM di database
  let jenis = jenisRaw.includes('Pemasukan') ? 'Pemasukan' : 'Penarikan';
  let status = statusRaw.includes('Selesai') ? 'Selesai' : 'Menunggu';

  if (!tanggalRaw || !keterangan || nominal <= 0) { 
      showToast('Lengkapi semua data dengan benar!', 'error'); 
      return; 
  }

  const [y, m, d] = tanggalRaw.split('-');
  const payload = { tanggal: `${d}-${m}-${y}`, jenis, keterangan, nominal, status };

  if (btn) {
      btn.innerText = "Menyimpan...";
      btn.disabled = true;
  }

  try {
      const res = await fetch('../Controllers/FinanceController.php?action=create', {
          method: 'POST', 
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
      });
      
      // Cek jika server PHP meledak/mengeluarkan error text bukan JSON
      if (!res.ok) {
          console.error("Server merespon dengan error:", await res.text());
          showToast("Terjadi error di database. Cek console!", "error");
          if (btn) { btn.innerText = "Simpan"; btn.disabled = false; }
          return;
      }

      const resData = await res.json();
      if (resData.status === 'success') {
          showToast(resData.message, 'success');
          closeModal();
          fetchKeuangan(); // Reload tabel
      } else { 
          showToast(resData.message, 'error'); 
      }
  } catch (err) { 
      console.error("Fetch Error: ", err);
      showToast("Gagal menghubungi server atau JSON tidak valid", "error"); 
  } finally { 
      if (btn) { btn.innerText = "Simpan"; btn.disabled = false; }
  }
}
function openDelete(id) {
  const row = allData.find(r => r.id === id && r.source === 'manual');
  if(!row) return;
  deletingId = id;
  document.getElementById('deleteItemName').textContent = row.keterangan;
  showModal('deleteModal');
}

async function confirmDelete() {
  try {
      const res = await fetch('../Controllers/FinanceController.php?action=delete', {
          method: 'POST', headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: deletingId })
      });
      const resData = await res.json();
      if (resData.status === 'success') {
          showToast("Data berhasil dihapus", 'success');
          closeModal();
          fetchKeuangan(); // Reload tabel
      } else { showToast(resData.message, 'error'); }
  } catch (err) { showToast("Terjadi kesalahan server", "error"); }
}

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

// INIT
fetchKeuangan();