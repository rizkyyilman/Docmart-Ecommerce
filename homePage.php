<?php
// Koneksi ke database
session_start();
$connection = new mysqli("localhost", "root", "", "docmartbeta");

// Periksa koneksi
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Query untuk mengambil data produk
$sql = "SELECT * FROM products";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Alat Kesehatan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="#">Docmart</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <?php if (isset($_SESSION['user'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cartProduct.php">Keranjang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="loginPage.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="registerPage.php">Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cartProduct.php">Keranjang</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<!-- Slogan Section -->
<div class="slogan-section">
    <div class="slogan-content">
        <h2>Selamat Datang di Docmart</h2>
        <p>Menyediakan Alat Kesehatan Berkualitas untuk Kesehatan Anda</p>
    </div>
</div>


<div class="container mt-5">
    <h1 class="text-center">Daftar Produk</h1>
    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="col-md-4 product-card">';
                echo '<div class="card">';
                echo '<img src="img/' . $row['image'] . '" class="card-img-top" alt="' . $row['name'] . '">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $row['name'] . '</h5>';
                echo '<p class="card-text">Harga: Rp ' . number_format($row['price'], 2, ',', '.') . '</p>';
                echo '<a href="detailProduct.php?id=' . $row['id'] . '" class="btn btn-primary">Detail</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "<p>Tidak ada produk ditemukan.</p>";
        }
        $connection->close();
        ?>
    </div>
</div>

<footer class="text-center mt-5">
    <p>&copy; 2024 Docmart. Semua hak dilindungi.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5