<?php
session_start();

// Cek apakah admin sudah login
if (!isset ($_SESSION['admin'])) {
    header("Location: adminLogin.php");
    exit;
}

// Isi halaman dashboard di sini
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>
        <p>Selamat datang, admin!</p>
        <a href="manageUsers.php">Manage Users</a> |
        <a href="manageProducts.php">Manage Products</a> |
        <a href="manageOrders.php">Manage Orders</a> |
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
