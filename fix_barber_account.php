<?php
// Script to fix the barber2@pablings.com account
require_once("connection.php");

try {
    $email = "barber2@pablings.com";
    $password = "barber123";
    $name = "Barber Two";
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    echo "<h2>Fixing barber account: $email</h2>";
    
    // Check if the account exists in webuser table
    $stmt = $pdo->prepare("SELECT * FROM webuser WHERE email = ?");
    $stmt->execute([$email]);
    $webuser_exists = $stmt->fetch();
    
    // Check if it exists in barber table
    $stmt = $pdo->prepare("SELECT * FROM barber WHERE docemail = ?");
    $stmt->execute([$email]);
    $barber_exists = $stmt->fetch();
    
    if ($webuser_exists) {
        echo "<p>✅ Account exists in webuser table</p>";
        
        if ($barber_exists) {
            echo "<p>✅ Account exists in barber table</p>";
            
            // Update the password to be hashed
            $stmt = $pdo->prepare("UPDATE barber SET docpassword = ? WHERE docemail = ?");
            $result = $stmt->execute([$hashed_password, $email]);
            
            if ($result) {
                echo "<p>✅ Password updated successfully with hash</p>";
                echo "<p><strong>Email:</strong> $email</p>";
                echo "<p><strong>Password:</strong> $password</p>";
                echo "<p><strong>Name:</strong> $name</p>";
                echo "<p><strong>Type:</strong> Barber</p>";
            } else {
                echo "<p>❌ Failed to update password</p>";
            }
        } else {
            echo "<p>❌ Account not found in barber table, creating new one...</p>";
            
            // Create new barber account
            $stmt = $pdo->prepare("INSERT INTO barber (docemail, docname, docpassword, specialties) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([$email, $name, $hashed_password, "Haircut, Beard Trim, Hair Styling"]);
            
            if ($result) {
                echo "<p>✅ New barber account created successfully</p>";
                echo "<p><strong>Email:</strong> $email</p>";
                echo "<p><strong>Password:</strong> $password</p>";
                echo "<p><strong>Name:</strong> $name</p>";
                echo "<p><strong>Type:</strong> Barber</p>";
            } else {
                echo "<p>❌ Failed to create barber account</p>";
            }
        }
    } else {
        echo "<p>❌ Account not found in webuser table</p>";
        
        if ($barber_exists) {
            echo "<p>✅ Account exists in barber table, creating webuser entry...</p>";
            
            // Create new webuser entry
            $stmt = $pdo->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'd')");
            $result = $stmt->execute([$email]);
            
            if ($result) {
                echo "<p>✅ Webuser entry created successfully</p>";
                
                // Update the password to be hashed
                $stmt = $pdo->prepare("UPDATE barber SET docpassword = ? WHERE docemail = ?");
                $result = $stmt->execute([$hashed_password, $email]);
                
                if ($result) {
                    echo "<p>✅ Password updated successfully with hash</p>";
                    echo "<p><strong>Email:</strong> $email</p>";
                    echo "<p><strong>Password:</strong> $password</p>";
                    echo "<p><strong>Name:</strong> $name</p>";
                    echo "<p><strong>Type:</strong> Barber</p>";
                } else {
                    echo "<p>❌ Failed to update password</p>";
                }
            } else {
                echo "<p>❌ Failed to create webuser entry</p>";
            }
        } else {
            echo "<p>❌ Account not found in barber table, creating new one...</p>";
            
            // Create new webuser entry
            $stmt = $pdo->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'd')");
            $result = $stmt->execute([$email]);
            
            if ($result) {
                echo "<p>✅ Webuser entry created</p>";
                
                // Create new barber account
                $stmt = $pdo->prepare("INSERT INTO barber (docemail, docname, docpassword, specialties) VALUES (?, ?, ?, ?)");
                $result = $stmt->execute([$email, $name, $hashed_password, "Haircut, Beard Trim, Hair Styling"]);
                
                if ($result) {
                    echo "<p>✅ New barber account created successfully</p>";
                    echo "<p><strong>Email:</strong> $email</p>";
                    echo "<p><strong>Password:</strong> $password</p>";
                    echo "<p><strong>Name:</strong> $name</p>";
                    echo "<p><strong>Type:</strong> Barber</p>";
                } else {
                    echo "<p>❌ Failed to create barber account</p>";
                }
            } else {
                echo "<p>❌ Failed to create webuser entry</p>";
            }
        }
    }
    
    echo "<p><a href='login.php'>Go to Login Page</a></p>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Database Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    
    // Check if it's a unique constraint violation
    if (strpos($e->getMessage(), '23505') !== false) {
        echo "<p><strong>Note:</strong> This error indicates the account already exists. The account should now be working properly.</p>";
        echo "<p><a href='login.php'>Try logging in now</a></p>";
    }
} catch (Exception $e) {
    echo "<h2>❌ General Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 