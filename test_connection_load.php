<?php
// Test if connection.php can be loaded
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing connection.php file load</h2>";

// Step 1: Check if file exists
echo "<p><strong>Step 1:</strong> Checking if connection.php exists... ";
if (file_exists('connection.php')) {
    echo "YES</p>";
} else {
    echo "NO - File not found!</p>";
    die("connection.php file does not exist.");
}

// Step 2: Check file syntax
echo "<p><strong>Step 2:</strong> Checking PHP syntax... ";
$syntax_check = shell_exec('php -l connection.php 2>&1');
if (strpos($syntax_check, 'No syntax errors') !== false) {
    echo "OK - No syntax errors</p>";
} else {
    echo "ERROR - Syntax errors found</p>";
    echo "<pre>" . htmlspecialchars($syntax_check) . "</pre>";
    die("Syntax errors in connection.php");
}

// Step 3: Try to include the file
echo "<p><strong>Step 3:</strong> Trying to include connection.php... ";
try {
    ob_start();
    include 'connection.php';
    $output = ob_get_clean();
    
    if (isset($pdo)) {
        echo "SUCCESS - PDO object created</p>";
        echo "<p style='color: green;'><strong>SUCCESS!</strong> connection.php loaded successfully.</p>";
    } else {
        echo "FAILED - PDO object not created</p>";
        echo "<p style='color: red;'><strong>ERROR!</strong> PDO object not available after including connection.php</p>";
    }
    
    if (!empty($output)) {
        echo "<p><strong>Output from connection.php:</strong></p>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "FAILED - Exception thrown</p>";
    echo "<p style='color: red;'><strong>ERROR!</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Error $e) {
    echo "FAILED - Fatal error</p>";
    echo "<p style='color: red;'><strong>FATAL ERROR!</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p><strong>Test completed.</strong></p>";
?> 