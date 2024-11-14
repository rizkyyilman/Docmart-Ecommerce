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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order - Order ID: <?php echo htmlspecialchars($order_id); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 900px;
            margin-top: 50px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }
        table th, table td {
            text-align: center;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        /* Custom class for gray button */
        .btn-gray {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-gray:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center">Order Details - Order ID: <?php echo htmlspecialchars($order_id); ?></h2>

            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>User ID:</strong> <?php echo htmlspecialchars($order['user_id']); ?></p>
                    <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Total Price:</strong> Rp <?php echo number_format($order['total_price'], 2, ',', '.'); ?></p>
                    <p><strong>Status:</strong> <?php echo ucfirst(htmlspecialchars($order['status'])); ?></p>
                </div>
            </div>

            <h4 class="mt-4">Ordered Products:</h4>
            <table class="table table-bordered mt-3">
                <thead class="thead-dark">
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $items_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>Rp <?php echo number_format($item['price'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>Rp <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <a href="manageOrders.php" class="btn btn-gray">Back to Manage Orders</a>
        </div>
    </div>
</div>

</body>
</html>

        <?php
    } else {
        echo "<div class='container mt-5'><p class='alert alert-danger'>Error: Order not found.</p></div>";
    }

    // Tutup statement
    $items_stmt->close();
} else {
    echo "<div class='container mt-5'><p class='alert alert-danger'>Error: No order ID provided.</p></div>";
}

// Tutup koneksi
$connection->close();
?>

