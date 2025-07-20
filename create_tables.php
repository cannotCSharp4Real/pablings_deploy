<?php
// Include the database connection
require_once("connection.php");

try {
    // SQL statements to create all necessary tables with proper PostgreSQL syntax
    $sql = "
        -- Create customer table
        CREATE TABLE IF NOT EXISTS customer (
            id SERIAL PRIMARY KEY,
            pemail VARCHAR(255) UNIQUE NOT NULL,
            pname VARCHAR(255) NOT NULL,
            ppassword VARCHAR(255) NOT NULL,
            paddress TEXT NOT NULL,
            pdob DATE NOT NULL,
            ptel VARCHAR(15) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Create admin table
        CREATE TABLE IF NOT EXISTS admin (
            id SERIAL PRIMARY KEY,
            aemail VARCHAR(255) UNIQUE NOT NULL,
            aname VARCHAR(255) NOT NULL,
            apassword VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Create barber table
        CREATE TABLE IF NOT EXISTS barber (
            id SERIAL PRIMARY KEY,
            docemail VARCHAR(255) UNIQUE NOT NULL,
            docname VARCHAR(255) NOT NULL,
            docpassword VARCHAR(255) NOT NULL,
            docnic VARCHAR(20),
            doctel VARCHAR(15),
            specialties TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Create webuser table
        CREATE TABLE IF NOT EXISTS webuser (
            id SERIAL PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            usertype CHAR(1) NOT NULL CHECK (usertype IN ('p', 'a', 'd')),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Create schedule table
        CREATE TABLE IF NOT EXISTS schedule (
            scheduleid SERIAL PRIMARY KEY,
            docid INTEGER NOT NULL,
            title VARCHAR(255) NOT NULL,
            scheduledate DATE NOT NULL,
            scheduletime TIME NOT NULL,
            nop INTEGER DEFAULT 1 CHECK (nop > 0),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (docid) REFERENCES barber(id) ON DELETE CASCADE
        );

        -- Create appointment table
        CREATE TABLE IF NOT EXISTS appointment (
            appoid SERIAL PRIMARY KEY,
            pid INTEGER NOT NULL,
            apponum INTEGER NOT NULL,
            scheduleid INTEGER NOT NULL,
            appodate DATE NOT NULL,
            appotime TIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (pid) REFERENCES customer(id) ON DELETE CASCADE,
            FOREIGN KEY (scheduleid) REFERENCES schedule(scheduleid) ON DELETE CASCADE,
            UNIQUE(apponum)
        );

        -- Create specialties table for barber specializations
        CREATE TABLE IF NOT EXISTS specialties (
            id SERIAL PRIMARY KEY,
            sname VARCHAR(50) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Create indexes for better performance
        CREATE INDEX IF NOT EXISTS idx_customer_email ON customer(pemail);
        CREATE INDEX IF NOT EXISTS idx_admin_email ON admin(aemail);
        CREATE INDEX IF NOT EXISTS idx_barber_email ON barber(docemail);
        CREATE INDEX IF NOT EXISTS idx_webuser_email ON webuser(email);
        CREATE INDEX IF NOT EXISTS idx_webuser_type ON webuser(usertype);
        CREATE INDEX IF NOT EXISTS idx_schedule_date ON schedule(scheduledate);
        CREATE INDEX IF NOT EXISTS idx_schedule_docid ON schedule(docid);
        CREATE INDEX IF NOT EXISTS idx_appointment_pid ON appointment(pid);
        CREATE INDEX IF NOT EXISTS idx_appointment_scheduleid ON appointment(scheduleid);
        CREATE INDEX IF NOT EXISTS idx_appointment_date ON appointment(appodate);
    ";

    // Execute the SQL
    $pdo->exec($sql);
    echo "<h2>✅ All tables created successfully with indexes!</h2>";
    
    // Insert default specialties
    $specialties = [
        'Haircut',
        'Hair Wash',
        'Beard Trim',
        'Mustache Trim',
        'Hair Styling',
        'Hair Coloring',
        'Scalp Treatment'
    ];

    $stmt = $pdo->prepare("INSERT INTO specialties (sname) VALUES (?) ON CONFLICT (sname) DO NOTHING");
    foreach ($specialties as $specialty) {
        $stmt->execute([$specialty]);
    }
    echo "<p>✅ Default specialties inserted.</p>";
    
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
        $stmt = $pdo->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'a') ON CONFLICT (email) DO NOTHING");
        $stmt->execute([$admin_email]);
        
        echo "<p>✅ Default admin account created:</p>";
        echo "<ul>";
        echo "<li><strong>Email:</strong> $admin_email</li>";
        echo "<li><strong>Password:</strong> admin123</li>";
        echo "<li><strong>Type:</strong> Administrator</li>";
        echo "</ul>";
    } else {
        echo "<p>ℹ️ Default admin account already exists.</p>";
    }
    
    // Create a sample barber account
    $barber_email = "barber1@pablings.com";
    $barber_password = password_hash("barber123", PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM barber WHERE docemail = ?");
    $stmt->execute([$barber_email]);
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $stmt = $pdo->prepare("INSERT INTO barber (docemail, docname, docpassword, docnic, doctel, specialties) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $barber_email, 
            "John Smith", 
            $barber_password,
            "123456789V",
            "0771234567",
            "Haircut, Beard Trim, Hair Styling"
        ]);
        
        // Add to webuser table
        $stmt = $pdo->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'd') ON CONFLICT (email) DO NOTHING");
        $stmt->execute([$barber_email]);
        
        echo "<p>✅ Sample barber account created:</p>";
        echo "<ul>";
        echo "<li><strong>Email:</strong> $barber_email</li>";
        echo "<li><strong>Password:</strong> barber123</li>";
        echo "<li><strong>Name:</strong> John Smith</li>";
        echo "<li><strong>Type:</strong> Barber</li>";
        echo "</ul>";
    } else {
        echo "<p>ℹ️ Sample barber account already exists.</p>";
    }
    
    // Show table status
    echo "<h3>📊 Database Tables Status:</h3>";
    $tables = ['customer', 'admin', 'barber', 'webuser', 'schedule', 'appointment', 'specialties'];
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Table Name</th><th>Row Count</th><th>Status</th></tr>";
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table");
            $stmt->execute();
            $count = $stmt->fetchColumn();
            echo "<tr><td>$table</td><td>$count</td><td style='color: green;'>✅ Ready</td></tr>";
        } catch (PDOException $e) {
            echo "<tr><td>$table</td><td>-</td><td style='color: red;'>❌ Error</td></tr>";
        }
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ Error creating tables:</h2>";
    echo "<p style='background: #ffeeee; padding: 10px; border: 1px solid red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
