<?php
// Database connection using full Database URL
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Method 1: Using Environment Variable (Recommended for production)
    $database_url = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');
    
    // Method 2: If environment variable not set, use the full URL directly
    if (!$database_url) {
        // Replace this with your actual External Database URL from Render dashboard
        $database_url = "postgresql://pablings_dp_jdd3_user:EDy75KM1w3BN7vbxxc1Par4i26N1ho9p@dpg-d1n4eper433s73bbh8g0-a.singapore-postgres.render.com/pablings_dp_jdd3";
    }
    
    // Remove debug info in production - comment out the line below for production
    echo "<!-- DEBUG: Connecting using database URL -->\n";
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_TIMEOUT => 30,
        PDO::ATTR_PERSISTENT => false
    ];
    
    // Create PDO connection using the full database URL
    $pdo = new PDO($database_url, null, null, $options);
    
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
