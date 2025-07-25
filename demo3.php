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

// form values
$value = $_POST['Gcash'];
$value2 = $_POST['gcashnumber'];
$value3 = $_POST['Amount'];
$value4 = $_POST['DOP'];

// Check if any required fields are empty
if (empty($value) || empty($value2) || empty($value3) || empty($value4)) {
    echo "Note: Please fill the form properly. Kindly check if the details in the required fields are correct." ;
} else {
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
        echo "";
    } else {
        echo "Error: " . pg_last_error($conn);
    }
}

// Close connection
pg_close($conn);
?>