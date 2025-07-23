<?php
// Include the database connection
require_once("connection.php");

try {
    // Check if connection was successful
    if (!isset($pdo)) {
        die("Database connection failed. Please check your database configuration.");
    }

    echo "<h1>🔧 Simple Barber Account Creation</h1>";
    echo "<hr>";

    // Barber account details
    $barber_email = "barber1@pablings.com";
    $barber_password = password_hash("barber123", PASSWORD_DEFAULT);
    $barber_name = "John Smith";

    // Start transaction
    $pdo->beginTransaction();

    // First, let's check what the actual barber table structure is
    echo "<h2>🔍 Checking table structure...</h2>";
    
    try {
        // Try a simple select to see what columns exist
        $stmt = $pdo->prepare("SELECT * FROM barber LIMIT 0");
        $stmt->execute();
        $columns = [];
        for ($i = 0; $i < $stmt->columnCount(); $i++) {
            $col = $stmt->getColumnMeta($i);
            $columns[] = $col['name'];
        }
        
        echo "<p>Available columns: " . implode(', ', $columns) . "</p>";
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Error checking table: " . $e->getMessage() . "</p>";
        
        // If table doesn't exist, create it with basic structure
        echo "<h2>📋 Creating barber table...</h2>";
        $create_table_sql = "
            CREATE TABLE IF NOT EXISTS barber (
                id SERIAL PRIMARY KEY,
                docemail VARCHAR(255) UNIQUE NOT NULL,
                docname VARCHAR(255) NOT NULL,
                docpassword VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        $pdo->exec($create_table_sql);
        echo "<p style='color: green;'>✅ Barber table created successfully!</p>";
        
        $columns = ['id', 'docemail', 'docname', 'docpassword', 'created_at'];
    }

    // Check if barber already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM barber WHERE docemail = ?");
    $stmt->execute([$barber_email]);
    $barber_exists = $stmt->fetchColumn() > 0;

    if ($barber_exists) {
        echo "<h2 style='color: orange;'>⚠️ Barber account already exists!</h2>";
        echo "<p>The barber account with email <strong>$barber_email</strong> already exists in the database.</p>";
    } else {
        // Insert barber with only the basic required columns
        echo "<h2>👨‍💼 Creating barber account...</h2>";
        $stmt = $pdo->prepare("INSERT INTO barber (docemail, docname, docpassword) VALUES (?, ?, ?)");
        $stmt->execute([$barber_email, $barber_name, $barber_password]);
        
        echo "<p style='color: green;'>✅ Barber account created successfully!</p>";
        echo "<ul>";
        echo "<li><strong>Email:</strong> $barber_email</li>";
        echo "<li><strong>Password:</strong> barber123</li>";
        echo "<li><strong>Name:</strong> $barber_name</li>";
        echo "</ul>";
    }

    // Check if webuser entry exists
    echo "<h2>👥 Checking webuser table...</h2>";
    
    // First check if webuser table exists
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM webuser WHERE email = ?");
        $stmt->execute([$barber_email]);
        $webuser_exists = $stmt->fetchColumn() > 0;

        if (!$webuser_exists) {
            // Insert into webuser table
            $stmt = $pdo->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'd')");
            $stmt->execute([$barber_email]);
            echo "<p style='color: green;'>✅ Webuser entry created for barber.</p>";
        } else {
            echo "<p style='color: blue;'>ℹ️ Webuser entry already exists for this barber.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: orange;'>⚠️ Webuser table might not exist. Creating it...</p>";
        
        // Create webuser table
        $create_webuser_sql = "
            CREATE TABLE IF NOT EXISTS webuser (
                id SERIAL PRIMARY KEY,
                email VARCHAR(255) UNIQUE NOT NULL,
                usertype CHAR(1) NOT NULL CHECK (usertype IN ('p', 'a', 'd')),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        $pdo->exec($create_webuser_sql);
        
        // Insert the barber user
        $stmt = $pdo->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'd')");
        $stmt->execute([$barber_email]);
        echo "<p style='color: green;'>✅ Webuser table created and barber user added!</p>";
    }

    // Commit transaction
    $pdo->commit();

    // Final verification
    echo "<hr>";
    echo "<h2>🔍 Final Verification:</h2>";
    
    // Check barber table
    $stmt = $pdo->prepare("SELECT * FROM barber WHERE docemail = ?");
    $stmt->execute([$barber_email]);
    $barber = $stmt->fetch();
    
    if ($barber) {
        echo "<p style='color: green;'>✅ Barber found in barber table:</p>";
        echo "<ul>";
        foreach ($barber as $key => $value) {
            if (!is_numeric($key) && $key !== 'docpassword') {
                echo "<li><strong>$key:</strong> " . htmlspecialchars($value) . "</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>❌ Barber NOT found in barber table!</p>";
    }
    
    // Check webuser table
    $stmt = $pdo->prepare("SELECT email, usertype FROM webuser WHERE email = ?");
    $stmt->execute([$barber_email]);
    $webuser = $stmt->fetch();
    
    if ($webuser) {
        echo "<p style='color: green;'>✅ User found in webuser table:</p>";
        echo "<ul>";
        echo "<li><strong>Email:</strong> " . htmlspecialchars($webuser['email']) . "</li>";
        echo "<li><strong>User Type:</strong> " . htmlspecialchars($webuser['usertype']) . "</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>❌ User NOT found in webuser table!</p>";
    }

    echo "<hr>";
    echo "<h2>🎯 Ready to Login!</h2>";
    echo "<div style='background: #e8f5e8; padding: 15px; border: 2px solid #4CAF50; border-radius: 5px;'>";
    echo "<h3>Login Credentials:</h3>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> barber1@pablings.com</li>";
    echo "<li><strong>Password:</strong> barber123</li>";
    echo "</ul>";
    echo "<p><a href='login.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔗 Go to Login Page</a></p>";
    echo "</div>";

} catch (PDOException $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    echo "<h2 style='color: red;'>❌ Database Error:</h2>";
    echo "<p style='background: #ffeeee; padding: 10px; border: 1px solid red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ General Error:</h2>";
    echo "<p style='background: #ffeeee; padding: 10px; border: 1px solid red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>