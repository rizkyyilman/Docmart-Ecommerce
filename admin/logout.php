<?php
session_start();

// Hapus semua data sesi
session_unset();
session_destroy();

// Arahkan kembali ke halaman homePage.php
header("Location: ../homePage.php");
exit;
?>
