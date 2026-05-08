<footer>
    <div class="footer-content">
        <div class="footer-info">
            <h3>Lokasi Kami</h3>
            <p>Gedung TEFA, Kampus Politeknik<br>Jl. Mastrip, Kabupaten Jember</p>
        </div>
        
        <div class="footer-info">
            <h3>Jam Operasional</h3>
            <p>Senin - Jumat: 08.00 - 16.00 WIB<br>Sabtu - Minggu: Tutup</p>
        </div>

        <div class="footer-info">
            <h3>Didukung Oleh</h3>
            <div class="footer-logos">
                <img src="Assets/IMG/Bennerpolije.png" alt="Politeknik Negeri Jember">
                <img src="Assets/IMG/Jurusan.png" alt="JTI">
                <img src="Assets/IMG/prodi.png" alt="Teknik Informatika">
                <img src="Assets/IMG/Tefalogo.png" alt="SIP TEFA">
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; 2026 Sistem Informasi Bread-Ordering dan Inventory Control TEFA Bakery & Coffee. All rights reserved.</p>
    </div>
</footer>

<!-- MODAL POPUP LOGIN UI BARU -->
<div id="modalLogin" class="modal">
    <div class="modal-box">
        <span class="close-btn" id="btnTutupLogin">&times;</span>
        <div class="modal-header">
            <span class="icon-coffee">☕</span>
            <h2>Staff Login</h2>
            <p>Masukkan email dan kata sandi Anda</p>
        </div>
        
        <form action="proses_login.php" method="POST">
            <!-- Bagian pemilihan role admin/kasir sudah dihapus untuk UX yang lebih modern -->

            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            
            <button type="submit" name="login" class="btn-submit-login">MASUK KE SISTEM</button>
        </form>
    </div>
</div>

<!-- Memanggil JavaScript dari folder Assets -->
<script src="Assets/JS/landing.js"></script>
</body>
</html>