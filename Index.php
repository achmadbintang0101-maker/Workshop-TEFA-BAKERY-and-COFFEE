<?php 
// Memanggil bagian atas halaman (Header & CSS)
include 'include/header.php'; 
?>

    <section id="home" class="hero">
        <div class="hero-content">
            <h1>Awali Harimu dengan Roti Hangat & Kopi Pilihan</h1>
            <p>Nikmati perpaduan sempurna dari TEFA Bakery & Coffee. Segar dari oven setiap hari.</p>
            <a href="Customer/Awal.php" class="btn-cta">Pesan Sekarang</a>
        </div>
    </section>

    <section id="about" class="about-section">
        <div class="about-container">
            <div class="about-text">
                <h2>Tentang Kami</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. TEFA Bakery & Coffee didirikan untuk memberikan pengalaman kuliner terbaik bagi mahasiswa dan masyarakat sekitar.</p>
                <h3>Sejarah Singkat</h3>
                <p>Bermula dari inisiatif program pada tahun XXXX, kami berkembang dari dapur kecil menjadi unit produksi mandiri yang mengutamakan kualitas bahan dan standar kebersihan tinggi.</p>
            </div>
            
            <div class="about-image">
                <div class="box-grid">
                    <div class="grid-item"><img src="assets/Jurusan.png" alt="Gambar 1"></div>
                    <div class="grid-item"><img src="assets/Jurusan.png" alt="Gambar 2"></div>
                    <div class="grid-item"><img src="assets/Jurusan.png" alt="Gambar 3"></div>
                    <div class="grid-item"><img src="assets/Jurusan.png" alt="Gambar 4"></div>
                </div>
            </div>
        </div>
    </section>
    
    <section id="produk" class="produk-section">
        <h2>Menu Favorit Kami</h2>
        <div class="produk-grid">
            <div class="produk-card">
                <img src="https://via.placeholder.com/250x200.png?text=Foto+Kopi+1" alt="Kopi">
                <h3>Kopi Susu Gula Aren</h3>
                <p>Perpaduan espresso mantap dengan manisnya gula aren asli.</p>
            </div>
            <div class="produk-card">
                <img src="https://via.placeholder.com/250x200.png?text=Foto+Roti+1" alt="Roti">
                <h3>Roti Coklat Lumer</h3>
                <p>Roti lembut dengan isian coklat premium yang meleleh di mulut.</p>
            </div>
            <div class="produk-card">
                <img src="https://via.placeholder.com/250x200.png?text=Foto+Pastry" alt="Pastry">
                <h3>Butter Croissant</h3>
                <p>Renyah di luar, lembut berlapis di dalam. Cocok untuk sarapan.</p>
            </div>
            <div class="produk-card">
                <img src="https://via.placeholder.com/250x200.png?text=Foto+Kopi+2" alt="Americano">
                <h3>Ice Americano</h3>
                <p>Kopi hitam dingin menyegarkan untuk menemanimu mengerjakan tugas.</p>
            </div>
        </div>
    </section>

<?php 
// Memanggil bagian bawah halaman (Footer, Modal Login, JS)
include 'include/footer.php'; 
?>