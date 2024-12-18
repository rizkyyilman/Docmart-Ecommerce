<?php
// Koneksi ke database
session_start();
$connection = new mysqli("localhost", "root", "", "docmartbeta");

// Periksa koneksi
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Ambil kategori dari URL (jika ada)
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Query untuk mengambil data produk berdasarkan kategori
if ($category == 'alat_kesehatan') {
    $sql = "SELECT * FROM products WHERE name IN ('Stetoskop', 'Pulse Oximeter', 'Tensimeter')";
} elseif ($category == 'alat_penunjang_kehidupan') {
    $sql = "SELECT * FROM products WHERE name = 'Kursi Roda'";
} elseif ($category == 'alat_rumah_sakit') {
    $sql = "SELECT * FROM products WHERE name = 'Kasur Rumah Sakit'";
} else {
    $sql = "SELECT * FROM products"; // Ambil semua produk jika kategori tidak dikenali
}

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
    <style>
        .product-card {
            position: relative;
        }
        .btn-buy {
            position: absolute;
            bottom: 15px;
            right: 15px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="#">Docmart</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <?php if (isset($_SESSION['user'])): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Menu
                    </a>
                    <div class="dropdown-menu" aria-labelledby="profileDropdown">
                        <a class="dropdown-item" href="profilePage.php">Profile </a>
                        <a class="dropdown-item" href="transactionHistory.php">Transaction </a>
                        <a class="dropdown-item" href="logout.php">Logout </a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cartProduct.php">Keranjang</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="loginPage.php">Login</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<!-- Slogan Section -->
<div class="slogan-content" style="background-image: url('assets/img/doctorbanner.jpg'); background-size: cover; background-position: center;">            
    <div class="text-center">
        <h2>Selamat Datang di Docmart</h2>
        <p>Menyediakan Alat Kesehatan Berkualitas untuk Kesehatan Anda</p>
    </div>
</div>

<div class="container mt-5">
    <h1 class="text-center">Daftar Produk</h1>
    
    <!-- Kategori Section -->
    <div class="text-center mb-4">
        <button class="btn btn-secondary category-btn" data-category="all">Semua Produk</button>
        <button class="btn btn-secondary category-btn" data-category="alat_kesehatan">Alat Kesehatan</button>
        <button class="btn btn-secondary category-btn" data-category="alat_penunjang_kehidupan">Alat Penunjang Kehidupan</button>
        <button class="btn btn-secondary category-btn" data-category="alat_rumah_sakit">Alat Rumah Sakit</button>
    </div>

    <div class="row" id="product-list">
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
                echo '<a href="#" class="btn btn-success mt-2 btn-buy" data-id="' . $row['id'] . '">Beli</a>';
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

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
document.querySelectorAll('.category-btn').forEach(button => {
    button.addEventListener('click', function() {
        const category = this.getAttribute('data-category');
        const url = new URL(window.location.href);
        url.searchParams.set('category', category);
        window.location.href = url.toString(); // Reload halaman dengan kategori yang dipilih
    });
});

document.querySelectorAll('.btn-buy').forEach(button => {
    button.addEventListener('click', function(event) {
        event.preventDefault();
        const productId = this.getAttribute('data-id');

        // Konfirmasi sebelum menambahkan ke keranjang
        if (confirm('Apakah Anda yakin ingin menambahkan produk ini ke dalam keranjang?')) {
            fetch('addToCart.php?id=' + productId)
                .then(response => response.text())
                .then(data => {
                    alert('Produk berhasil ditambahkan ke keranjang!');
                    // Anda bisa menambahkan logika lain di sini, seperti memperbarui tampilan keranjang
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    });
});
</script>

</body>
</html>