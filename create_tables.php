<?php
// Include the database connection
require_once("connection.php");

try {
    // SQL statements to create tables
    $sql = "
        CREATE TABLE IF NOT EXISTS customer (
            id SERIAL PRIMARY KEY,
            pemail VARCHAR(255) UNIQUE NOT NULL,
            pname VARCHAR(255) NOT NULL,
            ppassword VARCHAR(255) NOT NULL,
            paddress TEXT NOT NULL,
            pdob DATE NOT NULL,
            ptel VARCHAR(15) -- Adjust as necessary
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
            usertype CHAR(1) NOT NULL -- 'p' for customer, 'a' for admin, 'd' for barber
        );
    ";

    // Execute the SQL
    $pdo->exec($sql);
    echo "Tables created successfully.";
} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
}
?>
