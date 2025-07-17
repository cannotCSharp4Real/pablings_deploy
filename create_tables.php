<?php
// Include the database connection
require_once("connection.php");

try {
    // SQL statements to create tables with proper PostgreSQL syntax
    $sql = "
        CREATE TABLE IF NOT EXISTS customer (
            id SERIAL PRIMARY KEY,
            pemail VARCHAR(255) UNIQUE NOT NULL,
            pname VARCHAR(255) NOT NULL,
            ppassword VARCHAR(255) NOT NULL,
            paddress TEXT NOT NULL,
            pdob DATE NOT NULL,
            ptel VARCHAR(15) NOT NULL
        );

        CREATE TABLE IF NOT EXISTS admin (
            id SERIAL PRIMARY KEY,
            aemail VARCHAR(255) UNIQUE NOT NULL,
            aname VARCHAR(255) NOT NULL,
            apassword VARCHAR(255) NOT NULL
        );

        CREATE TABLE IF NOT EXISTS barber (
            id SERIAL PRIMARY KEY,
            docemail VARCHAR(255) UNIQUE NOT NULL,
            docname VARCHAR(255) NOT NULL,
            docpassword VARCHAR(255) NOT NULL
        );

        CREATE TABLE IF NOT EXISTS webuser (
            id SERIAL PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            usertype CHAR(1) NOT NULL CHECK (usertype IN ('p', 'a', 'd'))
        );

        -- Create indexes for better performance
        CREATE INDEX IF NOT EXISTS idx_customer_email ON customer(pemail);
        CREATE INDEX IF NOT EXISTS idx_admin_email ON admin(aemail);
        CREATE INDEX IF NOT EXISTS idx_barber_email ON barber(docemail);
        CREATE INDEX IF NOT EXISTS idx_webuser_email ON webuser(email);
        CREATE INDEX IF NOT EXISTS idx_webuser_type ON webuser(usertype);
    ";

    // Execute the SQL
    $pdo->exec($sql);
    echo "Tables created successfully with indexes.";
    
    // Create a default admin account if it doesn't exist
    $admin_email = "admin@pablings.com";
    $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
    
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin WHERE aemail = ?");
    $stmt->execute([$admin_email]);
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Create admin account
        $stmt = $pdo->prepare("INSERT INTO admin (aemail, aname, apassword) VALUES (?, ?, ?)");
        $stmt->execute([$admin_email, "System Administrator", $admin_password]);
        
        // Add to webuser table
        $stmt = $pdo->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'a')");
        $stmt->execute([$admin_email]);
        
        echo "<br>Default admin account created - Email: $admin_email, Password: admin123";
    }
    
} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
}
?>
