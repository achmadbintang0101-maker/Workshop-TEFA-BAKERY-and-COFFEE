<?php 
// 1. Proteksi Halaman & Koneksi
$page = 'manajemen_staff';
include '../Config/auth.php'; 
include '../Classes/Database.php';
include '../Classes/User.php';

// 2. Bangun Object (Instansiasi)
$database = new Database();
$conn = $database->getConnection();
$userObj = new User($conn); 

// 3. Ambil Data Semua Staff untuk Tabel
$tampil_staff = $userObj->getAllUsers();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Staff - TEFA Bakery</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../Assets/CSS/globaladmin.css">
    <link rel="stylesheet" href="../Assets/CSS/manajemen.css">
</head>
<body>

    <?php include '../include/sidebar.php'; ?>

    <div class="main">
        <div class="container">
            <h2 style="color: #5C3D2E; margin-bottom: 25px;">Manajemen Data Staff</h2>

            <div class="card">
                <h3>+ Tambah Staff Baru</h3>
                <p style="font-size: 0.9rem; color: #666; margin-bottom: 20px;">Daftarkan akun Admin atau Kasir baru di sini.</p>
                <form action="../Controllers/UserController.php" method="POST">
                    <div class="form-row">
                        <div class="input-group">
                            <label>Username</label>
                            <input type="text" name="username" placeholder="cth: bintang_dev" required>
                        </div>
                        <div class="input-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" placeholder="Nama asli pegawai" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="input-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="email@gmail.com" required>
                        </div>
                        <div class="input-group">
                            <label>Password Awal</label>
                            <input type="password" name="password" placeholder="Min. 6 karakter" required>
                        </div>
                        <div class="input-group">
                            <label>Role / Jabatan</label>
                            <select name="role" required>
                                <option value="kasir">Kasir (Transaksi)</option>
                                <option value="admin">Admin (Manajemen)</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="simpan_staff" class="btn-add">Simpan Data Pegawai</button>
                </form>
            </div>

            <div class="card">
                <h3>Daftar Staff Terdaftar</h3>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($row = mysqli_fetch_assoc($tampil_staff)) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><strong><?= $row['nama']; ?></strong></td>
                            <td><?= $row['email']; ?></td>
                            <td>
                                <span class="badge <?= $row['role'] == 'admin' ? 'badge-admin' : 'badge-kasir'; ?>">
                                    <?= strtoupper($row['role']); ?>
                                </span>
                            </td>
                            <td>
                              <a href="../Controllers/UserController.php?action=delete&id=<?= $row['id_user']; ?>" 
   class="btn-delete" 
   onclick="return confirm('Yakin ingin menghapus staff ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>