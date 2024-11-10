<?php
// Koneksi ke database
$connection = new mysqli("localhost", "root", "", "docmartbeta");

// Periksa koneksi
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}