<?php
// Database connection with improved error handling and SSL configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to parse database URL
function parseDatabaseUrl($url) {
    $parsed = parse_url($url);
    return [
        'host' => $parsed['host'] ?? 'localhost',
        'port' => $parsed['port'] ?? 5432,
        'database' => ltrim($parsed['path'] ?? '', '/'),
        'username' => $parsed['user'] ?? '',
        'password' => $parsed['pass'] ?? ''
    ];
}

try {
    // Get database URL from environment
    $database_url = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');
    
    // Updated fallback URL with new credentials
    if (!$database_url) {
        $database_url = "postgresql://pablings_l11r_user:PhrHoLeTMyXpgsdxK5TKDIq61s8Y0Qw3@dpg-d23mh52dbo4c738ddt70-a.singapore-postgres.render.com/pablings_111r";
    }
    
    // Parse the database URL
    $db_config = parseDatabaseUrl($database_url);
    
    // Validate required configuration
    if (empty($db_config['host']) || empty($db_config['database']) || empty($db_config['username'])) {
        throw new Exception("Invalid database configuration. Missing required parameters.");
    }
    
    // Build DSN string with proper SSL configuration for Render
    $dsn = sprintf(
        "pgsql:host=%s;port=%d;dbname=%s;sslmode=require",
        $db_config['host'],
        $db_config['port'],
        $db_config['database']
    );
    
    // PDO options optimized for PostgreSQL
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_TIMEOUT => 60, // Increased timeout for external connections
        PDO::ATTR_PERSISTENT => false,
    ];
    
    // Create PDO connection
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], $options);
    
    // Test the connection
    $test_query = $pdo->query('SELECT 1 as test');
    $test_result = $test_query->fetch();
    
    if ($test_result['test'] != 1) {
        throw new Exception("Database connection test failed");
    }
    
    // Set timezone for PostgreSQL
    $pdo->exec("SET timezone = 'Asia/Manila'");
    
    // For backward compatibility
    $conn = $pdo;
    $database = $pdo;
    
} catch(PDOException $e) {
    $error_message = $e->getMessage();
    error_log("Database connection error: " . $error_message);
    
    // Enable debug mode to see actual error
    $debug_mode = isset($_GET['debug']) || (defined('DEBUG') && DEBUG);
    
    // In debug mode, show the actual error immediately
    if ($debug_mode) {
        die("DEBUG - Actual Error: " . htmlspecialchars($error_message));
    }
    
    die("Connection failed: Database service unavailable. Please try again later.");
    
} catch(Exception $e) {
    error_log("General connection error: " . $e->getMessage());
    die("Connection failed: Database service unavailable. Please try again later.");
}
?>
