 // Fungsi khusus untuk mereset aplikasi
        function selesaikanPesanan() {
            // 1. Hapus memori keranjang (Reset)
            localStorage.removeItem('cart');
            
            // 2. Mainkan animasi fade-out
            const container = document.getElementById('main-container');
            container.classList.add('fade-out');
            
            // 3. Kembali ke halaman Awal
            setTimeout(() => {
                window.location.href = 'Awal.php'; 
            }, 300);
        }
