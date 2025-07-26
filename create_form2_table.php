<?php
// PostgreSQL connection details
$host = "dpg-d1n4eper433s73bbh8g0-a.singapore-postgres.render.com";
$dbname = "pablings_dp_jdd3";
$user = "pablings_dp_jdd3_user";
$password = "EDy75KM1w3BN7vbxxc1Par4i26N1ho9p";
$port = "5432";

$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require";
$conn = pg_connect($conn_string);

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Create the form2 table
$create_table_sql = "
CREATE TABLE IF NOT EXISTS form2 (
    id SERIAL PRIMARY KEY,
    Gcash VARCHAR(255) NOT NULL,
    gcashnumber VARCHAR(255) NOT NULL,
    Amount DECIMAL(10,2) NOT NULL,
    DOP VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";

$result = pg_query($conn, $create_table_sql);

if ($result) {
    echo "Table 'form2' created successfully!";
} else {
    echo "Error creating table: " . pg_last_error($conn);
}

// Close connection
pg_close($conn);
?> 