<?php
// Database connection with improved error handling and SSL configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to check available PDO drivers
function checkPDODrivers() {
    $drivers = PDO::getAvailableDrivers();
    return $drivers;
}

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
    // Check available PDO drivers
    $available_drivers = checkPDODrivers();
    
    // Check if PostgreSQL driver is available
    if (!in_array('pgsql', $available_drivers)) {
        throw new Exception("PostgreSQL PDO driver (pdo_pgsql) is not installed. Available drivers: " . implode(', ', $available_drivers));
    }
    
    // Get database URL from environment
    $database_url = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');
    
    // Updated fallback URL (make sure this is correct)
    if (!$database_url) {
        $database_url = "postgresql://pablings_l11r_user:PhrHoLeTMyXpgsdxK5TKDIq61s8Y0Qw3@dpg-d23mh52dbo4c738ddt70-a.singapore-postgres.render.com/pablings_l11r";
    }
    
    // Parse the database URL
    $db_config = parseDatabaseUrl($database_url);
    
    // Validate required configuration
    if (empty($db_config['host']) || empty($db_config['database']) || empty($db_config['username'])) {
        throw new Exception("Invalid database configuration. Missing required parameters.");
    }
    
    // Build DSN string with proper SSL configuration
    $dsn = sprintf(
        "pgsql:host=%s;port=%d;dbname=%s;sslmode=require;options='--client_encoding=UTF8'",
        $db_config['host'],
        $db_config['port'],
        $db_config['database']
    );
    
    // PDO options with improved configuration
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_TIMEOUT => 30,
        PDO::ATTR_PERSISTENT => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci", // This won't affect PostgreSQL but won't hurt
    ];
    
    // Create PDO connection with retry logic
    $max_retries = 3;
    $retry_delay = 1; // seconds
    $pdo = null;
    
    for ($i = 0; $i < $max_retries; $i++) {
        try {
            $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], $options);
            break; // Success, exit retry loop
        } catch (PDOException $e) {
            if ($i == $max_retries - 1) {
                throw $e; // Last attempt failed, throw the exception
            }
            sleep($retry_delay);
            $retry_delay *= 2; // Exponential backoff
        }
    }
    
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
    
    // Success message (only in development)
    // if (isset($_GET['debug']) || (defined('DEBUG') && DEBUG)) {
    //     echo "<!-- DEBUG: Database connection successful -->\n";
    // }
    
} catch(PDOException $e) {
    $error_message = $e->getMessage();
    error_log("Database connection error: " . $error_message);
    
    // Provide specific error messages without exposing sensitive information
    if (strpos($error_message, 'could not find driver') !== false) {
        $user_error = "Database driver not available. Please contact support.";
    } elseif (strpos($error_message, 'could not translate host name') !== false || 
              strpos($error_message, 'Name or service not known') !== false) {
        $user_error = "Database server is currently unavailable. Please try again later.";
    } elseif (strpos($error_message, 'password authentication failed') !== false) {
        $user_error = "Database authentication error. Please contact support.";
    } elseif (strpos($error_message, 'database') !== false && strpos($error_message, 'does not exist') !== false) {
        $user_error = "Database configuration error. Please contact support.";
    } elseif (strpos($error_message, 'Connection timed out') !== false || 
              strpos($error_message, 'timeout') !== false) {
        $user_error = "Database connection timeout. Please try again later.";
    } else {
        $user_error = "Database connection failed. Please try again later.";
    }
    
    // In development, show more details
    if (isset($_GET['debug']) || (defined('DEBUG') && DEBUG)) {
        die("Connection failed: " . htmlspecialchars($error_message));
    } else {
        die("Connection failed: " . $user_error);
    }
    
} catch(Exception $e) {
    error_log("General connection error: " . $e->getMessage());
    die("Connection failed: Database service unavailable. Please try again later.");
}
?>
