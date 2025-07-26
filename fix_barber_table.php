<?php
include("connection.php");

// Check if barber table has specialties column
$check_column_sql = "SELECT column_name 
FROM information_schema.columns 
WHERE table_name = 'barber' 
AND column_name = 'specialties';";

$column_exists = $database->query($check_column_sql);
if ($column_exists && $column_exists->rowCount() == 0) {
    // Column doesn't exist, add it
    $add_column_sql = "ALTER TABLE barber ADD COLUMN specialties INTEGER;";
    $add_result = $database->query($add_column_sql);
    if ($add_result) {
        echo "Specialties column added to barber table successfully!";
    } else {
        echo "Error adding specialties column: " . $database->errorInfo()[2];
    }
} else {
    echo "Specialties column already exists in barber table.";
}
?> 