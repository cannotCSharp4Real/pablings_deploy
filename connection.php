<?php
// Use environment variables for database connection (recommended)
$host = $_ENV['DATABASE_HOST'] ?? 'dpg-d1n4eper433s73bbh8g0-a';
$port = $_ENV['DATABASE_PORT'] ?? '5432';
$dbname = $_ENV['DATABASE_NAME'] ?? 'pablings_dp_jdd3';
$username = $_ENV['DATABASE_USER'] ?? 'pablings_dp_jdd3_user';
$password = $_ENV['DATABASE_PASSWORD'] ?? 'EDy75KM1w3BN7vbxxc1Par4i26N1ho9p';

// Clean any potential invisible characters from the host
$host = trim($host);
$host = preg_replace('/[\x{200E}\x{200F}\x{202A}-\x{202E}]/u', '', $host);

try {
    // Create PDO connection for PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false
    ]);
    
    // For backward compatibility, you can also create a mysqli-style connection variable
    // But we'll use PDO for better PostgreSQL support
    $conn = $pdo;
    $database = $pdo; // For compatibility with existing code
    
} catch(PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Connection failed: Unable to connect to database. Please try again later.");
}
?>
