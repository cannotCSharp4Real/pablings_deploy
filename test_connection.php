<?php
// Simple database connection test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test</h2>";

// Step 1: Check PHP version
echo "<p><strong>Step 1:</strong> PHP Version: " . phpversion() . "</p>";

// Step 2: Check available PDO drivers
echo "<p><strong>Step 2:</strong> Available PDO drivers: ";
$drivers = PDO::getAvailableDrivers();
if (empty($drivers)) {
    echo "None found!</p>";
} else {
    echo implode(', ', $drivers) . "</p>";
}

// Step 3: Check if PostgreSQL driver is available
echo "<p><strong>Step 3:</strong> PostgreSQL driver available: ";
if (in_array('pgsql', $drivers)) {
    echo "YES</p>";
} else {
    echo "NO - This is the problem!</p>";
    die("PostgreSQL PDO driver (pdo_pgsql) is not installed. Please install it on your server.");
}

// Step 4: Test connection parameters
echo "<p><strong>Step 4:</strong> Testing connection parameters...</p>";

$database_url = "postgresql://pablings_l11r_user:PhrHoLeTMyXpgsdxK5TKDIq61s8Y0Qw3@dpg-d23mh52dbo4c738ddt70-a.singapore-postgres.render.com/pablings_l11r";

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

$db_config = parseDatabaseUrl($database_url);

echo "<p>Host: " . $db_config['host'] . "</p>";
echo "<p>Port: " . $db_config['port'] . "</p>";
echo "<p>Database: " . $db_config['database'] . "</p>";
echo "<p>Username: " . $db_config['username'] . "</p>";
echo "<p>Password: [HIDDEN]</p>";

// Step 5: Test actual connection
echo "<p><strong>Step 5:</strong> Testing actual connection...</p>";

try {
    $dsn = "pgsql:host=" . $db_config['host'] . ";port=" . $db_config['port'] . ";dbname=" . $db_config['database'] . ";sslmode=require";
    
    echo "<p>DSN: " . $dsn . "</p>";
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 60,
    ];
    
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], $options);
    
    echo "<p style='color: green;'><strong>SUCCESS!</strong> Database connection established.</p>";
    
    // Test a simple query
    $test_query = $pdo->query('SELECT 1 as test');
    $test_result = $test_query->fetch();
    
    if ($test_result['test'] == 1) {
        echo "<p style='color: green;'><strong>SUCCESS!</strong> Database query test passed.</p>";
    } else {
        echo "<p style='color: red;'><strong>ERROR!</strong> Database query test failed.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>ERROR!</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>ERROR!</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p><strong>Test completed.</strong></p>";
?> 