// ======= REALTIME DATETIME =======
function updateWaktu() {
    const now = new Date();
    const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    const elTanggal = document.getElementById('tanggal');
    const elJam = document.getElementById('jam');

    if (elTanggal) {
        // Format: Kamis, 12 Maret 2026 (sesuai figma)
        elTanggal.textContent = `${hari[now.getDay()]}, ${now.getDate()} ${bulan[now.getMonth()]} ${now.getFullYear()}`;
    }
    
    if (elJam) {
        const jam = String(now.getHours()).padStart(2, '0');
        const menit = String(now.getMinutes()).padStart(2, '0');
        const detik = String(now.getSeconds()).padStart(2, '0');
        elJam.textContent = `${jam}:${menit}:${detik}`;
    }
}

// Jalankan setiap detik
setInterval(updateWaktu, 1000);
updateWaktu();

// ======= FILTER CATEGORY =======
function filterCategory(category, element) {
    // 1. Update status tombol aktif (gunakan selector yang fleksibel)
    const buttons = document.querySelectorAll('.btn-category, .btn-cat');
    buttons.forEach(btn => btn.classList.remove('active'));
    element.classList.add('active');

    // 2. Filter Produk berdasarkan data-category
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        const productCategory = card.getAttribute('data-category');
        if (category === 'all' || productCategory === category) {
            card.style.display = "block";
        } else {
            card.style.display = "none";
        }
    });
}

// ======= CART LOGIC =======
let cart = [];

// Tambahkan parameter ke-4 yaitu 'stock'
function addToCart(id, name, price, stock) {
    // 1. Cek apakah stok dari database memang sudah 0
    if (stock <= 0) {
        alert(`Maaf, stok ${name} sedang kosong/habis!`);
        return;
    }

    let found = cart.find(item => item.id === id);
    if (found) {
        // 2. Cek apakah item di keranjang sudah menyentuh batas stok
        if (found.qty < found.stock) {
            found.qty += 1;
        } else {
            alert(`Stok tidak cukup! Sisa stok ${name} hanya ${found.stock} item.`);
        }
    } else {
        // 3. Simpan data 'stock' ke dalam keranjang untuk dicek nanti
        cart.push({ id, name, price: parseFloat(price), qty: 1, stock: parseInt(stock) });
    }
    renderCart();
}

function increaseQty(id) {
    let item = cart.find(i => i.id === id);
    if (item) {
        // 4. Batasi tombol (+) di keranjang agar tidak tembus limit
        if (item.qty < item.stock) {
            item.qty += 1;
        } else {
            alert(`Stok tidak cukup! Sisa stok ${item.name} hanya ${item.stock} item.`);
        }
    }
    renderCart();
}

function decreaseQty(id) {
    let item = cart.find(i => i.id === id);
    if (item) {
        item.qty -= 1;
        if (item.qty <= 0) {
            cart = cart.filter(i => i.id !== id);
        }
    }
    renderCart();
}

function renderCart() {
    let html = "";
    let subtotal = 0;

    cart.forEach(item => {
        let itemSubtotal = item.price * item.qty;
        subtotal += itemSubtotal;

        html += `
        <div class="cart-item ${cart.length > 6 ? 'small' : ''}">
            <div>
                <strong>${item.name}</strong><br>
                Rp ${item.price.toLocaleString('id-ID')}
            </div>
            <div class="qty-control">
                <button type="button" class="qty-btn" onclick="decreaseQty(${item.id})">-</button>
                <span>${item.qty}</span>
                <button type="button" class="qty-btn" onclick="increaseQty(${item.id})">+</button>
            </div>
        </div>`;
    });

    const tax = subtotal * 0.10;
    const total = subtotal + tax;

    // Update tampilan Summary
    const elSubtotal = document.getElementById("subtotal");
    const elTax = document.getElementById("tax");
    const elTotal = document.getElementById("total");
    const elCartItems = document.getElementById("cartItems");
    const elCartData = document.getElementById("cartData");

    if (elCartItems) elCartItems.innerHTML = html;
    if (elSubtotal) elSubtotal.innerText = subtotal.toLocaleString('id-ID');
    if (elTax) elTax.innerText = tax.toLocaleString('id-ID');
    if (elTotal) elTotal.innerText = total.toLocaleString('id-ID');
    if (elCartData) elCartData.value = JSON.stringify(cart);
}

