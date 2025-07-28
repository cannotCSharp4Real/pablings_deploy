<?php
// Simple database connection check
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Check</h2>";

// Check if connection.php exists and can be loaded
if (!file_exists('connection.php')) {
    die("connection.php file not found");
}

// Try to include connection.php and see what happens
try {
    ob_start();
    include 'connection.php';
    $output = ob_get_clean();
    
    if (isset($pdo)) {
        echo "<p style='color: green;'><strong>SUCCESS!</strong> Database connection established.</p>";
        
        // Test a simple query
        try {
            $test_query = $pdo->query('SELECT 1 as test');
            $test_result = $test_query->fetch();
            
            if ($test_result['test'] == 1) {
                echo "<p style='color: green;'><strong>SUCCESS!</strong> Database query test passed.</p>";
            } else {
                echo "<p style='color: red;'><strong>ERROR!</strong> Database query test failed.</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'><strong>ERROR!</strong> Query test failed: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'><strong>ERROR!</strong> PDO object not created.</p>";
    }
    
    if (!empty($output)) {
        echo "<p><strong>Output from connection.php:</strong></p>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>EXCEPTION!</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Error $e) {
    echo "<p style='color: red;'><strong>FATAL ERROR!</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p><strong>Check completed.</strong></p>";
?> 