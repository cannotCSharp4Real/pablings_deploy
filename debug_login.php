<?php
require_once("connection.php");

$email = "barber1@pablings.com";

try {
    echo "<h3>Debugging Login for: " . $email . "</h3><br>";
    
    // Check webuser table
    echo "<h4>1. Checking webuser table:</h4>";
    $stmt = $pdo->prepare("SELECT * FROM webuser WHERE email = ?");
    $stmt->execute([$email]);
    $webuser = $stmt->fetch();
    
    if ($webuser) {
        echo "✓ Found in webuser table<br>";
        echo "Email: " . $webuser['email'] . "<br>";
        echo "User Type: " . $webuser['usertype'] . "<br>";
    } else {
        echo "✗ NOT found in webuser table<br>";
    }
    
    // Check barber table
    echo "<h4>2. Checking barber table:</h4>";
    $stmt = $pdo->prepare("SELECT * FROM barber WHERE docemail = ?");
    $stmt->execute([$email]);
    $barber = $stmt->fetch();
    
    if ($barber) {
        echo "✓ Found in barber table<br>";
        echo "Email: " . $barber['docemail'] . "<br>";
        echo "Name: " . $barber['docname'] . "<br>";
        echo "Password (first 20 chars): " . substr($barber['docpassword'], 0, 20) . "...<br>";
        echo "Password length: " . strlen($barber['docpassword']) . "<br>";
        
        // Test password verification
        $test_password = "barber123";
        if (password_verify($test_password, $barber['docpassword'])) {
            echo "✓ Password verification works!<br>";
        } else {
            echo "✗ Password verification failed!<br>";
        }
    } else {
        echo "✗ NOT found in barber table<br>";
    }
    
    echo "<br><a href='login.php'>Go to Login</a>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 