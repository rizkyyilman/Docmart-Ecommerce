<?php
session_start();
$connection = new mysqli("localhost", "root", "", "docmartbeta");

// Check if product ID is provided
if (!isset($_GET['id'])) {
    echo "Produk tidak ditemukan.";
    exit();
}

// Get the product ID from the URL
$product_id = $_GET['id'];

// Fetch product details from the database
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Produk tidak ditemukan.";
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - <?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 50px;
        }
        .card {
            max-width: 600px;
            margin: auto;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="index.php">Docmart</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <?php if (isset($_SESSION['user'])): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                        Profile
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="profile.php">My Profile</a>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="loginPage.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="registerPage.php">Register</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="cartProduct.php">Keranjang</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="card">
        <img src="img/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
            <p class="card-text">Harga: Rp <?php echo number_format($product['price'], 2, ',', '.'); ?></p>
            <p class="card-text"><strong>Deskripsi:</strong></p>
            <p class="card-text"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
            <a href="#" onclick="confirmAddToCart(<?php echo $product['id']; ?>)" class="btn btn-success">Beli</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
// JavaScript function for confirmation
function confirmAddToCart(productId) {
    if (confirm("Apakah Anda yakin ingin menambahkan produk ini ke keranjang?")) {
        window.location.href = "addToCart.php?id=" + productId;
    }
}
</script>

</body>
</html>

<?php $connection->close(); ?>
