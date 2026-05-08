<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta charset="utf-8" />
    <title>TEFA Bakery & Coffee - Antrean</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Inter', sans-serif; background-color: #333; display: flex; justify-content: center; min-height: 100vh; }
        .card-container { background-color: #FDF9F6; width: 100%; max-width: 400px; padding: 40px 20px; box-sizing: border-box; text-align: center; display: flex; flex-direction: column; justify-content: center; }
        .icon { width: 80px; margin-bottom: 20px; }
        h2 { color: #6D3D22; margin: 0 0 10px 0; }
        p { color: #A58F81; font-size: 0.95rem; margin-bottom: 30px; }
        .antrean-box { background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); margin-bottom: 40px; }
        .antrean-title { color: #A58F81; font-weight: bold; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }
        .antrean-number { color: #6D3D22; font-size: 3.5rem; font-weight: 800; margin: 10px 0 0 0; }
        .btn-home { padding: 15px; background: #6D3D22; color: #fff; border: none; border-radius: 10px; font-weight: bold; font-size: 1rem; cursor: pointer; width: 100%; }
    </style>
</head>
<body>

    <div class="card-container">
        <div>
            <!-- Menggunakan icon centang hijau bawaanmu -->
            <img src="../Assets/IMG/Centang asli (2).png" alt="Success" class="icon">
        </div>
        
        <h2>Pesanan Berhasil!</h2>
        <p>Silakan tunggu nomor antrean Anda dipanggil oleh Kasir untuk melakukan pembayaran.</p>

        <div class="antrean-box">
            <div class="antrean-title">NOMOR ANTREAN</div>
            <h1 class="antrean-number" id="nomor-tampil">...</h1>
        </div>

        <button class="btn-home" onclick="selesaiDanKembali()">Kembali ke Halaman Awal</button>
    </div>

    <script>
        // Tangkap nomor antrean dari URL
        document.addEventListener("DOMContentLoaded", () => {
            const urlParams = new URLSearchParams(window.location.search);
            const antrean = urlParams.get('antrean');
            
            if(antrean) {
                document.getElementById('nomor-tampil').innerText = antrean;
            } else {
                document.getElementById('nomor-tampil').innerText = "ERROR";
            }
        });

        // Hapus memori keranjang dan kembali ke awal
        function selesaiDanKembali() {
            localStorage.removeItem('cart');
            window.location.href = 'Awal.php';
        }
    </script>
</body>
</html>