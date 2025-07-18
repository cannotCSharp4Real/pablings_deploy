<?php
// Database connection with driver check and fallback
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
    echo "<!-- DEBUG: Available PDO drivers: " . implode(', ', $available_drivers) . " -->\n";
    
    // Check if PostgreSQL driver is available
    if (!in_array('pgsql', $available_drivers)) {
        throw new Exception("PostgreSQL PDO driver (pdo_pgsql) is not installed. Available drivers: " . implode(', ', $available_drivers));
    }
    
    // Get database URL
    $database_url = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');
    
    // Fallback URL if environment variable not set
    if (!$database_url) {
        $database_url = "postgresql://pablings_dp_jdd3_user:EDy75KM1w3BN7vbxxc1Par4i26N1ho9p@dpg-d1n4eper433s73bbh8g0-a.singapore-postgres.render.com/pablings_dp_jdd3";
    }
    
    // Parse the database URL
    $db_config = parseDatabaseUrl($database_url);
    
    // Build DSN string
    $dsn = sprintf(
        "pgsql:host=%s;port=%d;dbname=%s;sslmode=require",
        $db_config['host'],
        $db_config['port'],
        $db_config['database']
    );
    
    echo "<!-- DEBUG: Using DSN: " . str_replace($db_config['password'], '***', $dsn) . " -->\n";
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_TIMEOUT => 30,
        PDO::ATTR_PERSISTENT => false
    ];
    
    // Create PDO connection
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], $options);
    
    // Test the connection
    $pdo->query('SELECT 1');
    echo "<!-- DEBUG: Database connection successful -->\n";
    
    // For backward compatibility
    $conn = $pdo;
    $database = $pdo;
    
} catch(PDOException $e) {
    $error_message = $e->getMessage();
    error_log("Database connection error: " . $error_message);
    
    echo "<!-- DEBUG ERROR: " . htmlspecialchars($error_message) . " -->\n";
    
    // Provide specific error messages
    if (strpos($error_message, 'could not find driver') !== false) {
        die("Connection failed: PostgreSQL PDO driver is not installed. Please install php-pgsql extension.");
    } elseif (strpos($error_message, 'could not translate host name') !== false) {
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
