<?php
session_start();
$connection = new mysqli("localhost", "root", "", "docmartbeta");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Siapkan statement untuk mengambil data admin berdasarkan username
    $sql = "SELECT * FROM admin WHERE username=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Cek apakah ada hasil
    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        
        // Verifikasi password
        if ($password & $admin['password']) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin'] = $admin['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Username not found!";
    }
    
    $stmt->close();

}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>
        <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>