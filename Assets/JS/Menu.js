// Deklarasikan menuData sebagai array kosong
let menuData = []; 

// Fungsi untuk format rupiah
function formatRupiah(angka) {
    return 'Rp ' + angka.toLocaleString('id-ID');
}

// =========================================================
// FUNGSI BARU: Mengupdate Bar Bawah (Mini Cart) Real-Time
// =========================================================
function updateMiniCart(lastAddedImg = null, lastAddedName = null) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    const bottomImg = document.getElementById('bottom-img');
    const bottomName = document.getElementById('bottom-name');
    const bottomPrice = document.getElementById('bottom-price');
    
    // Jika keranjang kosong
    if (cart.length === 0) {
        if(bottomName) bottomName.innerText = "Belum ada pesanan";
        if(bottomPrice) bottomPrice.innerText = "-";
        return;
    }

    // Hitung total item dan subtotal harga murni (tanpa pajak)
    let totalQty = 0;
    let subtotal = 0;
    
    cart.forEach(item => {
        totalQty += item.qty;
        subtotal += (item.price * item.qty);
    });

    // Menampilkan nama barang terakhir yang dipencet beserta total item keseluruhan
    if (lastAddedName && bottomName) {
        bottomName.innerText = `${lastAddedName} ditambahkan (Total: ${totalQty} Item)`;
    } else if (bottomName) {
        // Jika halaman baru direfresh, tampilkan total standar
        bottomName.innerText = `Total ${totalQty} Item di Keranjang`;
    }
    
    // Menampilkan harga murni sebelum pajak 10%
    if(bottomPrice) bottomPrice.innerText = formatRupiah(subtotal);

    // Mengubah ikon gambar di bottom bar menjadi gambar produk terakhir
    if (lastAddedImg && bottomImg) {
        bottomImg.src = lastAddedImg;
    } else if (bottomImg && cart.length > 0) {
        bottomImg.src = cart[cart.length - 1].img;
    }
}

// Fungsi untuk mengambil data produk dari database via Controller
async function fetchProducts() {
    try {
        const response = await fetch('../Controllers/ProductController.php?action=api_get_products');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        // Masukkan data dari database ke variabel menuData
        menuData = data;
        
        // Panggil fungsi filter kategori (default ke 'coffee' saat pertama kali dimuat)
        const defaultBtn = document.querySelector('.cat-btn.active');
        if (defaultBtn) {
            filterCategory('coffee', defaultBtn);
        }
    } catch (error) {
        console.error("Gagal mengambil data produk:", error);
    }
}

// Panggil fungsi fetch dan UPDATE BAR BAWAH saat halaman selesai dimuat
document.addEventListener('DOMContentLoaded', () => {
    fetchProducts();
    updateMiniCart(); // <--- Agar saat pelanggan back dari Keranjang, bar bawah tetap terisi
});

// Fungsi untuk filter berdasarkan kategori
function filterCategory(category, btnElement) {
    // Ubah class active/inactive pada tombol
    const tabs = document.querySelectorAll('.cat-btn');
    tabs.forEach(tab => {
        tab.classList.remove('active');
        tab.classList.add('inactive');
    });
    
    if (btnElement) {
        btnElement.classList.remove('inactive');
        btnElement.classList.add('active');
    }

    // Filter data berdasarkan kategori
    const filteredData = menuData.filter(item => item.category === category);
    renderMenu(filteredData);
}

// Fungsi untuk merender HTML produk ke layar
function renderMenu(data) {
    const menuList = document.getElementById('menu-list');
    menuList.innerHTML = ''; // Kosongkan list sebelumnya

    if (data.length === 0) {
        menuList.innerHTML = '<p style="text-align:center; width:100%; color: #A58F81; margin-top: 20px;">Menu kategori ini sedang kosong.</p>';
        return;
    }

    // Looping data dan buat elemen HTML untuk setiap produk
    data.forEach(item => {
        const cardHTML = `
            <div class="menu-card" style="display: flex; align-items: center; justify-content: space-between; background: white; padding: 15px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <img src="${item.img}" alt="${item.name}" class="menu-img" style="width: 70px; height: 70px; border-radius: 10px; object-fit: cover; margin-right: 15px;">
                <div class="menu-info" style="flex: 1;">
                    <div class="menu-title" style="font-weight: 700; color: #6D3D22; font-size: 1.1rem;">${item.name}</div>
                    <div class="menu-price" style="color: #A58F81; font-weight: 600; margin-bottom: 5px;">${formatRupiah(item.price)}</div>
                    <div style="font-size: 0.8rem; color: #777; margin-bottom: 8px;">Sisa stok: ${item.stock}</div>
                </div>
                <button class="add-btn" onclick="addToCart(${item.id}, '${item.name}', ${item.price}, '${item.img}', '${item.category}', ${item.stock})" style="background: #6D3D22; color: white; border: none; padding: 8px 15px; border-radius: 8px; font-weight: 600; cursor: pointer;">Tambah</button>
            </div>
        `;
        menuList.innerHTML += cardHTML;
    });
}

// Fungsi untuk memasukkan pesanan ke Local Storage (Keranjang)
function addToCart(id, name, price, img, category, stock) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let existingItem = cart.find(item => item.id === id);

    if (existingItem) {
        // Cek jika jumlah yang ditambahkan melebihi stok di database
        if (existingItem.qty < stock) {
            existingItem.qty += 1;
        } else {
            // HANYA MUNCUL JIKA STOK HABIS
            alert('Maaf, stok ' + name + ' tidak mencukupi!');
            return; // Hentikan eksekusi agar tidak jadi ditambah
        }
    } else {
        // Jika belum ada di keranjang, masukkan item baru
        cart.push({ id, name, price, img, category, stock, qty: 1 });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Panggil fungsi ajaib Bar Bawah
    updateMiniCart(img, name);
}

// Fungsi untuk transisi antar halaman Customer
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