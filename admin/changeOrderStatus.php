<?php
session_start();
include '../connect.php'; // Pastikan file koneksi sesuai dengan struktur Anda

// Cek jika ada ID dan status yang ingin diubah
if (isset($_GET['id']) && isset($_GET['status'])) {
    $order_id = $_GET['id'];
    $new_status = $_GET['status'];

    // Siapkan statement untuk mengupdate status order
    $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
    $update_stmt = $connection->prepare($update_sql);
    $update_stmt->bind_param("si", $new_status, $order_id);

    if ($update_stmt->execute()) {
        // Redirect kembali ke halaman manageOrders setelah berhasil diubah
        header("Location: manageOrders.php?message=Order status updated successfully.");
        exit();
    } else {
        echo "Error updating order status: " . htmlspecialchars($update_stmt->error);
    }

    $update_stmt->close();
} else {
    echo "Error: No order ID or status provided.";
}

// Tutup koneksi
$connection->close();
?>