<?php
// Memulai session
session_start();

// Koneksi ke database
$connection = new mysqli("localhost", "root", "", "docmartbeta");

// Periksa koneksi
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Variabel untuk menyimpan pesan error
$error = '';
$success = '';

// Proses registrasi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi jika password dan konfirmasi password sama
    if ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak sama!";
    } else {
        // Cek apakah username sudah digunakan
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Hash password sebelum menyimpan
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Simpan data pengguna ke database
            $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                $success = "Registrasi berhasil! Silakan <a href='loginPage.php'>login</a>.";
            } else {
                $error = "Terjadi kesalahan saat registrasi. Silakan coba lagi.";
            }
        }
    }
    $stmt->close();
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Docmart</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Registrasi Akun Baru</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form action="registerPage.php" method="POST">
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                <p class="mt-3 text-center">Sudah punya akun? <a href="loginPage.php">Login</a></p>
            </form>
        </div>
    </div>
</div>

</body>
</html>
