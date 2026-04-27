<?php 
// 1. Proteksi Halaman & Koneksi
include '../Config/auth.php'; 
include '../Config/koneksi.php';

// Menentukan halaman aktif untuk sidebar
$page = 'manajemen_staff';

// 2. Logika Tambah Staff
if(isset($_POST['simpan_staff'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    // Enkripsi Password (HASH)
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah email sudah terdaftar
    $cek_email = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($cek_email) > 0) {
        echo "<script>alert('Error: Email sudah digunakan staff lain!');</script>";
    } else {
        $query = "INSERT INTO users (username, nama, email, password, role) 
                  VALUES ('$username', '$nama', '$email', '$password_hash', '$role')";
        
        if(mysqli_query($conn, $query)) {
            echo "<script>alert('Staff baru berhasil didaftarkan!'); window.location='manajemen_staff.php';</script>";
        }
    }
}

// 3. Ambil Data Semua Staff untuk Tabel
$tampil_staff = mysqli_query($conn, "SELECT id_user, nama, email, role FROM users ORDER BY role ASC");
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
                <form action="" method="POST">
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
                               <a href="hapus_staff.php?id=<?= $row['id_user']; ?>" 
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