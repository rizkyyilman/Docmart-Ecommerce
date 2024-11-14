<?php include 'includes/header.php'; ?>
<h2>Manage Orders</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Total Price</th>
            <th>Order Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Mengambil data pesanan dari database
        $result = $connection->query("SELECT * FROM orders");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['user_id']}</td>
                <td>Rp " . number_format($row['total_price'], 2, ',', '.') . "</td>
                <td>{$row['order_date']}</td>
                <td>{$row['status']}</td>
                <td>
                    <a href='viewOrder.php?id={$row['id']}' class='btn btn-sm btn-info'>View</a>";

            // Tambahkan tombol untuk mengubah status jika statusnya 'Pending'
            if ($row['status'] === 'Pending') {
                echo "<a href='changeOrderStatus.php?id={$row['id']}&status=success' class='btn btn-sm btn-success'>Mark as Success</a>";
            }

            echo "</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; 
?>