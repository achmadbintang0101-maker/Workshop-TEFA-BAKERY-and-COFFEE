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

<?php
// Tangkap parameter error dan email dari URL (jika ada)
$error_type = isset($_GET['error']) ? $_GET['error'] : '';
$last_email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
?>

<div id="modalLogin" class="modal" <?= ($error_type != '') ? 'style="display: flex;"' : '' ?>>
    <div class="modal-box">
        <span class="close-btn" id="btnTutupLogin">&times;</span>
        <div class="modal-header">
            <span class="icon-coffee">☕</span>
            <h2>Staff Login</h2>
            <p>Masukkan email dan kata sandi Anda</p>
        </div>
        
        <form action="proses_login.php" method="POST">
            
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" value="<?= $last_email ?>" required 
                       <?= ($error_type == 'email') ? 'style="border-color: #d93025;"' : '' ?>>
                
                <?php if ($error_type == 'email'): ?>
                    <div style="color: #d93025; font-size: 0.85rem; margin-top: 6px; display: flex; align-items: center; gap: 5px; font-weight: 500;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#d93025"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        Email tidak terdaftar di sistem!
                    </div>
                <?php endif; ?>
            </div>

            <div class="input-group" style="margin-top: <?= ($error_type == 'email') ? '10px' : '15px' ?>;">
                <input type="password" name="password" placeholder="Password" required <?= ($error_type == 'sandi') ? 'autofocus' : '' ?>
                       <?= ($error_type == 'sandi') ? 'style="border-color: #d93025;"' : '' ?>>
                
                <?php if ($error_type == 'sandi'): ?>
                    <div style="color: #d93025; font-size: 0.85rem; margin-top: 6px; display: flex; align-items: center; gap: 5px; font-weight: 500;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#d93025"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        Sandi salah. Coba lagi.
                    </div>
                <?php endif; ?>
            </div>
            
            <button type="submit" name="login" class="btn-submit-login" style="margin-top: 25px;">MASUK KE SISTEM</button>
        </form>
    </div>
</div>

<script src="Assets/JS/landing.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnTutup = document.getElementById('btnTutupLogin');
        const modalLog = document.getElementById('modalLogin');
        
        if (btnTutup && modalLog) {
            btnTutup.addEventListener('click', () => {
                modalLog.style.display = 'none';
                // Menghilangkan ?error=... dari URL bar di browser tanpa me-refresh halaman
                const urlClean = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.replaceState({path: urlClean}, '', urlClean);
            });
        }
    });
</script>
</body>
</html>