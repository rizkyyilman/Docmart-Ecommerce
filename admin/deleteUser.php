<?php
session_start();
include '../connect.php'; // Pastikan file koneksi sesuai dengan struktur Anda

// Cek jika ada ID pengguna yang ingin dihapus
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Siapkan statement untuk menghapus pengguna
    $delete_sql = "DELETE FROM users WHERE id = ?";
    $delete_stmt = $connection->prepare($delete_sql);
    $delete_stmt->bind_param("i", $user_id);

    if ($delete_stmt->execute()) {
        // Redirect kembali ke halaman manageUsers setelah berhasil dihapus
        header("Location: manageUsers.php?message=User deleted successfully.");
        exit();
    } else {
        echo "Error deleting user: " . htmlspecialchars($delete_stmt->error);
    }

    $delete_stmt->close();
} else {
    echo "Error: No user ID provided.";
}

// Tutup koneksi
$connection->close();
?>