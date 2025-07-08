<?php
// Get these values from your Render database dashboard
$host = "dpg-d1mh2hje5dus73bhlug0-a";
$port = "5432";
$dbname = "pablings_dp";
$username = "pablings_dp_user";
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
