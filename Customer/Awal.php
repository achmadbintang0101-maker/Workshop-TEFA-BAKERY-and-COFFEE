<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta charset="utf-8" />
    <title>TEFA Bakery & Coffee - Choose Order</title>
    <link rel="stylesheet" href="../Assets/CSS/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@600;700;800&display=swap" rel="stylesheet">
</head>
<body>

    <div class="card-container" id="main-container">
        
        <div class="card-header">
            <img src="../Assets/IMG/Gambarbgrn.png" alt="Coffee Background" class="bg-image">
            <h1 class="brand-title">Tefa Bakery and<br>Coffee</h1>
        </div>

        <div class="card-body">
            <div class="section-title">
                <img src="../Assets/IMG/Rotikecil.png" alt="Bread Icon" class="title-icon">
                <h2>Choose Order!</h2>
            </div>

            <div class="action-buttons">
                <button class="btn btn-dine-in" onclick="pindahHalaman('Menu.php')">
                    <img src="../Assets/IMG/Roti.png" alt="Dine In Icon" class="btn-icon">
                    <span>Dine In</span>
                </button>

                <div class="or-divider">or</div>

                <button class="btn btn-take-away" onclick="pindahHalaman('Menu.php')">
                    <img src="../Assets/IMG/Keranjang.png" alt="Take Away Icon" class="btn-icon">
                    <span>Take Away</span>
                </button>
            </div>

            <div style="margin-top: 5px; text-align: left; padding-left: 5px; margin-bottom: 0px;">
                <a href="../Index.php" style="text-decoration: none; font-size: 13px; font-weight: 600; color: #8a7060; opacity: 0.8; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
                    Keluar
                </a>
            </div>

        </div>

    </div>

    <script src="../Assets/JS/Awal.js"></script>
</body>
</html>