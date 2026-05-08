<?php
include '../Config/auth.php'; // Sekalian panggil perlindungan auth
include '../Classes/Database.php';
include '../Classes/Transaction.php';

// Proteksi Halaman
if(!isset($_SESSION['status']) || $_SESSION['role'] !== 'kasir'){
    header("Location: ../index.php");
    exit;
}

// Bangun Object
$database = new Database();
$conn = $database->getConnection();
$transObj = new Transaction($conn);

// Ambil ID dari URL
$transaction_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 1. Panggil Method Ambil Data Transaksi
$trans = $transObj->getTransactionById($transaction_id);

if(!$trans){
    echo "<script>alert('Data tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

// 2. Panggil Method Ambil Rincian Barang
$details = $transObj->getTransactionDetails($transaction_id);

// Format Waktu
$date = new DateTime($trans['created_at']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #<?= $transaction_id ?></title>
    <link rel="stylesheet" href="../Assets/CSS/struk.css">
</head>
<body>

<div class="wrapper">
    <div class="struk">
        <div class="struk-header">
            <h2>TEFA Bread & Coffee</h2>
            <p>Politeknik Negeri Jember</p>
        </div>

        <div class="info-row">
            <span>No. Transaksi :</span>
            <span>#TRX-<?= $transaction_id ?></span>
        </div>
        <div class="info-row">
            <span>Waktu :</span>
            <span><?= $date->format('d/m/Y H:i') ?></span>
        </div>
        <div class="info-row">
            <span>Kasir :</span>
            <span><?= htmlspecialchars($trans['nama_kasir']) ?></span>
        </div>

        <hr class="divider">

        <table class="struk-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align:center">Qty</th>
                    <th style="text-align:right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($details)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td style="text-align:center"><?= $row['qty'] ?>x</td>
                    <td style="text-align:right"><?= number_format($row['subtotal'], 0, ',', '.') ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="summary" style="margin-top: 15px;">
            <div class="summary-row">
                <span>Subtotal</span>
                <span>Rp <?= number_format($trans['total_price'], 0, ',', '.') ?></span>
            </div>
            <div class="summary-row">
                <span>PPN 10%</span>
                <span>Rp <?= number_format($trans['tax'], 0, ',', '.') ?></span>
            </div>
            <div class="summary-row total">
                <span>TOTAL</span>
                <span>Rp <?= number_format($trans['grand_total'], 0, ',', '.') ?></span>
            </div>
        </div>

        <div class="struk-footer">
            <p>✨ Terima kasih telah berkunjung! ✨</p>
            <p>Selamat menikmati ☕🍞</p>
        </div>
    </div>

    <div class="btn-group">
        <button class="btn btn-print" onclick="window.print()">🖨️ Cetak Struk</button>
        <button class="btn btn-back" onclick="window.location='index.php'">← Kembali</button>
    </div>
</div>

</body>
</html>