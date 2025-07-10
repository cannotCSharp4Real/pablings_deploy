<?php
// Database connection with improved error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials - Use environment variables with fallbacks
$host = $_ENV['DATABASE_HOST'] ?? getenv('DATABASE_HOST') ?? 'dpg-d1n4eper433s73bbh8g0-a.oregon-postgres.render.com';
$port = $_ENV['DATABASE_PORT'] ?? getenv('DATABASE_PORT') ?? '5432';
$dbname = $_ENV['DATABASE_NAME'] ?? getenv('DATABASE_NAME') ?? 'pablings_dp_jdd3';
$username = $_ENV['DATABASE_USER'] ?? getenv('DATABASE_USER') ?? 'pablings_dp_jdd3_user';
$password = $_ENV['DATABASE_PASSWORD'] ?? getenv('DATABASE_PASSWORD') ?? 'EDy75KM1w3BN7vbxxc1Par4i26N1ho9p';

// Clean any potential invisible characters
$host = trim($host);
$host = preg_replace('/[\x{200E}\x{200F}\x{202A}-\x{202E}]/u', '', $host);

// Remove debug info in production - comment out the line below for production
echo "<!-- DEBUG: Attempting to connect to: $host:$port/$dbname with user: $username -->\n";

try {
    // Create PDO connection for PostgreSQL with SSL and proper timeout
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_TIMEOUT => 30,
        PDO::ATTR_PERSISTENT => false
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Test the connection
    $pdo->query('SELECT 1');
    echo "<!-- DEBUG: Database connection successful -->\n";
    
    // For backward compatibility
    $conn = $pdo;
    $database = $pdo;
    
} catch(PDOException $e) {
    $error_message = $e->getMessage();
    error_log("Database connection error: " . $error_message);
    
    // More detailed error for debugging - remove in production
    echo "<!-- DEBUG ERROR: " . htmlspecialchars($error_message) . " -->\n";
    
    // Check if it's a specific type of error
    if (strpos($error_message, 'could not translate host name') !== false) {
        die("Connection failed: DNS resolution error. The database host cannot be reached.");
    } elseif (strpos($error_message, 'password authentication failed') !== false) {
        die("Connection failed: Authentication error. Please check username and password.");
    } elseif (strpos($error_message, 'database') !== false && strpos($error_message, 'does not exist') !== false) {
        die("Connection failed: Database does not exist. Please check database name.");
    } elseif (strpos($error_message, 'Connection timed out') !== false) {
        die("Connection failed: Connection timeout. Please try again later.");
    } else {
        die("Connection failed: " . htmlspecialchars($error_message));
    }
} catch(Exception $e) {
    error_log("General connection error: " . $e->getMessage());
    die("Connection failed: " . htmlspecialchars($e->getMessage()));
}
?>
