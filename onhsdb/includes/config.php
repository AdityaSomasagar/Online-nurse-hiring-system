<?php
$host = "localhost";  // Change if your database is hosted elsewhere
$dbname = "onhsdb";  // Database name
$username = "root";  // MySQL username
$password = "";  // MySQL password (default for XAMPP is empty)

try {
    $dbh = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
