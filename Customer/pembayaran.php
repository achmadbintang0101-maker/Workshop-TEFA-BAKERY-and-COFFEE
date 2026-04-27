<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta charset="utf-8" />
    <title>TEFA Bakery & Coffee - Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/CSS/pembayaran.css">
</head>
<body>

    <div class="card-container payment-container" id="main-container">
        
        <div class="icon-wrapper">
            <div class="icon-ring-outer">
                <div class="icon-ring-inner">
                    <img src="../Assets/IMG/Kopi atas (1).png" alt="Coffee Icon" class="coffee-icon">
                </div>
            </div>
        </div>

        <div class="payment-header">
            <h2>Payment Method</h2>
            <p>Choose how you want to pay</p>
        </div>

        <div class="payment-options">
            <button class="btn-qris" onclick="pindahHalaman('qris.php')">
                <img src="../Assets/IMG/Tulisan Qris.png" alt="QRIS Logo" class="qris-logo">
            </button>
        </div>

        <button class="btn-back-payment" onclick="pindahHalaman('Keranjang.php')">BACK</button>

    </div>

    <script src="../Assets/JS/Main.js"></script>
</body>
</html>