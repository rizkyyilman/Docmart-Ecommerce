<?php
session_start();
$connection = new mysqli("localhost", "root", "", "docmartbeta");

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user']) || !isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header('Location: loginPage.php');
    exit();
}

if (!isset($_GET['order_id'])) {
    die("Order ID tidak ditemukan.");
}

$orderId = $_GET['order_id']; // Ambil order_id dari URL

// Ambil data pesanan berdasarkan order_id
$sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = $connection->prepare($sql);

if (!$stmt) {
    die("Query preparation failed: " . $connection->error);
}

$stmt->bind_param("ii", $orderId, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Invoice tidak ditemukan.");
}

$order = $result->fetch_assoc();

// Ambil detail produk dari pesanan
$sql_details = "SELECT p.name, oi.quantity, oi.price, (oi.quantity * oi.price) AS total_price
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?";
$stmt_details = $connection->prepare($sql_details);
$stmt_details->bind_param("i", $orderId);
$stmt_details->execute();
$result_details = $stmt_details->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Order #<?php echo $order['id']; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <!-- Tombol Kembali ke Daftar Pesanan -->
    <a href="transactionHistory.php" class="btn btn-secondary mb-3">Kembali ke Daftar Pesanan</a>

    <h1 class="text-center">Invoice Order #<?php echo $order['id']; ?></h1>
    <div class="card mt-4">
        <div class="card-body">
            <h4>Detail Order</h4>
            <p><strong>Username:</strong> <?php echo $_SESSION['user']; ?></p>
            <p><strong>Tanggal Order:</strong> <?php echo $order['order_date']; ?></p>
            <p><strong>Total Harga:</strong> Rp <?php echo number_format($order['total_price'], 2, ',', '.'); ?></p>
            <p><strong>Metode Pembayaran:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
            <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
        </div>
    </div>

    <h4 class="mt-4">Detail Pesanan</h4>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Harga per Produk</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Menampilkan detail produk yang dipesan
            if ($result_details && $result_details->num_rows > 0) {
                while ($row = $result_details->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['name']}</td>
                            <td>{$row['quantity']}</td>
                            <td>Rp " . number_format($row['price'], 2, ',', '.') . "</td>
                            <td>Rp " . number_format($row['total_price'], 2, ',', '.') . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='text-center'>Tidak ada detail produk.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$stmt_details->close();
$connection->close(); // Tutup koneksi database
?>
