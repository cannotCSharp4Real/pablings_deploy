<?php
// Get these values from your Render database dashboard
$host = "dpg-xxxxxxxxx-a.singapore-postgres.render.com";
$port = "5432";
$dbname = "your_database_name";
$username = "your_username";
$password = "your_password";

// For PostgreSQL, use PDO
try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
