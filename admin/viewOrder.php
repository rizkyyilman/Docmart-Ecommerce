<?php
session_start();
include '../connect.php'; // Pastikan file koneksi sesuai dengan struktur Anda

// Cek jika ada ID order yang ingin dilihat
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // Ambil detail order
    $order_sql = "SELECT * FROM orders WHERE id = ?";
    $order_stmt = $connection->prepare($order_sql);
    $order_stmt->bind_param("i", $order_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();

    if ($order_result->num_rows > 0) {
        $order = $order_result->fetch_assoc();

        // Ambil detail produk dari order_items
        $items_sql = "SELECT products.name, products.price, order_items.quantity 
                      FROM order_items 
                      JOIN products ON order_items.product_id = products.id 
                      WHERE order_items.order_id = ?";
        $items_stmt = $connection->prepare($items_sql);
        $items_stmt->bind_param("i", $order_id);
        $items_stmt->execute();
        $items_result = $items_stmt->get_result();

        ?>

        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <title>View Order</title>
            <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        </head>
        <body>

        <div class="container mt-5">
            <h2>Order Details - Order ID: <?php echo htmlspecialchars($order_id); ?></h2>

            <p><strong>User ID:</strong> <?php echo htmlspecialchars($order['user_id']); ?></p>
            <p><strong>Total Price:</strong> Rp <?php echo number_format($order['total_price'], 2, ',', '.'); ?></p>
            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>

            <h4>Ordered Products:</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $items_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>Rp <?php echo number_format($item['price'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <a href="manageOrders.php" class="btn btn-primary">Back to Manage Orders</a>

        </div>

        </body>
        </html>

        <?php
    } else {
        echo "Error: Order not found.";
    }

    // Tutup statement
    $items_stmt->close();
} else {
    echo "Error: No order ID provided.";
}

// Tutup koneksi
$connection->close();
?>