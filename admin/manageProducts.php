<?php
include 'includes/header.php';
include 'includes/db.php';
?>

<h2>Manage Products</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>

        <?php
        // Query untuk mendapatkan data produk
        $result = $connection->query("SELECT * FROM products");

        // Loop melalui hasil query
        while ($row = $result->fetch_assoc()) {
            // Path lengkap ke gambar
           $image_path = 'img/' . $row['image'];

            // Menampilkan produk di tabel
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['description']}</td>
                <td>{$row['price']}</td>
                <td>{$row['stock']}</td>";

            // Cek apakah file gambar ada di path yang ditentukan
            if (file_exists($image_path)) {
                echo "<td><img src='$image_path' width='50' height='50'></td>";
            } else {
                // Jika gambar tidak ditemukan, tampilkan placeholder atau pesan error
                echo "<td><img src='path/to/placeholder.png' width='50' height='50' alt='Image not found'></td>";
            }

            echo "<td>
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
