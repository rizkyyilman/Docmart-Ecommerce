<?php
session_start();
$connection = new mysqli("localhost", "root", "", "docmartbeta");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../loginPage.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$total_price = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;

if ($total_price < 0) {
    echo "Error: Total price cannot be negative.";
    exit();
}

// Check if user_id exists in users table
$user_check_sql = "SELECT id FROM users WHERE id = ?";
$user_check_stmt = $connection->prepare($user_check_sql);
$user_check_stmt->bind_param("i", $user_id);
$user_check_stmt->execute();
$user_check_stmt->store_result();

if ($user_check_stmt->num_rows == 0) {
    echo "Error: User does not exist in the database. User ID: " . $user_id;
    exit();
}
$user_check_stmt->close();

// Handle the payment process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    $payment_method = $_POST['payment_method'];
    $valid_payment_methods = ['Bank Transfer', 'Credit Card'];

    if (!in_array($payment_method, $valid_payment_methods)) {
        echo "Error: Invalid payment method.";
        exit();
    }

    $order_date = date("Y-m-d H:i:s");

    // Insert order into orders table
    $order_sql = "INSERT INTO orders (user_id, total_price, payment_method, order_date) VALUES (?, ?, ?, ?)";
    $order_stmt = $connection->prepare($order_sql);
    $order_stmt->bind_param("idss", $user_id, $total_price, $payment_method, $order_date);

    if ($order_stmt->execute()) {
        $order_id = $order_stmt->insert_id; // Get the last inserted order ID
        $order_stmt->close();

        // Insert each product in cart as an order item
        $cart_sql = "SELECT product_id FROM cart WHERE user_id = ?";
        $cart_stmt = $connection->prepare($cart_sql);
        $cart_stmt->bind_param("i", $user_id);
        $cart_stmt->execute();
        $cart_result = $cart_stmt->get_result();

        while ($row = $cart_result->fetch_assoc()) {
            $product_id = $row['product_id'];
            $order_item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, 1, (SELECT price FROM products WHERE id = ?))";
            $order_item_stmt = $connection->prepare($order_item_sql);
            $order_item_stmt->bind_param("iii", $order_id, $product_id, $product_id);

            if (!$order_item_stmt->execute()) {
                echo "Error inserting order item: " . $order_item_stmt->error;
                exit();
            }

            $order_item_stmt->close();
        }
        $cart_stmt->close();

        // Clear the cart after successful checkout
        $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
        $clear_cart_stmt = $connection->prepare($clear_cart_sql);
        $clear_cart_stmt->bind_param("i", $user_id);

        if (!$clear_cart_stmt->execute()) {
            echo "Error clearing cart: " . $clear_cart_stmt->error;
            exit();
        }
        $clear_cart_stmt->close();

        // Redirect to order success page
        header("Location: orderSuccess.php?order_id=" . $order_id);
        exit();
    } else {
        echo "Error inserting order: " . $order_stmt->error;
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Checkout</h2>
    <p>Total Harga: Rp <?php echo number_format($total_price, 2, ',', '.'); ?></p>
    <form method="POST" action="">
        <div class="form-group">
            <label for="payment_method">Metode Pembayaran:</label>
            <select name="payment_method" id="payment_method" class="form-control" required>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Credit Card">Credit Card</option>
            </select>
        </div>
        <input type="hidden" name="total_price" value="<?= $total_price ?>">
        <button type="submit" name="pay" class="btn btn-primary">Bayar</button>
    </form>
</div>

</body>
</html>
