<?php
// Script to check and fix the edward@gmail.com account
require_once("connection.php");

try {
    $email = "edward@gmail.com";
    $password = "edward123";
    
    echo "<h2>Checking account: $email</h2>";
    
    // Check if the account exists in webuser table
    $stmt = $pdo->prepare("SELECT * FROM webuser WHERE email = ?");
    $stmt->execute([$email]);
    $webuser_exists = $stmt->fetch();
    
    if ($webuser_exists) {
        echo "<p>✅ Account exists in webuser table</p>";
        echo "<p><strong>User Type:</strong> " . $webuser_exists['usertype'] . "</p>";
        
        $utype = $webuser_exists['usertype'];
        
        if ($utype == 'p') {
            // Check customer table
            $stmt = $pdo->prepare("SELECT * FROM customer WHERE pemail = ?");
            $stmt->execute([$email]);
            $customer = $stmt->fetch();
            
            if ($customer) {
                echo "<p>✅ Account exists in customer table</p>";
                echo "<p><strong>Name:</strong> " . $customer['pname'] . "</p>";
                echo "<p><strong>Password Hash:</strong> " . substr($customer['ppassword'], 0, 20) . "...</p>";
                
                // Test password verification
                if (password_verify($password, $customer['ppassword'])) {
                    echo "<p>✅ Password verification successful</p>";
                } else {
                    echo "<p>❌ Password verification failed - updating password</p>";
                    
                    // Update password with hash
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE customer SET ppassword = ? WHERE pemail = ?");
                    $result = $stmt->execute([$hashed_password, $email]);
                    
                    if ($result) {
                        echo "<p>✅ Password updated successfully</p>";
                    } else {
                        echo "<p>❌ Failed to update password</p>";
                    }
                }
            } else {
                echo "<p>❌ Account not found in customer table</p>";
            }
        } elseif ($utype == 'a') {
            // Check admin table
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE aemail = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();
            
            if ($admin) {
                echo "<p>✅ Account exists in admin table</p>";
                echo "<p><strong>Name:</strong> " . $admin['aname'] . "</p>";
                echo "<p><strong>Password Hash:</strong> " . substr($admin['apassword'], 0, 20) . "...</p>";
                
                // Test password verification
                if (password_verify($password, $admin['apassword'])) {
                    echo "<p>✅ Password verification successful</p>";
                } else {
                    echo "<p>❌ Password verification failed - updating password</p>";
                    
                    // Update password with hash
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE admin SET apassword = ? WHERE aemail = ?");
                    $result = $stmt->execute([$hashed_password, $email]);
                    
                    if ($result) {
                        echo "<p>✅ Password updated successfully</p>";
                    } else {
                        echo "<p>❌ Failed to update password</p>";
                    }
                }
            } else {
                echo "<p>❌ Account not found in admin table</p>";
            }
        } elseif ($utype == 'd') {
            // Check barber table
            $stmt = $pdo->prepare("SELECT * FROM barber WHERE docemail = ?");
            $stmt->execute([$email]);
            $barber = $stmt->fetch();
            
            if ($barber) {
                echo "<p>✅ Account exists in barber table</p>";
                echo "<p><strong>Name:</strong> " . $barber['docname'] . "</p>";
                echo "<p><strong>Password Hash:</strong> " . substr($barber['docpassword'], 0, 20) . "...</p>";
                
                // Test password verification
                if (password_verify($password, $barber['docpassword'])) {
                    echo "<p>✅ Password verification successful</p>";
                } else {
                    echo "<p>❌ Password verification failed - updating password</p>";
                    
                    // Update password with hash
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE barber SET docpassword = ? WHERE docemail = ?");
                    $result = $stmt->execute([$hashed_password, $email]);
                    
                    if ($result) {
                        echo "<p>✅ Password updated successfully</p>";
                    } else {
                        echo "<p>❌ Failed to update password</p>";
                    }
                }
            } else {
                echo "<p>❌ Account not found in barber table</p>";
            }
        }
    } else {
        echo "<p>❌ Account not found in webuser table</p>";
        echo "<p>Creating new customer account...</p>";
        
        // Create new customer account
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert into webuser table
        $stmt = $pdo->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'p')");
        $result = $stmt->execute([$email]);
        
        if ($result) {
            echo "<p>✅ Webuser entry created</p>";
            
            // Insert into customer table
            $stmt = $pdo->prepare("INSERT INTO customer (pemail, pname, ppassword, paddress, pdob, ptel) VALUES (?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([$email, "Edward", $hashed_password, "Sample Address", "1990-01-01", "1234567890"]);
            
            if ($result) {
                echo "<p>✅ Customer account created successfully</p>";
                echo "<p><strong>Email:</strong> $email</p>";
                echo "<p><strong>Password:</strong> $password</p>";
                echo "<p><strong>Name:</strong> Edward</p>";
                echo "<p><strong>Type:</strong> Customer</p>";
            } else {
                echo "<p>❌ Failed to create customer account</p>";
            }
        } else {
            echo "<p>❌ Failed to create webuser entry</p>";
        }
    }
    
    echo "<p><a href='login.php'>Go to Login Page</a></p>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Database Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<h2>❌ General Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 