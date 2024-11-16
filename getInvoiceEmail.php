<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$connection = new mysqli("localhost", "root", "", "docmartbeta");

// Cek koneksi
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Cek session dan order_id
if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    die("Anda tidak memiliki akses atau Order ID tidak ditemukan.");
}

$orderId = $_GET['order_id'];

// Query untuk mendapatkan data order dan email
$sql = "SELECT orders.*, users.email, users.username 
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        WHERE orders.id = ? AND orders.user_id = ?";

$stmt = $connection->prepare($sql);
if (!$stmt) {
    die("Query preparation failed: " . $connection->error);
}

$stmt->bind_param("ii", $orderId, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Data pesanan tidak ditemukan.");
}

$order = $result->fetch_assoc(); // Simpan data order

// Query untuk detail produk
$sql_details = "SELECT p.name, oi.quantity, oi.price, (oi.quantity * oi.price) AS subtotal
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?";
                
$stmt_details = $connection->prepare($sql_details);
if (!$stmt_details) {
    die("Query preparation failed: " . $connection->error);
}

$stmt_details->bind_param("i", $orderId);
$stmt_details->execute();
$result_details = $stmt_details->get_result();

// Buat konten email
$emailBody = "
    <h2>Invoice Order #" . $order['id'] . "</h2>
    <p><strong>Username:</strong> " . $order['username'] . "</p>
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
            <th>Harga</th>
            <th>Subtotal</th>
        </tr>";

while ($item = $result_details->fetch_assoc()) {
    $emailBody .= "
        <tr>
            <td>" . $item['name'] . "</td>
            <td>" . $item['quantity'] . "</td>
            <td>Rp " . number_format($item['price'], 2, ',', '.') . "</td>
            <td>Rp " . number_format($item['subtotal'], 2, ',', '.') . "</td>
        </tr>";
}

$emailBody .= "</table>";

try {
    $mail = new PHPMailer(true);
    
    // Konfigurasi SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'inicimolz@gmail.com';
    $mail->Password   = 'kwenquoccpbiwraw';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // Set pengirim dan penerima
SMTP_USER=your_smtp_user
SMTP_PASS=your_smtp_password
 // Email penerima

    // Set konten email
    $mail->isHTML(true);
    $mail->Subject = 'Invoice Order #' . $order['id'];
    $mail->Body    = $emailBody;

    $mail->send();
    echo "Invoice berhasil dikirim ke " . htmlspecialchars($order['email']);
    
} catch (Exception $e) {
    echo "Gagal mengirim email. Error: {$mail->ErrorInfo}";
}

// Tutup statement dan koneksi
$stmt->close();
$stmt_details->close();
$connection->close();
?>
