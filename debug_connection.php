<?php
// Debug database connection parameters
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Debug</h2>";

// Step 1: Check environment variables
echo "<p><strong>Step 1:</strong> Environment Variables</p>";
$database_url = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');
if ($database_url) {
    echo "<p>DATABASE_URL found: " . substr($database_url, 0, 50) . "...</p>";
} else {
    echo "<p>DATABASE_URL not found in environment</p>";
}

// Step 2: Parse the database URL
echo "<p><strong>Step 2:</strong> Parsed Connection Parameters</p>";
if ($database_url) {
    $parsed = parse_url($database_url);
    echo "<p>Host: " . ($parsed['host'] ?? 'NOT SET') . "</p>";
    echo "<p>Port: " . ($parsed['port'] ?? '5432') . "</p>";
    echo "<p>Database: " . (ltrim($parsed['path'] ?? '', '/') ?: 'NOT SET') . "</p>";
    echo "<p>Username: " . ($parsed['user'] ?? 'NOT SET') . "</p>";
    echo "<p>Password: " . (strlen($parsed['pass'] ?? '') > 0 ? 'SET' : 'NOT SET') . "</p>";
} else {
    echo "<p>No database URL to parse</p>";
}

// Step 3: Test direct connection with current parameters
echo "<p><strong>Step 3:</strong> Testing Direct Connection</p>";

if ($database_url) {
    $parsed = parse_url($database_url);
    $host = $parsed['host'] ?? 'localhost';
    $port = $parsed['port'] ?? 5432;
    $database = ltrim($parsed['path'] ?? '', '/');
    $username = $parsed['user'] ?? '';
    $password = $parsed['pass'] ?? '';
    
    if (empty($host) || empty($database) || empty($username)) {
        echo "<p style='color: red;'>ERROR: Missing required connection parameters</p>";
    } else {
        try {
            $dsn = "pgsql:host=$host;port=$port;dbname=$database;sslmode=require";
            echo "<p>DSN: $dsn</p>";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 60,
            ];
            
            $pdo = new PDO($dsn, $username, $password, $options);
            echo "<p style='color: green;'><strong>SUCCESS!</strong> Direct connection established.</p>";
            
            // Test query
            $test_query = $pdo->query('SELECT 1 as test');
            $test_result = $test_query->fetch();
            
            if ($test_result['test'] == 1) {
                echo "<p style='color: green;'><strong>SUCCESS!</strong> Query test passed.</p>";
            } else {
                echo "<p style='color: red;'><strong>ERROR!</strong> Query test failed.</p>";
            }
            
        } catch (PDOException $e) {
            echo "<p style='color: red;'><strong>PDO ERROR:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'><strong>GENERAL ERROR:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
} else {
    echo "<p style='color: red;'>ERROR: No DATABASE_URL environment variable found</p>";
}

echo "<p><strong>Debug completed.</strong></p>";
?> 