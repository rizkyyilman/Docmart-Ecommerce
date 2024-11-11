<?php
session_start();
$connection = new mysqli("localhost", "root", "", "docmartbeta");

if (!isset($_SESSION['user'])) {
    // If not logged in, redirect to login page
    header("Location: loginPage.php");
    exit();
}

// Retrieve the product ID from the URL
$product_id = $_GET['id'];

// Add product to cart in the database
$username = $_SESSION['user'];
$sql = "INSERT INTO cart (username, product_id) VALUES ('$username', '$product_id')";
if ($connection->query($sql) === TRUE) {
    header("Location: cartProduct.php");
} else {
    echo "Error adding to cart: " . $connection->error;
}
?>
