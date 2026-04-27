<?php
// Mengecek apakah variabel $page sudah didefinisikan di halaman utama.
if (!isset($page)) {
    $page = 'dashboard';
}
?>

<link rel="stylesheet" href="../Assets/CSS/sidebar.css">

<aside class="sidebar">
  <div class="sidebar-brand">Tefa Bakery<br>&amp; Coffee</div>
  <nav class="sidebar-nav">
    
    <a class="nav-item <?= ($page == 'dashboard') ? 'active' : '' ?>" href="dashboard.php">
      <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
        <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
      </svg>Dashboard
    </a>
    
    <a class="nav-item <?= ($page == 'order') ? 'active' : '' ?>" href="order.php">
      <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
      </svg>Order
    </a>
    
    <a class="nav-item <?= ($page == 'produksi') ? 'active' : '' ?>" href="produksi.php">
      <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/>
      </svg>Produksi
    </a>
    
    <a class="nav-item <?= ($page == 'inventory') ? 'active' : '' ?>" href="inventory.php">
      <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
      </svg>Inventory
    </a>
    
    <a class="nav-item <?= ($page == 'keuangan') ? 'active' : '' ?>" href="keuangan.php">
      <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
      </svg>Keuangan
    </a>
    
    <a class="nav-item <?= ($page == 'user') ? 'active' : '' ?>" href="user.php">
      <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
      </svg>Profil Saya
    </a>

    <a class="nav-item <?= ($page == 'manajemen_staff') ? 'active' : '' ?>" href="manajemen_staff.php">
      <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
      </svg>Manajemen Staff
    </a>
    
  </nav>

  <div class="sidebar-logout">
    <a href="../Index.php" class="logout-btn">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
      </svg>Log Out
    </a>
  </div>
</aside>