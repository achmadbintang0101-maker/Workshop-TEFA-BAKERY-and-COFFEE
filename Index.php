<?php 
// Memanggil bagian atas halaman (Header & CSS)
include 'include/header.php'; 
?>

<section id="home" class="hero-section">
  <div class="hero-card">
    <p class="eyebrow"></p>
    <h1>Awali Harimu dengan Roti Hangat &amp; Kopi Pilihan</h1>
    <p>Nikmati perpaduan sempurna dari TEFA Bakery &amp; Coffee.<br>Segar dari oven, setiap hari.</p>
    <!-- Tetap mengarah ke Customer -->
    <a href="Customer/Awal.php" class="btn-primary">Pesan Sekarang</a>
  </div>
</section>

<section id="produk" class="why-section">
  <div class="section-icon">
    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>
  </div>
  <h2>Menu Terfavorit</h2>
  <p class="subtitle">Kami berdedikasi untuk memberikan kualitas terbaik. Tanpa bahan pengawet, dibuat fresh setiap hari oleh artisan baker berpengalaman kami.</p>

  <div class="cards-grid">
    <div class="product-card">
      <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600&q=80" alt="Artisan Breads">
      <div class="card-body">
        <h3>Artisan Breads</h3>
        <p>Roti sourdough khas eropa dengan tekstur renyah di luar dan lembut di dalam.</p>
        <p class="card-price">Mulai Rp 35.000</p>
        <div class="card-actions">
          <a href="Customer/Awal.php" class="btn-filled">Pesan Sekarang</a>
        </div>
      </div>
    </div>

    <div class="product-card" style="box-shadow: 0 12px 40px rgba(107,63,34,.18);">
      <img src="https://images.unsplash.com/photo-1486427944299-d1955d23e34d?w=600&q=80" alt="Sweet Pastries">
      <div class="card-body">
        <h3>Sweet Pastries</h3>
        <p>Pilihan croissant, danish, dan tart manis dengan mentega pilihan.</p>
        <p class="card-price">Mulai Rp 22.000</p>
        <div class="card-actions">
          <a href="Customer/Awal.php" class="btn-filled">Pesan Sekarang</a>
        </div>
      </div>
    </div>

    <div class="product-card">
      <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&q=80" alt="Custom Cakes">
      <div class="card-body">
        <h3>Custom Cakes</h3>
        <p>Kue perayaan yang dirancang khusus sesuai dengan keinginan dan tema acara Anda.</p>
        <p class="card-price">Mulai Rp 250.000</p>
        <div class="card-actions">
          <a href="Customer/Awal.php" class="btn-filled">Pesan Sekarang</a>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="about" class="about-section">
  <div class="about-container">
    <div class="about-text">
      <h2>Tentang Kami</h2>
      <div class="divider"></div>
      <p>TEFA Bakery &amp; Coffee merupakan tempat yang menghadirkan pengalaman menikmati kopi dan roti dengan kualitas terbaik. Setiap produk kami diolah secara teliti dengan standar tinggi, menggunakan bahan-bahan pilihan agar menghasilkan cita rasa yang konsisten dan memuaskan.</p>
      <p>Selain itu, kami juga menawarkan program wisata edukatif bagi pengunjung yang ingin memahami lebih jauh proses pembuatan kopi dan roti secara profesional.</p>
      <h3>Sejarah Singkat</h3>
       <p>TEFA bermula dari Unit Pengolahan Roti yang didirikan pada tahun 2008. Seiring perkembangan, unit ini bertransformasi menjadi TEFA Coffee &amp; Bakery pada tahun 2017. Tidak hanya berfungsi sebagai sarana praktik dan penelitian, TEFA juga mampu menghasilkan berbagai produk dengan standar industri.</p>
      <p>Dengan menggabungkan cita rasa, kualitas, dan pengalaman, TEFA tidak sekadar menjadi unit bisnis, tetapi juga ruang untuk mengembangkan kreativitas serta meningkatkan kompetensi.</p>
    </div>

    <div class="about-image">
      <div class="box-grid">
        <div class="grid-item"><img src="Assets/IMG/Jurusan.png" alt="TEFA Bakery 1" onerror="this.src='https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80'"></div>
        <div class="grid-item"><img src="Assets/IMG/Jurusan.png" alt="TEFA Bakery 2" onerror="this.src='https://images.unsplash.com/photo-1486427944299-d1955d23e34d?w=400&q=80'"></div>
        <div class="grid-item"><img src="Assets/IMG/Jurusan.png" alt="TEFA Bakery 3" onerror="this.src='https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&q=80'"></div>
        <div class="grid-item"><img src="Assets/IMG/Jurusan.png" alt="TEFA Bakery 4" onerror="this.src='https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400&q=80'"></div>
      </div>
    </div>
  </div>
</section>

<?php 
// Memanggil bagian bawah halaman (Footer, Modal Login, JS)
include 'include/footer.php'; 
?>