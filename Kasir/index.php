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
            echo '<div class="card" data-category="'.$row['category'].'" onclick="addToCart('.$row['id_product'].',\''.htmlspecialchars($row['name']).'\','.$row['price'].')">';
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
<div id="detail-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 10000; justify-content: center; align-items: center;">
    <div style="background: #FDF9F6; width: 90%; max-width: 420px; border-radius: 0; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.3); font-family: 'Inter', sans-serif;">
        
        <div style="background: #3A2318; color: #fff; padding: 25px; display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div style="font-size: 0.85rem; color: #D3C5BD; margin-bottom: 5px;">Order id</div>
                <div id="modal-order-id" style="font-size: 2rem; font-weight: 800; color: #CBA57A;">#...</div>
            </div>
            <div style="text-align: right;">
                <div id="modal-date" style="font-size: 0.9rem; font-weight: 600; color: #EAE0D5;">...</div>
                <div id="modal-time" style="font-size: 1.3rem; font-weight: 700; color: #CBA57A; margin-top: 5px;">...</div>
            </div>
        </div>

        <div class="modal-body-detail">
            <div class="section-title-modal">INFORMASI PELANGGAN</div>
            <div class="info-row">
                <span style="color: #888; font-weight: 500; font-size: 0.95rem;">Nama Pemesan</span>
                <span id="modal-cust-name" style="color: #222; font-weight: 700; font-size: 0.95rem;">...</span>
            </div>
            <div class="info-row">
                <span style="color: #888; font-weight: 500; font-size: 0.95rem;">Jenis Pesanan</span>
                <span id="modal-order-type" style="color: #222; font-weight: 700; font-size: 0.95rem;">Take Away</span>
            </div>
            <div class="info-row" style="margin-bottom: 25px;">
                <span style="color: #888; font-weight: 500; font-size: 0.95rem;">No. Antrian</span>
                <span id="modal-queue" style="color: #222; font-weight: 700; font-size: 0.95rem;">...</span>
            </div>

            <div class="section-title-modal">DETAIL PESANAN</div>
            <div id="modal-items-container">
                <!-- Looping Item Belanjaan Disini -->
            </div>
            
            <div style="background: #3A2318; border-radius: 8px; padding: 15px; display: flex; justify-content: space-between; color: #fff; font-weight: 700; margin-bottom: 25px; font-size: 1.1rem; margin-top: 15px;">
                <span>Total</span>
                <span id="modal-total">...</span>
            </div>

            <!-- Simpan ID Transaksi Tersembunyi untuk Konfirmasi -->
            <input type="hidden" id="active-trans-id">

           <div style="display: flex; gap: 10px;">
                <button onclick="closeDetailModal()" style="flex: 1; padding: 14px; background: #CBA57A; color: #fff; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1rem;">Kembali</button>
                
                <button onclick="batalkanPesanan()" style="flex: 1; padding: 14px; background: #e53935; color: #fff; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1rem;" id="btn-batal">Batalkan</button>
                
                <button onclick="konfirmasiPesanan()" style="flex: 1; padding: 14px; background: #3A2318; color: #fff; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1rem;" id="btn-konfirmasi">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

<script src="../Assets/JS/kasir.js"></script>
</body>
</html>