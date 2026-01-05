-- Create database
CREATE DATABASE IF NOT EXISTS ptm_system;
USE ptm_system;

-- Users table
CREATE TABLE IF NOT EXISTS parent_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS teacher_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    subject NOT NULL,
    phone VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Meetings table
USE ptm_system; -- Switch to the desired database

CREATE TABLE meetings (
    id INT PRIMARY KEY AUTO_INCREMENT, -- Unique meeting ID
    parent_name VARCHAR(255) NOT NULL, -- Name of the parent
    student_name VARCHAR(255) NOT NULL, -- Name of the student
    subject VARCHAR(255) NOT NULL, -- Subject of the meeting
    meeting_date DATE NOT NULL, -- Date of the meeting
    meeting_time TIME NOT NULL, -- Time of the meeting
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled', -- Meeting status
    response_status ENUM('accept', 'reject') DEFAULT 'accept', -- Response status for the meeting
    rejection_reason VARCHAR(100) DEFAULT NULL, -- Reason for rejection if the meeting is rejected
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Timestamp for record creation
);


-- Notifications table
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Student Performance table
CREATE TABLE student_performance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usn VARCHAR(20) NOT NULL,
    student_name VARCHAR(100) NOT NULL,
    parent_name VARCHAR(100) NOT NULL,
    marks DECIMAL(5,2) NOT NULL,
    subject VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Student Parent Mapping table
CREATE TABLE student_parent_mapping (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usn VARCHAR(20) NOT NULL,
    parent_id INT NOT NULL,
    FOREIGN KEY (parent_id) REFERENCES users(id),
    UNIQUE KEY unique_student (usn)
);

-- Insert admin user
INSERT INTO users (name, email, password, role) 
VALUES ('Admin', 'admin@ptm.com', '$2y$10$8KzQ8IzAF9tXXXXXXXXXXOBhxXXXXXXXXXXXXXXXXXXXXXXXXXXXX', 'admin');
