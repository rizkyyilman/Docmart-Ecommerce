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
        $result = $connection->query("SELECT * FROM orders");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['user_id']}</td>
                <td>{$row['total_price']}</td>
                <td>{$row['order_date']}</td>
                <td>{$row['status']}</td>
                <td>
                    <a href='viewOrder.php?id={$row['id']}' class='btn btn-sm btn-info'>View</a>
                </td>
            </tr>";
        }
        ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>
