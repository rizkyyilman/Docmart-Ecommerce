<?php
session_start();
$connection = new mysqli("localhost", "root", "", "docmartbeta");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if (!isset($_SESSION['user']) || !isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: loginPage.php");
    exit();
}

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Retrieve the product ID from the URL
$product_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Tambahkan produk ke keranjang di sesi
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]++;
} else {
    $_SESSION['cart'][$product_id] = 1;
}

// Tambahkan produk ke keranjang di database
$username = $_SESSION['user'];
$sql = "INSERT INTO cart (username, product_id, quantity, user_id) VALUES (?, ?, 1, ?) ON DUPLICATE KEY UPDATE quantity = quantity + 1";
$stmt = $connection->prepare($sql);
$stmt->bind_param("sii", $username, $product_id, $user_id);

if ($stmt->execute()) {
    header("Location: cartProduct.php");
} else {
    echo "Error adding to cart: " . $connection->error;
}

$stmt->close();
$connection->close();
?>