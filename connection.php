<?php
    $host = getenv('DB_HOST') ?: 'localhost';
    $username = getenv('DB_USERNAME') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';
    $dbname = getenv('DB_NAME') ?: 'edoc';

    $database = new mysqli($host, $username, $password, $dbname);
    if ($database->connect_error) {
        die("Connection failed: " . $database->connect_error);
    }
?>