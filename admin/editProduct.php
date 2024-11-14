<?php
include 'includes/header.php';
include 'includes/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $connection->query("SELECT * FROM products WHERE id = $id");
    $product = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $product['image']; // Gambar lama

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photo = basename($_FILES['photo']['name']);
        $target_dir = "img/"; // Gunakan direktori yang konsisten
        $target_file = $target_dir . $photo;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                // Hapus gambar lama jika berhasil mengunggah gambar baru
                if ($product['image'] && file_exists("image/" . $product['image'])) {
                    unlink("image/" . $product['image']);
                }
                $image = $photo; // Update variabel $image dengan gambar baru
            } else {
                echo "Failed to upload image.";
                exit();
            }
        } else {
            echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
            exit();
        }
    }

    // Query untuk memperbarui data produk
    $stmt = $connection->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, image=? WHERE id=?");
    $stmt->bind_param("ssdisi", $name, $description, $price, $stock, $image, $id);
    $stmt->execute();

    header("Location: manageProducts.php");
    exit();
}
?>

<h2>Edit Product</h2>
<form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group">
        <label for="name">Product Name:</label>
        <input type="text" name="name" id="name" class="form-control" value="<?= $product['name'] ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <textarea name="description" id="description" class="form-control" required><?= $product['description'] ?></textarea>
    </div>
    <div class="form-group">
        <label for="price">Price:</label>
        <input type="number" name="price" id="price" class="form-control" value="<?= $product['price'] ?>" step="0.01" required>
    </div>
    <div class="form-group">
        <label for="stock">Stock:</label>
        <input type="number" name="stock" id="stock" class="form-control" value="<?= $product['stock'] ?>" required>
    </div>
    <div class="form-group">
        <label for="photo">Photo:</label>
        <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
        <p>Current Image: <img src="image/<?= $product['image'] ?>" width="50" height="50"></p>
    </div>
    <button type="submit" class="btn btn-primary">Update Product</button>
</form>

<?php include 'includes/footer.php'; ?>
