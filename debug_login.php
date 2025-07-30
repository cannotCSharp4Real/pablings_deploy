<?php
// Detailed debugging script for edward@gmail.com login
require_once("connection.php");

try {
    $email = "edward@gmail.com";
    $password = "edward123";
    
    echo "<h2>🔍 Debugging Login for: $email</h2>";
    
    // Step 1: Check webuser table
    echo "<h3>Step 1: Checking webuser table</h3>";
    $stmt = $pdo->prepare("SELECT * FROM webuser WHERE email = ?");
    $stmt->execute([$email]);
    $webuser = $stmt->fetch();
    
    if ($webuser) {
        echo "<p>✅ Found in webuser table</p>";
        echo "<p><strong>Email:</strong> " . $webuser['email'] . "</p>";
        echo "<p><strong>User Type:</strong> " . $webuser['usertype'] . "</p>";
        echo "<p><strong>ID:</strong> " . $webuser['id'] . "</p>";
    } else {
        echo "<p>❌ NOT found in webuser table</p>";
        die("Account not found in webuser table");
    }
    
    // Step 2: Check customer table (since usertype is 'p')
    echo "<h3>Step 2: Checking customer table</h3>";
    $stmt = $pdo->prepare("SELECT * FROM customer WHERE pemail = ?");
    $stmt->execute([$email]);
    $customer = $stmt->fetch();
    
    if ($customer) {
        echo "<p>✅ Found in customer table</p>";
        echo "<p><strong>Name:</strong> " . $customer['pname'] . "</p>";
        echo "<p><strong>Email:</strong> " . $customer['pemail'] . "</p>";
        echo "<p><strong>Password Hash:</strong> " . $customer['ppassword'] . "</p>";
        echo "<p><strong>Password Hash Length:</strong> " . strlen($customer['ppassword']) . "</p>";
        
        // Step 3: Test password verification
        echo "<h3>Step 3: Testing password verification</h3>";
        echo "<p><strong>Testing password:</strong> $password</p>";
        
        $verify_result = password_verify($password, $customer['ppassword']);
        echo "<p><strong>password_verify() result:</strong> " . ($verify_result ? "TRUE" : "FALSE") . "</p>";
        
        if ($verify_result) {
            echo "<p>✅ Password verification SUCCESSFUL</p>";
        } else {
            echo "<p>❌ Password verification FAILED</p>";
            
            // Step 4: Try to fix the password
            echo "<h3>Step 4: Fixing password</h3>";
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            echo "<p><strong>New hash:</strong> $new_hash</p>";
            
            $stmt = $pdo->prepare("UPDATE customer SET ppassword = ? WHERE pemail = ?");
            $update_result = $stmt->execute([$new_hash, $email]);
            
            if ($update_result) {
                echo "<p>✅ Password updated in database</p>";
                
                // Step 5: Test again
                echo "<h3>Step 5: Testing updated password</h3>";
                $stmt = $pdo->prepare("SELECT ppassword FROM customer WHERE pemail = ?");
                $stmt->execute([$email]);
                $updated_customer = $stmt->fetch();
                
                $verify_again = password_verify($password, $updated_customer['ppassword']);
                echo "<p><strong>Updated password verification:</strong> " . ($verify_again ? "TRUE" : "FALSE") . "</p>";
                
                if ($verify_again) {
                    echo "<p>✅ Password verification now SUCCESSFUL</p>";
                } else {
                    echo "<p>❌ Password verification still FAILED</p>";
                }
            } else {
                echo "<p>❌ Failed to update password</p>";
            }
        }
        
        // Step 6: Simulate login process
        echo "<h3>Step 6: Simulating login process</h3>";
        if ($webuser['usertype'] == 'p') {
            echo "<p>✅ User type is 'p' (customer)</p>";
            
            if ($customer && password_verify($password, $customer['ppassword'])) {
                echo "<p>✅ Login simulation SUCCESSFUL</p>";
                echo "<p>✅ Would redirect to: customer/index.php</p>";
                echo "<p>✅ Session variables would be set:</p>";
                echo "<ul>";
                echo "<li>user: $email</li>";
                echo "<li>usertype: p</li>";
                echo "<li>username: " . $customer['pname'] . "</li>";
                echo "</ul>";
            } else {
                echo "<p>❌ Login simulation FAILED</p>";
            }
        } else {
            echo "<p>❌ User type is not 'p' (it's '" . $webuser['usertype'] . "')</p>";
        }
        
    } else {
        echo "<p>❌ NOT found in customer table</p>";
    }
    
    echo "<hr>";
    echo "<h3>📋 Summary</h3>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Password:</strong> $password</p>";
    echo "<p><strong>User Type:</strong> " . $webuser['usertype'] . "</p>";
    echo "<p><strong>Name:</strong> " . ($customer ? $customer['pname'] : "Not found") . "</p>";
    
    echo "<p><a href='login.php'>Try Login Again</a></p>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Database Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<h2>❌ General Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 