<?php 
$page = 'inventory';

// Panggil file OOP (auth.php sudah menjalankan session_start di dalamnya)
include '../Config/auth.php'; 
include '../Classes/Database.php';
include '../Classes/Product.php';

// Bangun Object (Instansiasi)
$database = new Database();
$conn = $database->getConnection();
$productObj = new Product($conn); 

// --- HANYA LOGIKA TAMPIL DATA YANG TERSISA DI SINI ---
$query_tampil = $productObj->getAllProducts();
$total_produk = mysqli_num_rows($query_tampil);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tefa Bakery & Coffee – Inventory Produk</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../Assets/CSS/globaladmin.css" />
  <link rel="stylesheet" href="../Assets/CSS/inventory.css" />
</head>
<body>

<?php include '../include/sidebar.php'; ?>

<div class="main">
  <div class="topbar">
    <h1>Inventory Produk</h1>
    <div class="topbar-right">
      <div class="user-chip">
        <div class="user-avatar"><svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
        <?= $_SESSION['nama'] ?? 'Admin'; ?>
      </div>
    </div>
  </div>

  <div class="content-area">
    <div class="action-row">
      <div>
        <span class="section-title">Manajemen Produk & Stok</span>
        <p style="font-size: 12px; color: var(--text-sub); margin-top: 4px;">Produk di sini akan otomatis muncul di menu Kasir.</p>
      </div>
      <div class="action-right">
        <button class="btn-input" onclick="openInputModal()">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Input Produk
        </button>
      </div>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>No.</th>
            <th>Gambar</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $no = 1;
          if ($total_produk > 0):
            while($p = mysqli_fetch_assoc($query_tampil)): 
              // Logika Status
              $status = "Aman";
              $class  = "badge-green";
              if($p['stok'] <= 0) { $status = "Habis"; $class = "badge-red"; }
              elseif($p['stok'] < 10) { $status = "Menipis"; $class = "badge-orange"; }
          ?>
          <tr>
            <td><?= str_pad($no++, 2, "0", STR_PAD_LEFT); ?></td>
            <td><img src="../Assets/IMG/<?= $p['image']; ?>" class="img-product-table"></td>
            <td style="font-weight:600"><?= htmlspecialchars($p['name']); ?></td>
            <!-- Tampilkan nama kategori dengan huruf kapital di awal -->
            <td><?= ucfirst($p['category']); ?></td>
            <td style="font-weight:600">Rp <?= number_format($p['price'], 0, ',', '.'); ?></td>
            <td style="font-weight:700"><?= $p['stok']; ?></td>
            <td><span class="badge <?= $class ?>"><?= $status ?></span></td>
           <td style="display: flex; gap: 5px;">
              <!-- PERBAIKAN: Tombol Edit (Kirimkan ID Kategori, Bukan Nama Kategori) -->
              <button class="btn-icon" style="color: #1e88e5;" onclick="bukaEditModal(<?= $p['id_product'] ?>, '<?= htmlspecialchars($p['name']) ?>', <?= $p['id_category'] ?>, <?= $p['price'] ?>, <?= $p['stok'] ?>)">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
              </button>
            
              <!-- Tombol Hapus -->
              <button class="btn-icon" style="color: #e53935;" onclick="if(confirm('Hapus produk ini?')) window.location='../Controllers/ProductController.php?action=delete&id=<?= $p['id_product'] ?>'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
              </button>
            </td>
          </tr>
          <?php endwhile; else: ?>
          <tr><td colspan="8" style="padding: 50px; color: #999;">Belum ada produk. Silakan tambah produk baru.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      <div class="pagination">
        <span class="page-info">Total Produk: <?= $total_produk ?></span>
      </div>
    </div>
  </div>
</div>

<!-- MODAL INPUT BARU -->
<div class="overlay" id="overlay">
  <form class="modal" id="inputModal" method="POST" action="../Controllers/ProductController.php" enctype="multipart/form-data" onclick="event.stopPropagation()">
    <div class="modal-header">
      <span class="modal-title">Input Produk Baru</span>
      <button type="button" class="modal-close" onclick="closeModal()">✕</button>
    </div>

    <div style="margin-bottom: 20px; text-align: center;">
      <div class="img-upload-box" onclick="document.getElementById('fGambar').click()">
        <img id="imgPreview" src="" style="display:none; width:100%; height:100%; object-fit:cover; border-radius:10px;">
        <div id="imgPlaceholder">
          <svg width="24" height="24" fill="none" stroke="#8a7060" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          <span style="display:block; font-size:11px; margin-top:5px; color:var(--text-sub)">Upload Foto</span>
        </div>
      </div>
      <input type="file" name="gambar" id="fGambar" hidden accept="image/*" onchange="previewImage(event)">
    </div>

    <div class="form-grid">
      <div class="form-group full">
        <label class="form-label">Nama Produk</label>
        <input class="form-input" name="nama" type="text" required placeholder="Roti Coklat" />
      </div>
      <div class="form-group">
        <label class="form-label">Kategori</label>
        <!-- PERBAIKAN: Value dropdown diganti menjadi angka (ID Kategori) -->
        <select class="form-select" name="kategori">
          <option value="1">Coffee</option>
          <option value="2">Bread</option>
          <option value="3">Snack</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Stok Awal</label>
        <input class="form-input" name="stok" type="number" required placeholder="0" />
      </div>
      <div class="form-group full">
        <label class="form-label">Harga Jual (Rp)</label>
        <input class="form-input" name="harga" type="text" onkeyup="formatHarga(this)" required placeholder="0" />
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
      <button type="submit" name="btnSimpan" class="btn-save">Simpan & Publikasikan</button>
    </div>
  </form>
</div>

<!-- MODAL EDIT DATA -->
<div class="overlay" id="overlayEdit" style="display: none;">
  <form class="modal" method="POST" action="../Controllers/ProductController.php" onclick="event.stopPropagation()">
    <div class="modal-header">
      <span class="modal-title">Edit Data Produk</span>
      <button type="button" class="modal-close" onclick="tutupEditModal()">✕</button>
    </div>

    <!-- Input tersembunyi untuk menyimpan ID Produk -->
    <input type="hidden" name="id_product" id="edit_id_product">

    <div class="form-grid">
      <div class="form-group full">
        <label class="form-label">Nama Produk</label>
        <input class="form-input" name="nama" id="edit_nama" type="text" required />
      </div>
      <div class="form-group">
        <label class="form-label">Kategori</label>
        <!-- PERBAIKAN: Value dropdown diganti menjadi angka (ID Kategori) -->
        <select class="form-select" name="kategori" id="edit_kategori">
          <option value="1">Coffee</option>
          <option value="2">Bread</option>
          <option value="3">Snack</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Stok (Update Baru)</label>
        <input class="form-input" name="stok" id="edit_stok" type="number" required />
      </div>
      <div class="form-group full">
        <label class="form-label">Harga Jual (Rp)</label>
        <!-- Menggunakan fungsi formatHarga milik Anda -->
        <input class="form-input" name="harga" id="edit_harga" type="text" onkeyup="formatHarga(this)" required />
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-cancel" onclick="tutupEditModal()">Batal</button>
      <button type="submit" name="btnUpdate" class="btn-save" style="background-color: #1e88e5;">Simpan Perubahan</button>
    </div>
  </form>
</div>

<script src="../Assets/JS/inventory.js"></script>
</body>
</html>