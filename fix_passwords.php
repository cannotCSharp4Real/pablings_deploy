<?php
require_once("connection.php");

try {
    // Fix admin password
    $admin_password = "123";
    $hashed_admin = password_hash($admin_password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE admin SET apassword = ? WHERE aemail = 'admin@edoc.com'");
    $stmt->execute([$hashed_admin]);
    echo "Admin password updated<br>";
    
    // Fix barber password
    $barber_password = "123";
    $hashed_barber = password_hash($barber_password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE barber SET docpassword = ? WHERE docemail = 'barber@edoc.com'");
    $stmt->execute([$hashed_barber]);
    echo "Barber password updated<br>";
    
    // Fix customer passwords
    $customer_password = "123";
    $hashed_customer = password_hash($customer_password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE customer SET ppassword = ? WHERE pemail = 'customer@edoc.com'");
    $stmt->execute([$hashed_customer]);
    echo "Customer password updated<br>";
    
    echo "<br>All passwords have been hashed!<br>";
    echo "<a href='login.php'>Go to Login</a>";
    
} catch (PDOException $e) {
    echo "Error updating passwords: " . $e->getMessage();
}
?> 