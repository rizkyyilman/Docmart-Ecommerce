<?php include 'includes/header.php'; ?>
<h2>Manage Products</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result = $connection->query("SELECT * FROM products");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['price']}</td>
                <td>
                    <a href='editProduct.php?id={$row['id']}' class='btn btn-sm btn-primary'>Edit</a>
                    <a href='deleteProduct.php?id={$row['id']}' class='btn btn-sm btn-danger'>Delete</a>
                </td>
            </tr>";
        }
        ?>
    </tbody>
</table>

<a href="addProduct.php" class="btn btn-success">Add Product</a>

<?php include 'includes/footer.php'; ?>
