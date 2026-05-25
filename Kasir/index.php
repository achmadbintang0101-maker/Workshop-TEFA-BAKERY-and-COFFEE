<?php 
// Kasir/index.php      
include '../Config/auth.php'; 
include '../Classes/Database.php';
include '../Classes/Product.php';

$database = new Database();
$conn = $database->getConnection();
$productObj = new Product($conn); 

$nama_display = isset($_SESSION['nama']) ? $_SESSION['nama'] : "Kasir";
$produk_kasir = $productObj->getAvailableProducts();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>POS Bread & Coffee</title>
    <!-- CSS Eksternal Dipanggil Disini -->
    <link rel="stylesheet" href="../Assets/CSS/kasir.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<div class="left">
    <div class="header-pos">
        <h1>TEFA Bread & Coffee ☕</h1>
        
        <div style="display: flex; align-items: center; gap: 20px; position: relative;">
            <div class="datetime-box">
                <span id="tanggal" class="tanggal"></span>
                <span id="jam" class="jam"></span>
            </div>
            
            <!-- LOGO LONCENG -->
            <div style="position: relative; cursor: pointer;" onclick="toggleNotif()">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#4A2C1D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                <span id="notif-badge" style="position: absolute; top: -2px; right: 2px; background: #FF4500; width: 12px; height: 12px; border-radius: 50%; display: none;"></span>
            </div>
            
            <!-- DROPDOWN NOTIFIKASI SIDEBAR KANAN -->
            <div id="notif-dropdown" class="notif-dropdown">
                <div class="notif-header">
                    <h3 style="margin: 0; font-size: 1.2rem; color: #111;">Notifikasi</h3>
                    <span style="cursor: pointer; font-weight: bold; font-size: 1.5rem;" onclick="toggleNotif()">&times;</span>
                </div>
                <!-- Perbaikan Tinggi List agar penuh ke bawah -->
                <div id="notif-list" style="height: calc(100vh - 75px); overflow-y: auto;">
                    <!-- Diisi oleh JS API -->
                    <div style="padding: 20px; text-align: center; color: #888;">Memuat data...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sisa Konten Kiri (Kategori & Produk) -->
    <div class="categories">
        <button class="btn-cat active" onclick="filterCategory('all', this)">All</button>
        <button class="btn-cat" onclick="filterCategory('coffee', this)">Coffee</button>
        <button class="btn-cat" onclick="filterCategory('bread', this)">Bread</button>
        <button class="btn-cat" onclick="filterCategory('snack', this)">Snacks</button>
    </div>

    <div class="products">
    <?php
    if(mysqli_num_rows($produk_kasir) > 0) {
        while($row = mysqli_fetch_assoc($produk_kasir)){
            // PERHATIKAN BARIS INI: Parameter disusun rapi -> (id, nama, harga, stok)
            echo '<div class="card" data-category="'.$row['category'].'" onclick="addToCart('.$row['id_product'].',\''.htmlspecialchars($row['name']).'\','.$row['price'].', '.$row['stok'].')">';
            echo '<img src="../Assets/IMG/'.$row['image'].'" alt="'.htmlspecialchars($row['name']).'">';
            echo '<h4>'.htmlspecialchars($row['name']).'</h4>';
            echo '<p>Rp '.number_format($row['price'], 0, ',', '.').'</p>';
            echo '<small style="color: #888;">Stok: '.$row['stok'].'</small>';
            echo '</div>';
        } 
    }
    ?>
    </div>
</div>

<!-- Sisa Konten Kanan (Cart Kasir Manual) -->
<div class="cart">
    <div class="logout-area">
        <div><strong>Kasir: <?= htmlspecialchars($nama_display); ?></strong></div>
<a href="../logout.php" class="logout-btn">Logout</a>
    </div>

    <h2>🛒 Cart</h2>
    <div class="cart-items" id="cartItems"></div>

    <div class="cart-summary">
        <p>Subtotal: Rp <span id="subtotal">0</span></p>
        <p>PPN 10%: Rp <span id="tax">0</span></p> 
        <div class="total">Total: Rp <span id="total">0</span></div>

        <form method="POST" action="../Controllers/TransactionController.php">
            <input type="hidden" name="cartData" id="cartData">
            <button type="submit" class="pay-btn">Make Payment</button>
        </form>
    </div>
</div>

<!-- MODAL DETAIL PESANAN -->
<div id="detail-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10000; justify-content:center; align-items:center;">
    <div class="modal-kasir-wrap">

        <!-- HEADER -->
        <div class="modal-kasir-header">
            <div>
                <div class="modal-kasir-label">No. Antrian</div>
                <div id="modal-order-id" class="modal-kasir-id">#...</div>
            </div>
            <div style="text-align:right;">
                <div id="modal-date" class="modal-kasir-date">...</div>
                <div id="modal-time" class="modal-kasir-time">...</div>
            </div>
        </div>

        <!-- BODY: scroll keseluruhan isi -->
        <div class="modal-kasir-body">

            <!-- Info pelanggan -->
            <div class="modal-kasir-section">INFORMASI PELANGGAN</div>
            <div class="modal-kasir-row">
                <span class="modal-kasir-key">Nama Pemesan</span>
                <span id="modal-cust-name" class="modal-kasir-val">...</span>
            </div>
            <div class="modal-kasir-row">
                <span class="modal-kasir-key">Jenis Pesanan</span>
                <span id="modal-order-type" class="modal-kasir-val">Belum Ditentukan</span>
            </div>
            <div class="modal-kasir-row" style="margin-bottom:16px;">
                <span class="modal-kasir-key">No. Antrian</span>
                <span id="modal-queue" class="modal-kasir-val">...</span>
            </div>

            <!-- Detail pesanan -->
            <div class="modal-kasir-section">DETAIL PESANAN</div>
            <div id="modal-items-container">
                <!-- item diisi JS -->
            </div>

            <!-- Total -->
            <div class="modal-kasir-total">
                <span>Total</span>
                <span id="modal-total">...</span>
            </div>
        </div>

        <!-- FOOTER tombol -->
        <div class="modal-kasir-footer">
            <input type="hidden" id="active-trans-id">
            <button onclick="closeDetailModal()" class="btn-kasir-back">Kembali</button>
            <button onclick="batalkanPesanan()" class="btn-kasir-batal" id="btn-batal">Batalkan</button>
            <button onclick="konfirmasiPesanan()" class="btn-kasir-konfirm" id="btn-konfirmasi">Konfirmasi</button>
        </div>

    </div>
</div>

<script src="../Assets/JS/kasir.js"></script>
</body>
</html>