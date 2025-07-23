<?php
// Include the database connection
require_once("connection.php");

try {
    // Check if connection was successful
    if (!isset($pdo)) {
        die("Database connection failed. Please check your database configuration.");
    }

    // Barber account details
    $barber_email = "barber1@pablings.com";
    $barber_password = password_hash("barber123", PASSWORD_DEFAULT);
    $barber_name = "John Smith";
    $barber_nic = "123456789V";
    $barber_tel = "0771234567";
    $specialties = "Haircut, Beard Trim, Hair Styling";

    // Start transaction
    $pdo->beginTransaction();

    // First, check if the barber already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM barber WHERE docemail = ?");
    $stmt->execute([$barber_email]);
    $barber_exists = $stmt->fetchColumn() > 0;

    if ($barber_exists) {
        echo "<h2 style='color: orange;'>⚠️ Barber account already exists!</h2>";
        echo "<p>The barber account with email <strong>$barber_email</strong> already exists in the database.</p>";
    } else {
        // Insert barber account
        $stmt = $pdo->prepare("INSERT INTO barber (docemail, docname, docpassword, docnic, doctel, specialties) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $barber_email,
            $barber_name,
            $barber_password,
            $barber_nic,
            $barber_tel,
            $specialties
        ]);

        echo "<h2 style='color: green;'>✅ Barber account created successfully!</h2>";
        echo "<ul>";
        echo "<li><strong>Email:</strong> $barber_email</li>";
        echo "<li><strong>Password:</strong> barber123</li>";
        echo "<li><strong>Name:</strong> $barber_name</li>";
        echo "</ul>";
    }

    // Check if webuser entry exists
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

    // Commit transaction
    $pdo->commit();

    // Verify the accounts were created properly
    echo "<h3>🔍 Account Verification:</h3>";
    
    // Check barber table
    $stmt = $pdo->prepare("SELECT id, docemail, docname FROM barber WHERE docemail = ?");
    $stmt->execute([$barber_email]);
    $barber = $stmt->fetch();
    
    if ($barber) {
        echo "<p style='color: green;'>✅ Barber found in barber table:</p>";
        echo "<ul>";
        echo "<li>ID: " . $barber['id'] . "</li>";
        echo "<li>Email: " . $barber['docemail'] . "</li>";
        echo "<li>Name: " . $barber['docname'] . "</li>";
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
        echo "<li>Email: " . $webuser['email'] . "</li>";
        echo "<li>User Type: " . $webuser['usertype'] . "</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>❌ User NOT found in webuser table!</p>";
    }

    echo "<hr>";
    echo "<h3>🎯 Now you can login with:</h3>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> barber1@pablings.com</li>";
    echo "<li><strong>Password:</strong> barber123</li>";
    echo "</ul>";
    echo "<p><a href='login.php'>← Go back to Login</a></p>";

} catch (PDOException $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    echo "<h2 style='color: red;'>❌ Database Error:</h2>";
    echo "<p style='background: #ffeeee; padding: 10px; border: 1px solid red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    
    // Show more details for debugging
    echo "<h3>Debug Information:</h3>";
    echo "<p><strong>Error Code:</strong> " . $e->getCode() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ General Error:</h2>";
    echo "<p style='background: #ffeeee; padding: 10px; border: 1px solid red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>