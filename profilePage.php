<?php
session_start();
$connection = new mysqli("localhost", "root", "", "docmartbeta");

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: loginPage.php");
    exit();
}

$username = $_SESSION['user'];

// Fetch user information
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update profile information
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];

    // Update user information in the database
    $update_sql = "UPDATE users SET username = ?, email = ?, phone_number = ?, address = ? WHERE id = ?";
    $update_stmt = $connection->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $new_username, $email, $phone_number, $address, $user['id']);

    if ($update_stmt->execute()) {
        // Update session username if it was changed
        $_SESSION['user'] = $new_username;
        echo "<p>Profile updated successfully.</p>";
    } else {
        echo "<p>Error updating profile: " . $update_stmt->error . "</p>";
    }

    $update_stmt->close();
}

$stmt->close();
$connection->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Profile</h2>
    <form action="profilePage.php" method="POST">
        <div class="form-group">
            <label for="user_id">User ID</label>
            <input type="text" class="form-control" id="user_id" value="<?php echo $user['id']; ?>" readonly>
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo $user['username']; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Nomor Telepon</label>
            <input type="text" name="phone_number" class="form-control" value="<?php echo $user['phone_number']; ?>" required>
        </div>
        <div class="form-group">
            <label for="address">Alamat</label>
            <textarea name="address" class="form-control" required><?php echo $user['address']; ?></textarea>
        </div>
        <a href="homePage.php" class="btn btn-secondary ml-2">Back</a> <!-- Back to Homepage Button -->
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

</body>
</html>
