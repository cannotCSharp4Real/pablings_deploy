<?php
require_once("connection.php");

// Barber account details
$email = "barber1@pablings.com";
$password = "barber123";
$name = "Barber 1";
$nic = "123456789";
$tel = "1234567890";
$specialties = 1; // Default specialty

try {
    // Check if barber already exists
    $stmt = $pdo->prepare("SELECT * FROM barber WHERE docemail = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        echo "Barber account already exists!";
        exit;
    }
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert into barber table
    $stmt = $pdo->prepare("INSERT INTO barber (docemail, docname, docpassword, docnic, doctel, specialties) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$email, $name, $hashed_password, $nic, $tel, $specialties]);
    
    // Insert into webuser table
    $stmt = $pdo->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'd')");
    $stmt->execute([$email]);
    
    echo "Barber account created successfully!<br>";
    echo "Email: " . $email . "<br>";
    echo "Password: " . $password . "<br>";
    echo "<a href='login.php'>Go to Login</a>";
    
} catch (PDOException $e) {
    echo "Error creating account: " . $e->getMessage();
}
?> 