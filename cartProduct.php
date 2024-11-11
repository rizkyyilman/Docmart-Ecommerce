<?php
session_start();
$connection = new mysqli("localhost", "root", "", "docmartbeta");

if (!isset($_SESSION['user'])) {
    header("Location: loginPage.php");
    exit();
}

$username = $_SESSION['user'];

// Fetch cart items for the logged-in user
$sql = "SELECT products.* FROM products 
        JOIN cart ON products.id = cart.product_id 
        WHERE cart.username = '$username'";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Keranjang</h2>
    <?php if ($result->num_rows > 0): ?>
        <ul class="list-group">
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="list-group-item">
                    <?php echo $row['name']; ?> - Rp <?php echo number_format($row['price'], 2, ',', '.'); ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Keranjang Anda kosong.</p>
    <?php endif; ?>
</div>

</body>
</html>
