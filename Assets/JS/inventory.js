// ── DATA PRODUK (Tabel dibiarkan kosong di awal) ──
let allData = [];
let filteredData = [...allData];
let currentPage = 1;
const PER_PAGE = 5;
let editingId = null;

// ── LOGIKA STOK & STATUS ──
function getStatus(stok) {
  if (stok <= 0) return "Habis";
  if (stok >= 1 && stok <= 9) return "Menipis";
  return "Aman"; // >= 10
}

function badgeClass(status) {
  if (status === "Aman") return "badge badge-green";
  if (status === "Menipis") return "badge badge-orange";
  return "badge badge-red";
}

// ── FORMATTING HARGA ──
function formatRp(num) {
  return "Rp " + Number(num).toLocaleString("id-ID");
}

function parseRp(str) {
  return parseInt(str.replace(/\D/g, "")) || 0;
}

function formatHarga(input) {
  let raw = input.value.replace(/\D/g, "");
  input.value = raw ? parseInt(raw).toLocaleString("id-ID") : "";
}

// ── RENDER TABLE ──
function renderTable() {
  const tbody = document.getElementById("tableBody");
  const start = (currentPage - 1) * PER_PAGE;
  const pageData = filteredData.slice(start, start + PER_PAGE);

  if (pageData.length === 0) {
    tbody.innerHTML = `<tr><td colspan="8" style="padding: 50px; color: #999;">Tabel masih kosong. Klik "Input Produk" untuk menambahkan data.</td></tr>`;
  } else {
    tbody.innerHTML = pageData
      .map((row, index) => {
        const status = getStatus(row.stok);
        const imgEl = row.gambar
          ? `<img src="${row.gambar}" style="width:45px; height:45px; border-radius:8px; object-fit:cover;">`
          : `<div style="width:45px; height:45px; background:#f0f0f0; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#ccc; font-size:10px;">No Img</div>`;

        return `
        <tr>
          <td class="no">${String(start + index + 1).padStart(2, "0")}</td>
          <td>${imgEl}</td>
          <td class="name" style="font-weight:600">${row.nama}</td>
          <td style="color:var(--text-sub)">${row.kategori}</td>
          <td style="font-weight:600">${formatRp(row.harga)}</td>
          <td style="font-weight:700">${row.stok}</td>
          <td><span class="${badgeClass(status)}">${status}</span></td>
          <td>
            <div class="aksi-wrap">
              <button class="btn-icon" onclick="openEdit(${row.id})" title="Edit Detail">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </button>
              <button class="btn-icon" onclick="deleteData(${row.id})" title="Hapus Produk" style="color: #e53935;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
              </button>
            </div>
          </td>
        </tr>`;
      })
      .join("");
  }
  updatePagination();
}

// ── IMAGE PREVIEW ──
function previewImage(event) {
  const file = event.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = (e) => {
    const preview = document.getElementById("imgPreview");
    preview.src = e.target.result;
    preview.style.display = "block";
    document.getElementById("imgPlaceholder").style.display = "none";
  };
  reader.readAsDataURL(file);
}

// ── SAVE / UPDATE DATA ──
function saveData() {
  const nama = document.getElementById("fNama").value.trim();
  const kategori = document.getElementById("fKategori").value;
  const harga = parseRp(document.getElementById("fHarga").value);
  const stok = parseInt(document.getElementById("fStok").value) || 0;
  const gambar = document.getElementById("imgPreview").src;

  if (!nama || harga <= 0)
    return showToast("Lengkapi nama dan harga produk!", "error");

  if (editingId) {
    // Update data jika sedang dalam mode Edit
    const idx = allData.findIndex((r) => r.id === editingId);
    allData[idx] = { ...allData[idx], nama, kategori, harga, stok, gambar };
    showToast("Produk berhasil diperbarui!", "success");
  } else {
    // Tambah data baru
    allData.push({ id: Date.now(), nama, kategori, harga, stok, gambar });
    showToast("Produk baru berhasil ditambahkan!", "success");
  }

  filteredData = [...allData];
  closeModal();
  renderTable();
}

// ── EDIT DATA (Fungsi Baru) ──
function openEdit(id) {
  const item = allData.find((r) => r.id === id);
  if (!item) return;

  editingId = id;
  document.getElementById("modalTitle").textContent = "Edit Produk";

  // Isi form dengan data yang sudah ada
  document.getElementById("fNama").value = item.nama;
  document.getElementById("fKategori").value = item.kategori;
  document.getElementById("fStok").value = item.stok;
  document.getElementById("fHarga").value = item.harga.toLocaleString("id-ID");

  // Atur gambar
  if (item.gambar && item.gambar !== window.location.href) {
    document.getElementById("imgPreview").src = item.gambar;
    document.getElementById("imgPreview").style.display = "block";
    document.getElementById("imgPlaceholder").style.display = "none";
  } else {
    document.getElementById("imgPreview").src = "";
    document.getElementById("imgPreview").style.display = "none";
    document.getElementById("imgPlaceholder").style.display = "block";
  }

  showModal("inputModal");
}

// ── HAPUS DATA (Fungsi Baru) ──
function deleteData(id) {
  if (confirm("Apakah Anda yakin ingin menghapus produk ini?")) {
    allData = allData.filter((item) => item.id !== id);
    filteredData = [...allData];
    renderTable();
    showToast("Produk berhasil dihapus!", "success");
  }
}

// ── MODAL HELPERS ──
function openInputModal() {
  editingId = null;
  document.getElementById("modalTitle").textContent = "Input Produk Baru";
  document.getElementById("fNama").value = "";
  document.getElementById("fHarga").value = "";
  document.getElementById("fStok").value = "";

  document.getElementById("imgPreview").src = "";
  document.getElementById("imgPreview").style.display = "none";
  document.getElementById("imgPlaceholder").style.display = "block";

  showModal("inputModal");
}

function showModal(id) {
  document.getElementById(id).style.display = "block";
  document.getElementById("overlay").classList.add("active");
}

function closeModal() {
  document
    .querySelectorAll(".modal")
    .forEach((m) => (m.style.display = "none"));
  document.getElementById("overlay").classList.remove("active");
}

function closeOverlay(e) {
  if (e.target.id === "overlay") closeModal();
}

// ── SEARCH & PAGINATION ──
document.getElementById("searchInput").addEventListener("input", (e) => {
  const q = e.target.value.toLowerCase();
  filteredData = allData.filter((item) => item.nama.toLowerCase().includes(q));
  currentPage = 1;
  renderTable();
});

function updatePagination() {
  const info = document.getElementById("pageInfo");
  info.textContent = `Total Produk: ${filteredData.length}`;
}

// ── TOAST NOTIFICATION LENGKAP ──
function showToast(msg, type) {
  const container = document.getElementById("toastContainer");
  if (!container) return;

  const toast = document.createElement("div");
  toast.className = `toast ${type}`;
  toast.innerHTML = `
    <span class="toast-icon">
      ${type === "success" ? "✓" : "✕"}
    </span> 
    ${msg} 
    <div class="toast-bar"></div>
  `;

  container.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}

// Render tabel kosong saat halaman pertama kali dimuat
renderTable();
