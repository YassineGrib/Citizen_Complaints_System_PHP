<?php
/**
 * Database Configuration
 * 
 * This file contains the database connection settings for the Citizen Complaints System.
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'complaints_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create database connection
function get_db_connection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set character set
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to sanitize database inputs
function db_sanitize($conn, $input) {
    if (is_array($input)) {
        $sanitized = [];
        foreach ($input as $key => $value) {
            $sanitized[$key] = db_sanitize($conn, $value);
        }
        return $sanitized;
    }
    return $conn->real_escape_string($input);
}
?>
