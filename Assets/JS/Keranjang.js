// Default tab yang terbuka pertama kali
let activeCategory = 'Coffee'; 

function formatRupiah(angka) {
    return 'Rp ' + angka.toLocaleString('id-ID');
}

function getCartData() {
    return JSON.parse(localStorage.getItem('cart')) || [];
}

function saveCartData(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
}

// Fungsi untuk berpindah tab kategori keranjang
function switchCategory(catName, btnElement) {
    activeCategory = catName;

    // Ubah warna tombol (active/inactive)
    const tabs = document.querySelectorAll('#cart-tabs .cat-btn');
    tabs.forEach(tab => {
        tab.classList.remove('active');
        tab.classList.add('inactive');
    });
    btnElement.classList.remove('inactive');
    btnElement.classList.add('active');

    // Render ulang isi keranjang
    renderCart();
}

function renderCart() {
    const cartContainer = document.getElementById('cart-container');
    const cartData = getCartData();
    
    cartContainer.innerHTML = ''; 
    let subtotalAll = 0; // Subtotal untuk SEMUA kategori (Singkronisasi Harga)

    // 1. Hitung total keseluruhan semua item di keranjang dulu
    cartData.forEach(item => {
        subtotalAll += (item.price * item.qty);
    });

    // 2. Filter item yang hanya cocok dengan tab yang diklik
    // (Jika item.category kosong/dari data lama, otomatis masuk ke Coffee)
    const filteredCart = cartData.filter(item => item.category === activeCategory || (!item.category && activeCategory === 'Coffee'));

    // Tampilan jika kategori tersebut kosong
    if (filteredCart.length === 0) {
        cartContainer.innerHTML = `
            <div style="text-align:center; padding: 30px 20px; color: #A58F81; font-weight: 600; font-family: 'Inter', sans-serif;">
                <img src="../Assets/IMG/LogoTefa.jpeg" style="width:80px; height:80px; border-radius:50%; margin-bottom:15px; opacity:0.5; object-fit:cover;">
                <br>Keranjang pesanan ${activeCategory} masih kosong.<br>Silakan pilih menu terlebih dahulu.
            </div>`;
    } else {
        // Tampilan jika ada pesanan di kategori tersebut
        filteredCart.forEach((item) => {
            // Cari index aslinya di data keseluruhan agar tombol tambah/kurang tetap akurat
            const originalIndex = cartData.findIndex(cartItem => cartItem.name === item.name);

            const itemHTML = `
                <div class="cart-item">
                    <img src="${item.img}" alt="${item.name}" class="cart-img">
                    <div class="cart-details">
                        <div>
                            <div class="item-name">${item.name}</div>
                            <div class="item-meta">
                                <span>${formatRupiah(item.price)}</span>
                                <span>Sisa: ${item.stock}</span>
                            </div>
                        </div>
                        
                        <div class="qty-control">
                            <button class="qty-btn" onclick="updateQty(${originalIndex}, -1)">-</button>
                            <span class="qty-number">${item.qty}</span>
                            <button class="qty-btn plus" onclick="updateQty(${originalIndex}, 1)">+</button>
                        </div>
                    </div>
                </div>
            `;
            cartContainer.innerHTML += itemHTML;
        });
    }

    // 3. Masukkan hasil perhitungan KESELURUHAN ke layar
    const tax = subtotalAll * 0.10;
    const total = subtotalAll + tax;

    document.getElementById('subtotal-val').innerText = formatRupiah(subtotalAll);
    document.getElementById('tax-val').innerText = formatRupiah(tax);
    document.getElementById('total-val').innerText = formatRupiah(total);
}

function updateQty(index, change) {
    let cartData = getCartData();
    let newQty = cartData[index].qty + change;

    if (newQty > 0 && newQty <= cartData[index].stock) {
        cartData[index].qty = newQty; 
    } else if (newQty === 0) {
        cartData.splice(index, 1);
    } else if (newQty > cartData[index].stock) {
        alert('Stok maksimal tercapai!');
    }
    
    saveCartData(cartData); 
    renderCart(); 
}

document.addEventListener('DOMContentLoaded', () => {
    renderCart();
});

// Fungsi Transisi Halaman khusus Keranjang
function pindahHalaman(url) {
    const container = document.getElementById('main-container');
    if (container) {
        container.classList.add('fade-out');
        setTimeout(() => {
            window.location.href = url;
        }, 300);
    } else {
        window.location.href = url;
    }
}