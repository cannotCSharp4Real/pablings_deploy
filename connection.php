<?php
// Use environment variables for database connection (recommended)
$host = $_ENV['DATABASE_HOST'] ?? 'dpg-d1n3sv8dl3ps73ft5df0-a';
$port = $_ENV['DATABASE_PORT'] ?? '5432';
$dbname = $_ENV['DATABASE_NAME'] ?? 'pablings';
$username = $_ENV['DATABASE_USER'] ?? 'pablings_user';
$password = $_ENV['DATABASE_PASSWORD'] ?? '';

try {
    // Create PDO connection for PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password);
    
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // For backward compatibility, you can also create a mysqli-style connection variable
    // But we'll use PDO for better PostgreSQL support
    $conn = $pdo;
    $database = $pdo; // For compatibility with existing code
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
