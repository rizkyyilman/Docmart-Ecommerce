<?php
session_start();
require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$connection = new mysqli("localhost", "root", "", "docmartbeta");

// Cek apakah pengguna sudah login dan order_id tersedia
if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    die("Anda tidak memiliki akses atau Order ID tidak ditemukan.");
}

$orderId = $_GET['order_id'];

// Ambil data pesanan dan pengguna
$sql = "SELECT orders.*, users.email
        FROM orders
        JOIN users ON orders.user_id = users.id
        WHERE orders.id = ? AND orders.user_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("ii", $orderId, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Data pesanan tidak ditemukan.");
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

// Mulai membuat isi email
$emailBody = "
    <h2>Invoice Order #" . $order['id'] . "</h2>
    <p><strong>Username:</strong> " . $_SESSION['user'] . "</p>
    <p><strong>Tanggal Order:</strong> " . $order['order_date'] . "</p>
    <p><strong>Total Harga:</strong> Rp " . number_format($order['total_price'], 2, ',', '.') . "</p>
    <p><strong>Metode Pembayaran:</strong> " . ucfirst($order['payment_method']) . "</p>
    <p><strong>Status:</strong> " . ucfirst($order['status']) . "</p>
    <br>
    <h4>Detail Pesanan</h4>
    <table border='1' cellpadding='10'>
        <tr>
            <th>Nama Produk</th>
            <th>Jumlah</th>
            <th>Harga per Produk</th>
            <th>Total Harga</th>
        </tr>";

while ($row = $result_details->fetch_assoc()) {
    $emailBody .= "
        <tr>
            <td>" . $row['name'] . "</td>
            <td>" . $row['quantity'] . "</td>
            <td>Rp " . number_format($row['price'], 2, ',', '.') . "</td>
            <td>Rp " . number_format($row['total_price'], 2, ',', '.') . "</td>
        </tr>";
}
$emailBody .= "</table>";

// Inisialisasi PHPMailer
$mail = new PHPMailer(true);

try {
    // Konfigurasi SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'inicimolz@gmail.com'; // ganti dengan email Anda
    $mail->Password   = 'kwenquoccpbiwraw'; // ganti dengan password email Anda
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // Set pengirim dan penerima email
    $mail->setFrom('inicimolz@gmail.com', 'DocMart');
    $mail->addAddress($order['email']);

    // Set konten email
    $mail->isHTML(true);
    $mail->Subject = 'Invoice Order #' . $order['id'];
    $mail->Body    = $emailBody;

    // Kirim email
    $mail->send();
    echo "Invoice berhasil dikirim ke " . htmlspecialchars($order['email']);
} catch (Exception $e) {
    echo "Gagal mengirim email. Error: {$mail->ErrorInfo}";
}

// Tutup koneksi
$stmt->close();
$stmt_details->close();
$connection->close();
?>
