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
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Sistem Informasi Bread-Ordering dan Inventory Control TEFA Bakery & Coffee. All rights reserved.</p>
        </div>
    </footer>

    <div id="modalLogin" class="modal">
        <div class="modal-box">
            <span class="close-btn" id="btnTutupLogin">&times;</span>
            <div class="modal-header">
                <span class="icon-coffee">☕</span>
                <h2>Staff Login</h2>
                <p>Pilih peran Anda untuk masuk ke sistem</p>
            </div>
            
            <form action="proses_login.php" method="POST">
                <div class="role-selector">
                    <label class="role-option">
                        <input type="radio" name="role" value="admin" checked>
                        <div class="role-card">
                            <span class="role-icon">👑</span>
                            <span class="role-title">Admin</span>
                            <span class="role-desc">Manajemen Penuh</span>
                        </div>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role" value="kasir">
                        <div class="role-card">
                            <span class="role-icon">🧾</span>
                            <span class="role-title">Kasir</span>
                            <span class="role-desc">Transaksi & Pesanan</span>
                        </div>
                    </label>
                </div>

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

    <script src="Assets/JS/landing.js"></script>
</body>
</html>