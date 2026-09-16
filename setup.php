<?php
// setup.php - Run this once to import the DB schema
$host = 'localhost';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create DB
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `chronoscent-elite`");
    $pdo->exec("USE `chronoscent-elite`");
    
    // Check if tables exist
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        die("Database and tables already exist. Please drop them first if you want to reinstall.");
    }

    // Read SQL file
    $sql = file_get_contents('database/chronoscent-elite.sql');
    if ($sql === false) {
        die("Could not read database/chronoscent-elite.sql file.");
    }
    
    // Execute SQL
    $pdo->exec($sql);
    echo "<h1>Database Setup Successful!</h1>";
    echo "<p>ChronoScent Elite database has been created and populated with 20 sample products and admin account.</p>";
    echo "<a href='index.php'>Go to Homepage</a> | <a href='admin/index.php'>Go to Admin Panel</a>";
    
} catch(PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>
