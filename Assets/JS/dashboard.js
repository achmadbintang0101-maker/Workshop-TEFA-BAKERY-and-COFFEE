// ── MODAL HELPERS ──
function showModal(id) {
  document.querySelectorAll('.modal').forEach(m => m.style.display='none');
  document.getElementById(id).style.display = '';
  document.getElementById('overlay').classList.add('active');
}
function closeModal() { document.getElementById('overlay').classList.remove('active'); }
function closeOverlay(e) { if (e.target === document.getElementById('overlay')) closeModal(); }

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
    <div style="margin-top:14px;padding:14px;background:#f9f5f0;border-radius:10px;font-size:13.5px;color:var(--text-sub);line-height:1.7">
      Terkait pesanan: <strong>${deskripsi}</strong>
    </div>
  `;
  showModal('activityModal');
}

// ── FETCH DATA DARI DATABASE ──
async function fetchDashboard() {
    try {
        const response = await fetch('../Controllers/DashboardController.php?action=get_data');
        const data = await response.json();

        if (data.status === 'success') {
            // Update Stat Cards Atas
            document.getElementById('valPendapatan').innerText = 'Rp ' + parseFloat(data.stats.pendapatan).toLocaleString('id-ID');
            document.getElementById('valPesanan').innerText = data.stats.pesanan_hari_ini;
            document.getElementById('valStok').innerText = data.stats.total_stok;

            // Update Tabel Aktivitas
            const tbody = document.getElementById('activityBody');
            if (data.activities.length > 0) {
                tbody.innerHTML = data.activities.map(a => `
                    <tr onclick="openActivityDetail('${a.time}', '${a.title}', '${a.desc}', '${a.type}')">
                        <td style="width: 80px;">${a.time}</td>
                        <td style="font-weight: 600; color: ${a.type === 'green' ? '#3a9c4e' : '#e8760a'}">${a.title}</td>
                        <td class="act-detail-desc">${a.desc}</td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align:center; padding:30px; color:#999;">Belum ada aktivitas transaksi hari ini.</td></tr>';
            }
        }
    } catch (error) {
        console.error("Gagal memuat data Dashboard", error);
        showToast("Terjadi kesalahan jaringan", "error");
    }
}
// ── SET TANGGAL REAL-TIME ──
function updateTanggalPesanan() {
    const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const now = new Date();
    
    // Format: 12 Mei 2026
    const tanggalText = `${now.getDate()} ${bulan[now.getMonth()]} ${now.getFullYear()}`;
    
    // Masukkan ke dalam HTML
    const elTanggal = document.getElementById('tanggalPesanan');
    if (elTanggal) {
        elTanggal.innerText = tanggalText;
    }
}

// Jalankan fungsi saat file dimuat
updateTanggalPesanan();

// ── SET BULAN REAL-TIME UNTUK PENDAPATAN ──
function updateBulanPendapatan() {
    const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const now = new Date();
    
    // Format: Bulan Mei 2026
    const bulanText = `Bulan ${bulan[now.getMonth()]} ${now.getFullYear()}`;
    
    // Masukkan ke dalam HTML
    const elBulan = document.getElementById('bulanPendapatan');
    if (elBulan) {
        elBulan.innerText = bulanText;
    }
}

// Jalankan fungsi saat file dimuat
updateBulanPendapatan();

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

// Jalankan ketika halaman dimuat
fetchDashboard();