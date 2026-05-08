<?php 
// 1. Buka sesi yang sedang berjalan
session_start();

// 2. Kosongkan semua data di dalam sesi (nama, role, id_user, dll)
session_unset();

// 3. Hancurkan sesi sepenuhnya dari server
session_destroy();

// 4. Arahkan kembali ke halaman form login (Index.php)
header("location: Index.php");
exit;
?>