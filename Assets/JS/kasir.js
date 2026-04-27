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

function addToCart(id, name, price) {
    let found = cart.find(item => item.id === id);
    if (found) {
        found.qty += 1;
    } else {
        cart.push({ id, name, price: parseFloat(price), qty: 1 });
    }
    renderCart();
}

function increaseQty(id) {
    let item = cart.find(i => i.id === id);
    if (item) item.qty += 1;
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