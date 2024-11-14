<?php
session_start();
include 'connect.php'; // Pastikan ini mengarah ke file koneksi database

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user']) || !is_array($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    die("User session is not set correctly.");
}

$userId = $_SESSION['user']['id'];

// Ambil data pesanan pengguna dari database
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $connection->prepare($sql);

if (!$stmt) {
    die("Query preparation failed: " . $connection->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query execution failed: " . $stmt->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Order</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center">Status Order</h1>
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>ID Order</th>
                <th>Username</th>
                <th>Tanggal Order</th>
                <th>Total Harga</th>
                <th>Metode Pembayaran</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Cek apakah ada data yang diambil
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$_SESSION['user']['username']}</td> <!-- Menggunakan username dari session -->
                            <td>{$row['order_date']}</td>
                            <td>Rp " . number_format($row['total_price'], 2, ',', '.') . "</td>
                            <td>{$row['payment_method']}</td>
                            <td>" . ucfirst($row['status']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>Tidak ada data order.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script> <!-- Pastikan ini ada di footer -->
</body>
</html>

<?php
$stmt->close();
$connection->close(); // Tutup koneksi database
?>