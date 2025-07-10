<?php
// Database connection for Render PostgreSQL
// You should set these as environment variables in your Render service settings

// Use environment variables first, then fallback to hardcoded values
$host = getenv('DATABASE_HOST') ?: 'dpg-d1n4eper433s73bbh8g0-a';
$port = getenv('DATABASE_PORT') ?: '5432';
$dbname = getenv('DATABASE_NAME') ?: 'pablings_dp_jdd3';
$username = getenv('DATABASE_USER') ?: 'pablings_dp_jdd3_user';
$password = getenv('DATABASE_PASSWORD') ?: 'EDy75KM1w3BN7vbxxc1Par4i26N1ho9p';

// Alternative: Use Render's DATABASE_URL if available
if (getenv('DATABASE_URL')) {
    $db_url = parse_url(getenv('DATABASE_URL'));
    $host = $db_url['host'];
    $port = $db_url['port'];
    $dbname = ltrim($db_url['path'], '/');
    $username = $db_url['user'];
    $password = $db_url['pass'];
}

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
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_TIMEOUT => 30
    ]);
    
    // For backward compatibility, you can also create a mysqli-style connection variable
    // But we'll use PDO for better PostgreSQL support
    $conn = $pdo;
    $database = $pdo; // For compatibility with existing code
    
} catch(PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    // In production, don't expose sensitive error details
    if (getenv('APP_ENV') === 'development') {
        die("Connection failed: " . $e->getMessage());
    } else {
        die("Connection failed: Unable to connect to database. Please try again later.");
    }
}
?>
