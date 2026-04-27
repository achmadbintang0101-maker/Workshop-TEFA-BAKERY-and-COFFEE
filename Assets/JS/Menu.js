// =============================================
// DATA SEMUA PRODUK (ganti dari 3 file HTML)
// =============================================
const allProducts = [
    // --- COFFEE ---
    { category: 'coffee', img: '../Assets/IMG/Capucino.png',      name: 'Cappuchino',   price: 8000,  stars: 5, stock: 5  },
    { category: 'coffee', img: '../Assets/IMG/sodakopi.png',      name: 'Soda Coffee',  price: 10000, stars: 5, stock: 0  },
    { category: 'coffee', img: '../Assets/IMG/Americano.png',     name: 'Americano',    price: 10000, stars: 5, stock: 4  },
    { category: 'coffee', img: '../Assets/IMG/mocacino.jpeg',     name: 'Mochaccino',   price: 12000, stars: 4, stock: 10 },
    { category: 'coffee', img: '../Assets/IMG/vanilalatte.jpeg',  name: 'Vanilla Latte',price: 15000, stars: 5, stock: 2  },

    // --- BAKERY ---
    { category: 'bakery', img: '../Assets/IMG/Roti Kasur.jpg',   name: 'Roti Kasur',  price: 18000, stars: 5, stock: 12 },
    { category: 'bakery', img: '../Assets/IMG/Roti coklat.jpeg', name: 'Roti Coklat', price: 15000, stars: 5, stock: 0  },
    { category: 'bakery', img: '../Assets/IMG/Roti kering.jpeg', name: 'Roti Kering', price: 20000, stars: 5, stock: 8  },
    { category: 'bakery', img: '../Assets/IMG/Roti sisir.jpeg',  name: 'Roti Sisir',  price: 25000, stars: 4, stock: 5  },
    { category: 'bakery', img: '../Assets/IMG/Roti abon.jpeg',   name: 'Roti Abon',   price: 22000, stars: 5, stock: 10 },

    // --- SNACKS ---
    { category: 'snacks', img: '../Assets/IMG/Kentang (1).png', name: 'French Fries', price: 12000, stars: 5, stock: 15 },
    { category: 'snacks', img: '../Assets/IMG/Onion ring.png',  name: 'Onion Ring',   price: 10000, stars: 5, stock: 0  },
    { category: 'snacks', img: '../Assets/IMG/Cookies.png',     name: 'Cookies',      price: 15000, stars: 5, stock: 8  },
    { category: 'snacks', img: '../Assets/IMG/Brownies.png',    name: 'Brownies',     price: 12000, stars: 4, stock: 10 },
    { category: 'snacks', img: '../Assets/IMG/Chees cake.png',  name: 'Chees Cake',   price: 10000, stars: 5, stock: 20 },
];

// RENDER PRODUK KE LAYAR (tanpa request server)
function renderProducts(products) {
    const menuList = document.getElementById('menu-list');

    // Animasi hilang dulu sebelum ganti isi
    menuList.style.opacity = '0';
    menuList.style.transform = 'translateY(10px)';

    setTimeout(() => {
        menuList.innerHTML = products.map(p => {
            const starsHtml = '★'.repeat(p.stars) + '☆'.repeat(5 - p.stars);
            const stockHtml = p.stock === 0
                ? `<span class="stok-habis">Habis</span>`
                : `<span>Sisa: ${p.stock}</span>`;

            return `
            <div class="menu-item"
                data-name="${p.name}"
                data-price="${p.price}"
                data-img="${p.img}"
                data-stock="${p.stock}">
                <img src="${p.img}" alt="${p.name}" class="menu-img">
                <div class="menu-details">
                    <div class="menu-name">${p.name}</div>
                    <div class="menu-price">Rp ${p.price.toLocaleString('id-ID')}</div>
                    <div class="menu-meta">
                        <span class="stars">${starsHtml}</span>
                        ${stockHtml}
                    </div>
                </div>
                <button class="add-btn" ${p.stock === 0 ? 'disabled' : ''}>+ Add</button>
            </div>`;
        }).join('');

        // Pasang event listener ke tombol Add yang baru di-render
        attachAddButtons();

        // Animasi muncul kembali
        menuList.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
        menuList.style.opacity = '1';
        menuList.style.transform = 'translateY(0)';
    }, 150);
}

// =============================================
// FILTER KATEGORI (instant, tanpa pindah halaman)
// =============================================
function filterCategory(category, btnEl) {
    // Update tombol aktif
    document.querySelectorAll('.cat-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.classList.add('inactive');
    });
    btnEl.classList.add('active');
    btnEl.classList.remove('inactive');

    // Update judul header
    const titles = { coffee: 'Coffee', bakery: 'Bakery', snacks: 'Snacks' };
    document.getElementById('header-title').innerText = titles[category];

    // Filter & render produk
    const filtered = allProducts.filter(p => p.category === category);
    renderProducts(filtered);
}

// =============================================
// TOMBOL ADD — KERANJANG & BOTTOM BAR
// =============================================
function attachAddButtons() {
    document.querySelectorAll('.add-btn').forEach(button => {
        button.addEventListener('click', function () {
            if (this.disabled) {
                alert('Maaf, stok item ini sedang habis!');
                return;
            }

            const item   = this.closest('.menu-item');
            const name   = item.dataset.name;
            const price  = parseInt(item.dataset.price);
            const img    = item.dataset.img;
            const stock  = parseInt(item.dataset.stock);

            // Update Bottom Bar
            document.getElementById('bottom-img').src   = img;
            document.getElementById('bottom-name').innerText  = name;
            document.getElementById('bottom-price').innerText = 'Rp ' + price.toLocaleString('id-ID');

            // Simpan ke keranjang (localStorage)
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let existing = cart.find(c => c.name === name);

            if (existing) {
                if (existing.qty < existing.stock) {
                    existing.qty += 1;
                } else {
                    alert('Stok maksimal tercapai!');
                    return;
                }
            } else {
                cart.push({ name, price, qty: 1, stock, img });
            }

            localStorage.setItem('cart', JSON.stringify(cart));

            // Animasi tombol
            const orig = this.innerText;
            this.innerText = 'Added ✓';
            this.style.backgroundColor = '#4CAF50';
            setTimeout(() => {
                this.innerText = orig;
                this.style.backgroundColor = '';
            }, 1000);
        });
    });
}

// =============================================
// TRANSISI HALAMAN (tetap dipakai untuk Back & View Order)
// =============================================
function pindahHalaman(url) {
    const container = document.getElementById('main-container');
    container.classList.add('fade-out');
    setTimeout(() => { window.location.href = url; }, 300);
}

// =============================================
// INISIALISASI — tampilkan Coffee saat pertama buka
// =============================================
document.addEventListener('DOMContentLoaded', function () {
    const coffeeBtn = document.querySelector('.cat-btn');
    filterCategory('coffee', coffeeBtn);
});
