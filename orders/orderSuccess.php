<?php
session_start();

if (!isset($_SESSION['user']) || !isset($_GET['order_id'])) {
    header("Location: ../loginPage.php");
    exit();
}

$order_id = $_GET['order_id'];

// Koneksi ke database
$connection = new mysqli("localhost", "root", "", "docmartbeta");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Ambil informasi order dari database
$order_sql = "SELECT total_price, payment_method, order_date FROM orders WHERE id = ?";
$order_stmt = $connection->prepare($order_sql);
$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order_stmt->bind_result($total_price, $payment_method, $order_date);
$order_stmt->fetch();
$order_stmt->close();

if (!$total_price) {
    echo "Error: Order not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Order Berhasil</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Order Berhasil</h2>
    <p>Terima kasih telah berbelanja dengan kami. Berikut adalah detail order Anda:</p>
    <ul class="list-group mb-4">
        <li class="list-group-item">Order ID: <?= htmlspecialchars($order_id) ?></li>
        <li class="list-group-item">Total Harga: Rp <?= number_format($total_price, 2, ',', '.') ?></li>
        <li class="list-group-item">Metode Pembayaran: <?= htmlspecialchars($payment_method) ?></li>
        <li class="list-group-item">Tanggal Order: <?= htmlspecialchars($order_date) ?></li>
    </ul>
    <p>Silakan unduh faktur Anda:</p>
    <a href="generateInvoice.php?order_id=<?= $order_id ?>" class="btn btn-primary">Unduh Invoice PDF</a>
    <a href="../index.php" class="btn btn-secondary">Kembali ke Beranda</a>
</div>

</body>
</html>