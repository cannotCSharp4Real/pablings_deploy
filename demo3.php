<?php
$servername = getenv("DB_HOST");
$username = getenv("DB_USER");
$password = getenv("DB_PASS");
$dbname   = getenv("DB_NAME");

if (!$servername || !$username || !$password || !$dbname) {
    die("Database connection environment variables are not set. Please check your deployment settings.");
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
    $value = $conn->real_escape_string($value);
    $value2 = $conn->real_escape_string($value2);
    $value3 = $conn->real_escape_string($value3);
    $value4 = $conn->real_escape_string($value4);

    // Prepare SQL query
    $sql = "INSERT INTO form2 (Gcash, gcashnumber, Amount, DOP) VALUES ('$value', '$value2', '$value3', '$value4')";

    // Execute query and check result
    if ($conn->query($sql) === TRUE) {
        echo "<div style=\"text-align: center;\">
            <img src=\"gcash.png\" alt=\"Image\" style=\"width: 1000px; height: auto; display: inline-block;\">
          </div>";
        echo "";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close connection
$conn->close();
?>