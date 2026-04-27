<?php
session_start();
// Ubah dari $_SESSION['kasir'] menjadi $_SESSION['role'] == 'kasir'
if(!isset($_SESSION['status']) || $_SESSION['role'] !== 'kasir'){
    header("Location: ../index.php");
    exit;
}
include '../Config/koneksi.php';

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    header("Location: index.php");
    exit;
}

$transaction_id = (int)$_GET['id'];

$trans = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM transactions WHERE id = $transaction_id"
));

if(!$trans){
    header("Location: index.php");
    exit;
}

$details = mysqli_query($conn,
    "SELECT td.qty, td.price, td.subtotal, p.name
     FROM transaction_details td
     JOIN products p ON p.id = td.product_id
     WHERE td.transaction_id = $transaction_id"
);

// Format tanggal Indonesia
$created = new DateTime($trans['created_at']);
$created->setTimezone(new DateTimeZone('Asia/Jakarta'));

$hari  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$bulan = ['','Januari','Februari','Maret','April','Mei','Juni',
          'Juli','Agustus','September','Oktober','November','Desember'];

$namaHari  = $hari[(int)$created->format('w')];
$tgl       = $created->format('j');
$namaBulan = $bulan[(int)$created->format('n')];
$tahun     = $created->format('Y');
$jamWib    = $created->format('H:i:s');

$tanggalStruk = "$namaHari, $tgl $namaBulan $tahun";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Struk Pembayaran #<?= $transaction_id ?></title>
    <link rel="stylesheet" href="../Assets/CSS/struk.css">
</head>
<body>

<div class="wrapper">

    <!-- STRUK -->
    <div class="struk" id="struk">

        <div class="struk-header">
            <h2>☕ TEFA Bread & Coffee</h2>
            <p>Jl. Mastrip, Kec. Sumbersari<br>Politeknik Negeri Jember</p>

            <div class="datetime">
                <div class="tgl"><?= $tanggalStruk ?></div>
                <div class="jam"><?= $jamWib ?> WIB</div>
            </div>

            <div class="no-trans">No. Transaksi: #<?= str_pad($transaction_id, 5, '0', STR_PAD_LEFT) ?></div>
        </div>

       <div class="kasir-info">
    Kasir: <strong><?= htmlspecialchars($_SESSION['nama'] ?? 'Kasir') ?></strong>
</div>
        <hr class="divider">

        <!-- ITEM LIST -->
        <table class="item-list">
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align:center">Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($details)): ?>
                <tr>
                    <td>
                        <div class="item-name"><?= htmlspecialchars($row['name']) ?></div>
                        <div class="item-price">Rp <?= number_format($row['price'], 0, ',', '.') ?></div>
                    </td>
                    <td style="text-align:center"><?= $row['qty'] ?>x</td>
                    <td>Rp <?= number_format($row['subtotal'], 0, ',', '.') ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <hr class="divider">

        <!-- SUMMARY -->
<div class="summary">
    <div class="summary-row">
        <span>Subtotal</span>
        <span>Rp <?= number_format($trans['total'], 0, ',', '.') ?></span>
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
            <p>✨ Terima kasih telah berkunjung!</p>
            <p>Selamat menikmati ☕🍞</p>
        </div>

    </div>

    <!-- TOMBOL -->
    <div class="btn-group">
        <button class="btn btn-print" onclick="window.print()">🖨️ Cetak Struk</button>
        <button class="btn btn-back" onclick="window.location='index.php'">← Kembali</button>
    </div>

</div>

</body>
</html>
