<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta charset="utf-8" />
    <title>TEFA Bakery & Coffee - Scan QRIS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/CSS/qris.css">
</head>
<body>

    <div class="card-container qris-container" id="main-container">
        
        <div class="icon-wrapper">
            <img src="../Assets/IMG/Logoqrs.png" alt="Payment Icon" class="pay-icon">
        </div>

        <h2 class="qr-title">QR CODE:</h2>

       <div class="qr-wrapper">
            <img src="../Assets/IMG/Myqrisgweh.png" alt="QR Code" class="qr-image" 
                 onclick="pindahHalaman('sukses.php')" 
                 style="cursor: pointer;" 
                 title="Klik untuk simulasi bayar sukses">
        </div>

        <button class="btn-back-qris" onclick="pindahHalaman('pembayaran.php')">BACK</button>

    </div>

    <script src="../Assets/JS/Main.js"></script>
</body>
</html>