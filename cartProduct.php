<?php
session_start();
$connection = new mysqli("localhost", "root", "", "docmartbeta");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if (!isset($_SESSION['user']) || !isset($_SESSION['user_id'])) {
    header("Location: loginPage.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle product removal from cart
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    $remove_sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    $remove_stmt = $connection->prepare($remove_sql);
    $remove_stmt->bind_param("ii", $user_id, $product_id);
    $remove_stmt->execute();
    $remove_stmt->close();
    header("Location: cartProduct.php"); // Refresh the page after removal
    exit();
}

// Fetch cart items for the logged-in user
$sql = "SELECT products.id, products.name, products.price, cart.quantity 
        FROM products 
        JOIN cart ON products.id = cart.product_id 
        WHERE cart.user_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0; // Variable to store the total price
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
    <h2>Keranjang Anda</h2>
    <?php if ($result->num_rows > 0): ?>
        <ul class="list-group mb-3">
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                $subtotal = $row['price'] * $row['quantity'];
                $total_price += $subtotal; // Add subtotal to total
                ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <?php echo $row['name']; ?> - Rp <?php echo number_format($row['price'], 2, ',', '.'); ?> x <?php echo $row['quantity']; ?>
                    </div>
                    <div>
                        <!-- Remove button -->
                        <a href="cartProduct.php?remove=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Remove</a>
                    </div>
                </li>
            <?php endwhile; ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>Total Harga Keseluruhan:</strong>
                <strong>Rp <?php echo number_format($total_price, 2, ',', '.'); ?></strong>
            </li>
        </ul>
        <form action="orders/checkout.php" method="POST">
            <input type="hidden" name="total_price" value="<?= $total_price ?>">
            <a href="index.php" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary">Checkout</button>
        </form>
    <?php else: ?>
        <p>Keranjang Anda kosong.</p>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    <?php endif; ?>
</div>

</body>
</html>
<?php
$stmt->close();
$connection->close();
?>