// ==============================================================
// LOGIKA API JSON (FETCH) UNTUK NOTIFIKASI & KONFIRMASI KASIR
// ==============================================================

// 1. Fetch Notifikasi secara berkala (Polling setiap 5 detik)
async function loadNotifikasi() {
    try {
        const response = await fetch('../Controllers/ApiKasir.php?action=get_notif');
        const result = await response.json();
        
        if (result.status === 'success') {
            const listContainer = document.getElementById('notif-list');
            const badge = document.getElementById('notif-badge');
            
            listContainer.innerHTML = ''; // Kosongkan list
            
            if (result.data.length > 0) {
                badge.style.display = 'block'; // Tampilkan titik merah
                result.data.forEach(order => {
                    listContainer.innerHTML += `
                        <div class="notif-item" onclick="openDetailModal(${order.id})">
                            <div style="font-weight: 600; color: #111; font-size: 0.95rem;">
                                <span class="red-dot"></span> Pesanan masuk <span style="font-weight: 400;">menunggu konfirmasi pesanan</span>
                            </div>
                            <div style="color: #888; font-size: 0.8rem; margin-left: 18px; margin-top: 5px;">${order.waktu_text}</div>
                        </div>
                    `;
                });
            } else {
                badge.style.display = 'none'; // Sembunyikan titik merah
                listContainer.innerHTML = '<div style="padding: 20px; text-align: center; color: #888;">Tidak ada pesanan tertunda.</div>';
            }
        }
    } catch (e) { 
        console.error("Gagal memuat notifikasi", e); 
    }
}

// Jalankan loadNotifikasi saat file JS dimuat, dan ulangi setiap 5 detik
loadNotifikasi();
setInterval(loadNotifikasi, 5000); 

