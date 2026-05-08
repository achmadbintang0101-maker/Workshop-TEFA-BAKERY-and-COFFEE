<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta charset="utf-8" />
    <title>TEFA Bakery & Coffee - Nota Customer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/CSS/Keranjang.css">
</head>
<body>

    <div class="card-container" id="main-container">
        
        <div class="header-nota">
            <h1>Nota Customer</h1>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="#6D3D22" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z"/>
            </svg>
        </div>

        <div class="categories" id="cart-tabs">
            <button class="cat-btn active" onclick="switchCategory('Coffee', this)">Coffee</button>
            <button class="cat-btn inactive" onclick="switchCategory('Bakery', this)">Bakery</button>
            <button class="cat-btn inactive" onclick="switchCategory('Snacks', this)">Snacks</button>
        </div>

        <div class="cart-list" id="cart-container">
            </div>

        <div class="summary-card">
            <div class="summary-row">
                <span class="summary-label">Subtotal</span>
                <span class="summary-value" id="subtotal-val">Rp 0</span>
            </div>
            <div class="summary-row border-bottom">
                <span class="summary-label">Tax (10%)</span>
                <span class="summary-value" id="tax-val">Rp 0</span>
            </div>
            <div class="summary-row total-row">
                <span class="summary-label">Total</span>
                <span class="summary-total" id="total-val">Rp 0</span>
            </div>

           <div class="action-buttons">
                <button class="btn btn-back" onclick="window.location.href='Menu.php'">Back</button>
                <button class="btn btn-complete" onclick="window.location.href='pembayaran.php'">Lanjut Validasi</button>
            </div>
        </div>

    </div>

    <script src="../Assets/JS/Keranjang.js"></script>
</body>
</html>