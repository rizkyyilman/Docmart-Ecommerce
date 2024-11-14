<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: adminLogin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    
    <!-- Font Awesome (untuk ikon) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .dashboard-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }
        h2 {
            margin-bottom: 30px;
            font-weight: bold;
            color: #343a40;
            text-align: center;
        }
        .card {
            border: none;
            transition: transform 0.3s ease;
            text-align: center;
            position: relative;
            cursor: pointer;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.15);
        }
        .card i {
            font-size: 36px;
            margin-bottom: 10px;
            color: #007bff;
        }
        .stretched-link {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Admin Dashboard</h2>
        <div class="row text-center">
            <!-- Manage Users -->
            <div class="col-md-6 mb-4">
                <div class="card p-4">
                    <a href="manageUsers.php" class="stretched-link"></a>
                    <i class="fas fa-users"></i>
                    <h5 class="card-title">Manage Users</h5>
                </div>
            </div>

            <!-- Manage Products -->
            <div class="col-md-6 mb-4">
                <div class="card p-4">
                    <a href="manageProducts.php" class="stretched-link"></a>
                    <i class="fas fa-box-open"></i>
                    <h5 class="card-title">Manage Products</h5>
                </div>
            </div>

            <!-- Manage Orders -->
            <div class="col-md-6 mb-4">
                <div class="card p-4">
                    <a href="manageOrders.php" class="stretched-link"></a>
                    <i class="fas fa-file-invoice"></i>
                    <h5 class="card-title">Manage Orders</h5>
                </div>
            </div>

            <!-- Logout -->
            <div class="col-md-6 mb-4">
                <div class="card p-4">
                    <a href="logout.php" class="stretched-link"></a>
                    <i class="fas fa-sign-out-alt"></i>
                    <h5 class="card-title">Logout</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