// Fungsi Toggle Dropdown Lonceng
function toggleNotif() {
    const dropdown = document.getElementById('notif-dropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// 2. Fetch Detail Pesanan Saat Notif Diklik
async function openDetailModal(id_transaction) {
    document.getElementById('notif-dropdown').style.display = 'none'; // Tutup dropdown
    
    try {
        const response = await fetch(`../Controllers/ApiKasir.php?action=get_detail&id=${id_transaction}`);
        const result = await response.json();

        if (result.status === 'success') {
            const data = result.data;
            
            // Format Waktu & Tanggal
            const dateObj = new Date(data.created_at);
            document.getElementById('modal-date').innerText = dateObj.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            document.getElementById('modal-time').innerText = dateObj.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            
            // LOGIKA CERDAS: Cek apakah punya nama atau Walk-in
            let namaPemesan = "Pelanggan Walk-in (Kasir)";
            if (data.nama_customer) {
                // Huruf kapital di awal untuk role (contoh: Mahasiswa)
                let roleKapital = data.role_customer.charAt(0).toUpperCase() + data.role_customer.slice(1);
                namaPemesan = `${data.nama_customer} (${roleKapital})`;
            }

            // Isi Data Teks HTML
            document.getElementById('modal-order-id').innerText = '#' + (data.queue_number || `TRX-${data.id_transaction}`);
            document.getElementById('modal-cust-name').innerText = namaPemesan;
            document.getElementById('modal-queue').innerText = data.queue_number || '-';
            document.getElementById('modal-total').innerText = 'Rp ' + Number(data.grand_total).toLocaleString('id-ID');
            
            // Ambil data 'jenis_pesanan' (Dine In/Take Away)
            document.getElementById('modal-order-type').innerText = data.jenis_pesanan ? data.jenis_pesanan : 'Belum Ditentukan';

            // Looping List Barang Belanjaan
            let itemsHTML = '';
            data.items.forEach(item => {
                const hargaSatuan = Number(item.subtotal) / Number(item.qty);
                itemsHTML += `
                <div class="modal-item-row">
                    <div class="modal-item-left">
                        <div class="modal-item-name">${item.product_name}</div>
                        <div class="modal-item-sub">${item.qty} x Rp ${hargaSatuan.toLocaleString('id-ID')}</div>
                    </div>
                    <div class="modal-item-price">Rp ${Number(item.subtotal).toLocaleString('id-ID')}</div>
                </div>`;
            });
            document.getElementById('modal-items-container').innerHTML = itemsHTML;
            
            // Simpan ID Transaksi di input hidden untuk konfirmasi
            document.getElementById('active-trans-id').value = data.id_transaction;
            
            // Tampilkan Modal
            document.getElementById('detail-modal').style.display = 'flex';
        }
    } catch (e) {
        alert("Gagal mengambil detail pesanan!");
        console.error(e);
    }
}

// Tutup Modal Detail
function closeDetailModal() { 
    document.getElementById('detail-modal').style.display = 'none'; 
}

// 3. Eksekusi Konfirmasi Pesanan (Update Status & Potong Stok)
async function konfirmasiPesanan() {
    const id_trans = document.getElementById('active-trans-id').value;
    const btn = document.getElementById('btn-konfirmasi');
    
    // Ganti teks tombol saat loading
    btn.innerText = "Memproses..."; 
    btn.disabled = true;

    try {
        const response = await fetch('../Controllers/ApiKasir.php?action=confirm_order', {
            method: 'POST', 
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_transaction: id_trans })
        });
        const result = await response.json();

        if (result.status === 'success') {
            alert("Pesanan berhasil dikonfirmasi!");
            closeDetailModal();
            loadNotifikasi(); // Refresh daftar notifikasi tanpa reload
            window.location.reload(); // Refresh halaman agar stok produk berkurang di layar
        } else {
            alert("Gagal konfirmasi: " + result.message);
        }
    } catch (e) {
        alert("Terjadi kesalahan sistem!");
    } finally {
        btn.innerText = "Konfirmasi"; 
        btn.disabled = false;
    }
}

// 4. Eksekusi Pembatalan Pesanan (Hapus dari Database)
async function batalkanPesanan() {
    const id_trans = document.getElementById('active-trans-id').value;
    const btnBatal = document.getElementById('btn-batal');
    
    // Konfirmasi ganda (alert pop-up) untuk mencegah Kasir salah klik
    if (!confirm("Apakah Anda yakin ingin MEMBATALKAN pesanan ini? Pesanan akan dihapus dari sistem dan tidak dapat dikembalikan.")) {
        return;
    }

    // Ganti teks tombol saat loading
    btnBatal.innerText = "Memproses..."; 
    btnBatal.disabled = true;

    try {
        // Tembak API hapus milik Admin (OrderController)
        const response = await fetch('../Controllers/OrderController.php?action=delete', {
            method: 'POST', 
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id_trans })
        });
        const result = await response.json();

        if (result.status === 'success') {
            alert("Pesanan berhasil dibatalkan!");
            closeDetailModal(); // Tutup Pop-up detail
            loadNotifikasi();   // Refresh otomatis data di lonceng notifikasi Kasir
        } else {
            alert("Gagal membatalkan: " + result.message);
        }
    } catch (e) {
        alert("Terjadi kesalahan sistem saat membatalkan pesanan!");
        console.error(e);
    } finally {
        // Kembalikan tombol seperti semula jika terjadi error
        btnBatal.innerText = "Batalkan"; 
        btnBatal.disabled = false;
    }
}