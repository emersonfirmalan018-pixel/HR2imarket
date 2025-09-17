<?php
// Database connection settings
$servername = "localhost";   // Database server
$username   = "root";        // Default XAMPP username
$password   = "";            // Default XAMPP password
$dbname     = "imarket";     // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// Set UTF-8 encoding for all queries
if (!$conn->set_charset("utf8mb4")) {
    die("❌ Error setting UTF-8 charset: " . $conn->error);
}

// Optional: Debug mode (disable in production)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
?>
