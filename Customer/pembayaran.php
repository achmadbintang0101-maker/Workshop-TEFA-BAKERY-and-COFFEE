<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta charset="utf-8" />
    <title>TEFA Bakery & Coffee - Validasi Pesanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Styling cepat agar serasi dengan tema coklat TEFA */
        body { margin: 0; font-family: 'Inter', sans-serif; background-color: #333; display: flex; justify-content: center; min-height: 100vh; }
        .card-container { background-color: #FDF9F6; width: 100%; max-width: 400px; padding: 30px 20px; box-sizing: border-box; display: flex; flex-direction: column; }
        .header h2 { color: #6D3D22; margin: 0 0 5px 0; text-align: center; }
        .header p { color: #A58F81; font-size: 0.9rem; text-align: center; margin-bottom: 20px; }
        .detail-box { background: #fff; padding: 15px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .item-list { font-size: 0.9rem; color: #555; border-bottom: 1px dashed #E0D5CE; padding-bottom: 10px; margin-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; color: #6D3D22; font-weight: 600; font-size: 0.9rem; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid #E0D5CE; border-radius: 8px; font-family: 'Inter', sans-serif; box-sizing: border-box; }
        .btn-row { display: flex; gap: 10px; margin-top: 20px; }
        .btn-back { flex: 1; padding: 12px; background: transparent; color: #6D3D22; border: 2px solid #6D3D22; border-radius: 8px; font-weight: bold; cursor: pointer; }
        .btn-submit { flex: 1; padding: 12px; background: #6D3D22; color: #fff; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

    <div class="card-container" id="main-container">
        <div class="header">
            <h2>Validasi Pesanan</h2>
            <p>Periksa detail dan lengkapi identitas</p>
        </div>

        <div class="detail-box">
            <strong style="color:#6D3D22;">Pesanan Anda:</strong>
            <div id="order-list" class="item-list">
                <!-- Diisi oleh JS -->
            </div>
            <div style="display: flex; justify-content: space-between; font-weight: bold; color: #6D3D22;">
                <span>Total Bayar:</span>
                <span id="grand-total-text">Rp 0</span>
            </div>
        </div>

        <div class="form-group">
            <label>Nama Pemesan</label>
            <input type="text" id="cust-name" placeholder="Masukkan nama Anda...">
        </div>

        <div class="form-group">
            <label>Sebagai Apa?</label>
            <select id="cust-role">
                <option value="" disabled selected>Pilih identitas...</option>
                <option value="dosen">Dosen</option>
                <option value="mahasiswa">Mahasiswa</option>
                <option value="umum">Umum</option>
            </select>
        </div>

        <div class="btn-row">
            <button class="btn-back" onclick="window.location.href='Keranjang.php'">Back</button>
            <button class="btn-submit" onclick="submitOrder()">Complete Order</button>
        </div>
    </div>

    <script>
        // 1. Tampilkan Detail Pesanan saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            if(cart.length === 0) {
                alert("Keranjang kosong!"); window.location.href='Menu.php'; return;
            }

            let html = '';
            let subtotal = 0;
            
            cart.forEach(item => {
                html += `<div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                            <span>${item.qty}x ${item.name}</span>
                            <span>Rp ${(item.price * item.qty).toLocaleString('id-ID')}</span>
                         </div>`;
                subtotal += (item.price * item.qty);
            });
            
            let tax = subtotal * 0.10;
            let grandTotal = subtotal + tax;

            document.getElementById('order-list').innerHTML = html;
            document.getElementById('grand-total-text').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
        });

        // 2. Fungsi Submit ke Database
        async function submitOrder() {
            const custName = document.getElementById('cust-name').value;
            const custRole = document.getElementById('cust-role').value;
            const cartData = JSON.parse(localStorage.getItem('cart')) || [];

            if (!custName || !custRole) {
                alert("Harap isi Nama dan Identitas terlebih dahulu!"); return;
            }

            let subtotal = 0;
            cartData.forEach(item => subtotal += (item.price * item.qty));
            let tax = subtotal * 0.10;
            let grandTotal = subtotal + tax;

            const payload = {
                nama: custName, role: custRole,
                total_price: subtotal, tax: tax, grand_total: grandTotal,
                items: cartData
            };

            // Animasi tombol loading
            const btnSubmit = document.querySelector('.btn-submit');
            btnSubmit.innerText = "Memproses..."; btnSubmit.disabled = true;

            try {
                // Tembak ke API backend yang kita buat di TransactionController.php
                const response = await fetch('../Controllers/TransactionController.php?action=api_checkout', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();

                if (result.status === 'success') {
                    // Berhasil! Bawa nomor antrean ke halaman sukses
                    window.location.href = `sukses.php?antrean=${result.queue_number}`;
                } else {
                    alert("Gagal memproses pesanan!");
                    btnSubmit.innerText = "Complete Order"; btnSubmit.disabled = false;
                }
            } catch (error) {
                console.error(error); alert("Terjadi kesalahan sistem!");
            }
        }
    </script>
</body>
</html>