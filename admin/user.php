<?php 
$page = 'user'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tefa Bakery & Coffee – User</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../Assets/CSS/globaladmin.css" />
  <link rel="stylesheet" href="../Assets/CSS/user.css" />
</head>
<body>

<!-- ── SIDEBAR ── -->
<?php include '../include/sidebar.php'; ?>
<!-- ── MAIN ── -->
<div class="main">
  <div class="topbar">
    <h1>User</h1>
    <div class="topbar-right">
      <button class="bell-btn" onclick="toggleNotif()" style="position:relative">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/>
        </svg>
        <span class="bell-badge" id="bellBadge"></span>
      </button>
      <div class="user-chip">
        <div class="user-avatar">
          <svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
        Pak Dwiki
      </div>
    </div>
  </div>

  <div class="content-area">
    <div class="page-title">Profil Pengguna</div>

    <div class="form-grid">

      <!-- Nama Lengkap -->
      <div class="form-group">
        <label class="form-label">Nama Lengkap</label>
        <input class="form-input" id="namaLengkap" type="text" value="Dwiki Santoso" readonly />
      </div>

      <!-- Username -->
      <div class="form-group">
        <label class="form-label">Username</label>
        <input class="form-input" id="username" type="text" value="Pak Dwiki" readonly />
      </div>

      <!-- Email -->
      <div class="form-group">
        <label class="form-label">Email</label>
        <input class="form-input" id="email" type="email" value="dwiki@santoso.com" readonly />
      </div>

      <!-- Password -->
      <div class="form-group">
        <label class="form-label">Password</label>
        <div class="password-wrap">
          <input class="form-input" id="password" type="password" value="password123" readonly />
          <button class="btn-ubah" id="btnUbah" onclick="togglePassword()">Ubah</button>
        </div>
      </div>

      <!-- Jenis Kelamin -->
      <div class="form-group">
        <label class="form-label">Jenis Kelamin</label>
        <div class="select-wrap">
          <select id="jenisKelamin" disabled>
            <option value="Laki-Laki" selected>Laki-Laki</option>
            <option value="Perempuan">Perempuan</option>
          </select>
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <polyline points="6 9 12 15 18 9"/>
          </svg>
        </div>
      </div>

      <!-- Nomor Telepon -->
      <div class="form-group">
        <label class="form-label">Nomor Telepon</label>
        <input class="form-input" id="noTelepon" type="tel" value="+6281234567890" readonly />
      </div>

      <!-- Tanggal Lahir -->
      <div class="form-group">
        <label class="form-label">Tanggal Lahir</label>
        <div class="date-wrap">
          <input class="form-input" id="tanggalLahir" type="text" value="12-07-1995" placeholder="dd-mm-yyyy" readonly />
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </div>
      </div>

      <!-- Joined -->
      <div class="form-group">
        <label class="form-label">Joined</label>
        <input class="form-input" id="joined" type="text" value="01-02-2023" readonly />
      </div>

      <!-- Status Akun -->
      <div class="form-group">
        <label class="form-label">Status Akun</label>
        <div class="status-wrap">
          <span class="badge-aktif" id="statusBadge">Aktif</span>
        </div>
      </div>

    </div>

    <!-- Edit / Save -->
    <div class="save-row">
      <button class="btn-simpan" id="actionBtn" onclick="handleAction()">
        <svg id="actionIcon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        <span id="actionLabel">Edit</span>
      </button>
    </div>
  </div>
</div>

<!-- ── NOTIF PANEL ── -->
<div class="notif-overlay" id="notifOverlay" onclick="closeNotif()"></div>
<div class="notif-panel" id="notifPanel">
  <div class="notif-header">
    <span class="notif-title-text">Notifikasi</span>
    <button class="notif-close" onclick="closeNotif()"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
  </div>
  <div class="notif-list" id="notifList"></div>
  <div class="notif-footer"><span class="notif-mark-all" onclick="markAllRead()">Tandai Semua Dibaca</span></div>
</div>

<!-- ── OVERLAY / LOGOUT MODAL ── -->
<div class="overlay" id="overlay" onclick="if(event.target===this)closeModal()">
  <div class="modal" id="logoutModal" onclick="event.stopPropagation()">
    <div class="confirm-icon"><svg width="26" height="26" fill="none" stroke="#e8760a" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg></div>
    <div class="confirm-title">Log Out?</div>
    <div class="confirm-desc">Apakah kamu yakin ingin keluar dari akun <strong>Pak Dwiki</strong>?</div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-confirm" onclick="showToast('Berhasil log out!','info');closeModal()">Ya, Keluar</button>
    </div>
  </div>
</div>

<!-- Toast -->
<script src="../Assets/JS/user.js"></script>
</body>
</html>