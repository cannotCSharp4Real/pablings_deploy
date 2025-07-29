<?php
// PostgreSQL connection details
$host = "dpg-d1n4eper433s73bbh8g0-a.singapore-postgres.render.com";
$dbname = "pablings_dp_jdd3";
$user = "pablings_dp_jdd3_user";
$password = "EDy75KM1w3BN7vbxxc1Par4i26N1ho9p";
$port = "5432";

// Updated connection string with better SSL configuration
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=prefer";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Attempt connection with better error handling
$conn = pg_connect($conn_string);

if (!$conn) {
    // Use proper error handling for deprecated function
    $error_msg = pg_last_error($conn);
    die("Connection failed: " . ($error_msg ? $error_msg : "Unknown connection error"));
}

// Check if connection is still valid
if (pg_connection_status($conn) !== PGSQL_CONNECTION_OK) {
    die("Connection lost: " . pg_last_error($conn));
}

// form values
$value = $_POST['Gcash'];
$value2 = $_POST['gcashnumber'];
$value3 = $_POST['Amount'];
$value4 = $_POST['DOP'];

// Check if any required fields are empty
if (empty($value) || empty($value2) || empty($value3) || empty($value4)) {
    echo "Note: Please fill the form properly. Kindly check if the details in the required fields are correct.";
} else {
    // First, check if the table exists
    $check_table_sql = "SELECT EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_name = 'form2'
    );";
    
    $table_exists = pg_query($conn, $check_table_sql);
    if ($table_exists) {
        $exists = pg_fetch_result($table_exists, 0, 0);
        if ($exists == 'f') {
            // Table doesn't exist, create it
            $create_table_sql = "
            CREATE TABLE form2 (
                id SERIAL PRIMARY KEY,
                Gcash VARCHAR(255) NOT NULL,
                gcashnumber VARCHAR(255) NOT NULL,
                Amount DECIMAL(10,2) NOT NULL,
                DOP VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );";
            
            $create_result = pg_query($conn, $create_table_sql);
            if (!$create_result) {
                echo "Error creating table: " . pg_last_error($conn);
                exit;
            }
        }
    }
    
    // Escape values to prevent SQL injection
    $value = pg_escape_string($conn, $value);
    $value2 = pg_escape_string($conn, $value2);
    $value3 = pg_escape_string($conn, $value3);
    $value4 = pg_escape_string($conn, $value4);

    // Prepare SQL query
    $sql = "INSERT INTO form2 (Gcash, gcashnumber, Amount, DOP) VALUES ('$value', '$value2', '$value3', '$value4')";

    // Execute query and check result
    $result = pg_query($conn, $sql);
    if ($result) {
        echo "<div style=\"text-align: center;\">\n            <img src=\"gcash.png\" alt=\"Image\" style=\"width: 1000px; height: auto; display: inline-block;\">\n          </div>";
    } else {
        echo "Error: " . pg_last_error($conn);
    }
}

// Close connection
pg_close($conn);
?>