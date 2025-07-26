<?php
include("connection.php");

// Check if specialties table exists
$check_table_sql = "SELECT EXISTS (
    SELECT FROM information_schema.tables 
    WHERE table_schema = 'public' 
    AND table_name = 'specialties'
);";

$table_exists = $database->query($check_table_sql);
if ($table_exists) {
    $exists = $table_exists->fetch(PDO::FETCH_ASSOC);
    if ($exists['exists'] == 'f') {
        // Table doesn't exist, create it
        $create_table_sql = "
        CREATE TABLE specialties (
            id SERIAL PRIMARY KEY,
            sname VARCHAR(100) NOT NULL
        );";
        
        $create_result = $database->query($create_table_sql);
        if ($create_result) {
            echo "Specialties table created successfully!<br>";
            
            // Insert some default specialties
            $insert_sql = "INSERT INTO specialties (sname) VALUES 
            ('Hair Cutting'),
            ('Hair Styling'),
            ('Beard Trimming'),
            ('Hair Coloring'),
            ('Hair Treatment'),
            ('Shampoo and Conditioning'),
            ('Hair Extensions'),
            ('Scalp Treatment'),
            ('Hair Consultation'),
            ('Kids Haircut');";
            
            $insert_result = $database->query($insert_sql);
            if ($insert_result) {
                echo "Default specialties inserted successfully!";
            } else {
                echo "Error inserting default specialties: " . $database->errorInfo()[2];
            }
        } else {
            echo "Error creating specialties table: " . $database->errorInfo()[2];
        }
    } else {
        echo "Specialties table already exists.";
    }
} else {
    echo "Error checking table existence: " . $database->errorInfo()[2];
}
?> 