<?php 
// 1. Proteksi Halaman & Koneksi
include '../Config/auth.php';
include '../Config/koneksi.php';

// Sinkronisasi nama dari session login
$nama_display = isset($_SESSION['nama']) ? $_SESSION['nama'] : "Kasir";
?>
<!DOCTYPE html>
<html>
<head>
    <title>POS Bread & Coffee</title>
    <link rel="stylesheet" href="../Assets/CSS/kasir.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="left">
    <div class="header-pos">
        <h1>TEFA Bread & Coffee ☕</h1>
        
        <div class="datetime-box">
            <span id="tanggal" class="tanggal"></span>
            <span id="jam" class="jam"></span>
        </div>
    </div>

    <div class="categories">
        <button class="btn-cat active" onclick="filterCategory('all', this)">All</button>
        <button class="btn-cat" onclick="filterCategory('coffee', this)">Coffee</button>
        <button class="btn-cat" onclick="filterCategory('bread', this)">Bread</button>
        <button class="btn-cat" onclick="filterCategory('snack', this)">Snacks</button>
    </div>

    <div class="products">
    <?php
    // Query UNION untuk mengambil masing-masing 5 produk per kategori
    $query = mysqli_query($conn,"
        (SELECT * FROM products WHERE category='coffee' LIMIT 5)
        UNION ALL
        (SELECT * FROM products WHERE category='bread' LIMIT 5)
        UNION ALL
        (SELECT * FROM products WHERE category='snack' LIMIT 5)
    ");

    while($row = mysqli_fetch_assoc($query)){
    ?>
        <div class="card" 
             data-category="<?= $row['category']; ?>" 
             onclick="addToCart(<?= $row['id']; ?>,'<?= $row['name']; ?>',<?= $row['price']; ?>)">
            <img src="../Assets/IMG/<?= $row['image']; ?>" alt="<?= $row['name']; ?>">
            <h4><?= $row['name']; ?></h4>
            <p>Rp <?= number_format($row['price'], 0, ',', '.'); ?></p>
        </div>
    <?php } ?>
    </div>
</div>

<div class="cart">
    <div class="logout-area">
        <div><strong>Kasir: <?= $nama_display; ?></strong></div>
        <a href="../Index.php" class="logout-btn">Logout</a>
    </div>

    <h2>🛒 Cart</h2>
    <div class="cart-items" id="cartItems">
        </div>

    <div class="cart-summary">
        <p>Subtotal: Rp <span id="subtotal">0</span></p>
        <p>PPN 10%: Rp <span id="tax">0</span></p> <div class="total">Total: Rp <span id="total">0</span></div>

        <form method="POST" action="proses_bayar.php">
            <input type="hidden" name="cartData" id="cartData">
            <button type="submit" class="pay-btn">Make Payment</button>
        </form>
    </div>
</div>

<script src="../Assets/JS/kasir.js"></script>
</body>
</html>