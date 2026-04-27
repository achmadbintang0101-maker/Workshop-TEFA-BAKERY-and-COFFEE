<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta charset="utf-8" />
    <title>TEFA Bakery & Coffee</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/CSS/Main.css">
</head>
<body>

    <div class="card-container" id="main-container">
        
        <div class="header">
            <img src="../Assets/IMG/Gambarbgrn.png" alt="Header">
            <h1 id="header-title">Coffee</h1>
        </div>

        <div class="categories">
            <button class="cat-btn active" onclick="filterCategory('coffee', this)">Coffee</button>
            <button class="cat-btn inactive" onclick="filterCategory('bakery', this)">Bakery</button>
            <button class="cat-btn inactive" onclick="filterCategory('snacks', this)">Snacks</button>
        </div>

        <div class="menu-list" id="menu-list">
            <!-- Diisi oleh JavaScript -->
        </div>

        <div class="bottom-bar">
            <img src="../Assets/IMG/Americano.png" alt="Selected" class="bottom-img" id="bottom-img">
            <div class="bottom-info">
                <div class="bottom-name" id="bottom-name">Belum ada pesanan</div>
                <div class="bottom-price" id="bottom-price">-</div>
                <div class="bottom-actions">
                    <a href="Awal.php" class="btn-action btn-back">Back</a>
                    <button class="btn-action btn-view" onclick="pindahHalaman('Keranjang.php')">View Order</button>
                </div>
            </div>
        </div>

    </div>

    <script src="../Assets/JS/Menu.js"></script>
</body>
</html>
