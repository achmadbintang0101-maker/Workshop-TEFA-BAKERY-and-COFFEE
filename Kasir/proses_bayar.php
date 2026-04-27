<?php
session_start();
// Ubah dari $_SESSION['kasir'] menjadi $_SESSION['role'] == 'kasir'
if(!isset($_SESSION['status']) || $_SESSION['role'] !== 'kasir'){
    header("Location: ../index.php");
    exit;
}
include '../Config/koneksi.php';
// Validasi input
if(empty($_POST['cartData'])){
    header("Location: index.php");
    exit;
}

$cart = json_decode($_POST['cartData'], true);

if(!$cart || count($cart) === 0){
    header("Location: index.php");
    exit;
}

$kasir    = $_SESSION['kasir'];
$subtotal = 0;

foreach($cart as $item){
    $subtotal += $item['price'] * $item['qty'];
}

$tax   = $subtotal * 0.10;
$total = $subtotal + $tax;

// Simpan ke tabel transactions (pakai prepared statement)
$stmt = mysqli_prepare($conn, 
    "INSERT INTO transactions (total, tax, grand_total) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ddd", $subtotal, $tax, $total);
mysqli_stmt_execute($stmt);
$transaction_id = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

// Simpan detail per item
foreach($cart as $item){
    $itemSubtotal = $item['price'] * $item['qty'];

    $stmt2 = mysqli_prepare($conn,
        "INSERT INTO transaction_details 
         (transaction_id, product_id, qty, price, subtotal) 
         VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt2, "iiidd",
        $transaction_id,
        $item['id'],
        $item['qty'],
        $item['price'],
        $itemSubtotal
    );
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);
}

// Redirect ke halaman struk
header("Location: struk.php?id=" . $transaction_id);
exit;
?>