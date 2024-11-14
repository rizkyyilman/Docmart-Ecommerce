<?php
include 'includes/header.php';
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = '';

    // Pastikan untuk menggunakan "image" sebagai nama file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $photo = basename($_FILES['image']['name']);  // Ubah "photo" menjadi "image"
        $target_dir = "img/";
        $target_file = $target_dir . $photo;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $stmt = $connection->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdis", $name, $description, $price, $stock, $photo);
                if ($stmt->execute()) {
                    echo "Product added successfully!";
                } else {
                    echo "Error adding product: " . $stmt->error;
                }
            } else {
                echo "Failed to upload image.";
            }
        } else {
            echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    } else {
        echo "Image upload error: " . $_FILES['image']['error'];
    }
}
?>

<h2>Add New Product</h2>
<form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group">
        <label for="name">Product Name:</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <textarea name="description" id="description" class="form-control" required></textarea>
    </div>
    <div class="form-group">
        <label for="price">Price:</label>
        <input type="number" name="price" id="price" class="form-control" step="0.01" required>
    </div>
    <div class="form-group">
        <label for="stock">Stock:</label>
        <input type="number" name="stock" id="stock" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="image">Photo:</label>
        <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
    </div>
    <button type="submit" class="btn btn-success">Add Product</button>
</form>

<?php include 'includes/footer.php'; ?>
