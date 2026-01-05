<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "ptm_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . $database;
if ($conn->query($sql) === FALSE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($database);

// Drop users table if exists
$sql = "DROP TABLE IF EXISTS users";
$conn->query($sql);

// Create parent table
$sql = "CREATE TABLE IF NOT EXISTS parent_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === FALSE) {
    die("Error creating parent table: " . $conn->error);
}

// Create teacher table
$sql = "CREATE TABLE IF NOT EXISTS teacher_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === FALSE) {
    die("Error creating teacher table: " . $conn->error);
}

// Check if meetings table exists
$result = $conn->query("SHOW TABLES LIKE 'meetings'");
if ($result->num_rows == 0) {
    // Create meetings table only if it doesn't exist
    $sql = "CREATE TABLE meetings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        parent_name VARCHAR(255) NOT NULL,
        student_name VARCHAR(255) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        teacher_name VARCHAR(255) NOT NULL,
        meeting_date DATE NOT NULL,
        meeting_time TIME NOT NULL,
        status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
        response_status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
        rejection_reason TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql) === FALSE) {
        die("Error creating meetings table: " . $conn->error);
    }
} else {
    // Ensure columns exist, add if they don't
    $columns_to_check = [
        'response_status' => "ALTER TABLE meetings ADD COLUMN IF NOT EXISTS response_status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending'",
        'rejection_reason' => "ALTER TABLE meetings ADD COLUMN IF NOT EXISTS rejection_reason TEXT"
    ];

    foreach ($columns_to_check as $column => $alter_query) {
        // Check if column exists
        $check_column_query = "SHOW COLUMNS FROM meetings LIKE '$column'";
        $column_result = $conn->query($check_column_query);
        
        if ($column_result->num_rows == 0) {
            // Column does not exist, add it
            if ($conn->query($alter_query) === FALSE) {
                die("Error adding column $column: " . $conn->error);
            }
        } else {
            // Ensure existing table has the correct default
            $alter_query = "ALTER TABLE meetings 
                MODIFY COLUMN response_status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending'";
            $conn->query($alter_query);
        }
    }
}

// Function to get the appropriate table name based on role
function getTableName($role) {
    return $role === 'parent' ? 'parent_users' : 'teacher_users';
}
?>
