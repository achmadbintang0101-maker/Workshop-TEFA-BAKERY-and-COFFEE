// inventory.js - Versi Bersih tanpa Dummy

// FORMATTING HARGA SAAT KETIK
function formatHarga(input) {
  let raw = input.value.replace(/\D/g, "");
  input.value = raw ? parseInt(raw).toLocaleString("id-ID") : "";
}

// PREVIEW GAMBAR SEBELUM UPLOAD
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

// FUNGSI MODAL
function openInputModal() {
  document.getElementById("overlay").classList.add("active");
}

function closeModal() {
  document.getElementById("overlay").classList.remove("active");
}

// Menutup modal jika klik di luar area modal
window.onclick = function (event) {
  const overlay = document.getElementById("overlay");
  if (event.target == overlay) {
    closeModal();
  }
};

// ==========================================
// FUNGSI MODAL EDIT PRODUK
// ==========================================

function bukaEditModal(id, nama, kategori, harga, stok) {
  document.getElementById('edit_id_product').value = id;
  document.getElementById('edit_nama').value = nama;
  document.getElementById('edit_kategori').value = kategori;
  
  // Format harga kembali menjadi string dengan pemisah ribuan agar rapi di form
  document.getElementById('edit_harga').value = parseInt(harga).toLocaleString("id-ID"); 
  
  document.getElementById('edit_stok').value = stok;
  
  // Tampilkan modal
  document.getElementById('overlayEdit').style.display = 'flex';
}

function tutupEditModal() {
  document.getElementById('overlayEdit').style.display = 'none';
}

// Tambahan: Agar overlayEdit juga tertutup jika di-klik di luar area modal
window.addEventListener('click', function(event) {
  const overlayEdit = document.getElementById("overlayEdit");
  if (event.target == overlayEdit) {
    tutupEditModal();
  }
});
