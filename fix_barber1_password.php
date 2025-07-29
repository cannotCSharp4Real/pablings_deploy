<?php
require_once("connection.php");

$email = "barber1@pablings.com";
$password = "barber123";

try {
    // Check if the account exists
    $stmt = $pdo->prepare("SELECT * FROM barber WHERE docemail = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() == 0) {
        echo "Account not found. Creating new account...<br>";
        
        // Create new account
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $name = "Barber 1";
        $nic = "123456789";
        $tel = "1234567890";
        $specialties = 1;
        
        $stmt = $pdo->prepare("INSERT INTO barber (docemail, docname, docpassword, docnic, doctel, specialties) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$email, $name, $hashed_password, $nic, $tel, $specialties]);
        
        // Add to webuser table
        $stmt = $pdo->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'd')");
        $stmt->execute([$email]);
        
        echo "New barber account created successfully!<br>";
    } else {
        echo "Account exists. Updating password...<br>";
        
        // Update existing account password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE barber SET docpassword = ? WHERE docemail = ?");
        $stmt->execute([$hashed_password, $email]);
        
        // Ensure webuser entry exists
        $stmt = $pdo->prepare("SELECT * FROM webuser WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() == 0) {
            $stmt = $pdo->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'd')");
            $stmt->execute([$email]);
            echo "Added to webuser table<br>";
        }
        
        echo "Password updated successfully!<br>";
    }
    
    echo "<br>Account details:<br>";
    echo "Email: " . $email . "<br>";
    echo "Password: " . $password . "<br>";
    echo "<a href='login.php'>Go to Login</a>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 