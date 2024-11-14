<?php
session_start();
$connection = new mysqli("localhost", "root", "", "docmartbeta");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if user exists in the database
    $sql = "SELECT * FROM users WHERE username=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Save user_id and username in session
            $_SESSION['user_id'] = $user['id'];  // Simpan user ID
            $_SESSION['user'] = $user['username'];  // Simpan username
            header("Location: index.php");
            exit();
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Pengguna tidak ditemukan.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Set full page background to cyan */
        body {
            background-color: #00bcd4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Style the card */
        .card {
            width: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>

<div class="card">
    <div class="card-body">
        <h2 class="card-title text-center">Login</h2>
        <?php if (isset($error)) echo "<p style='color:red;' class='text-center'>$error</p>"; ?>
        <form method="POST" action="loginPage.php">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-3">Login</button>
        </form>
        <p class="text-center mt-3">Belum punya akun? <a href="registerPage.php">Daftar di sini</a></p>
    </div>
</div>

</body>
</html>
