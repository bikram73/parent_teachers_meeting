-- Create database
CREATE DATABASE IF NOT EXISTS ptm_system;
USE ptm_system;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'parent') NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Meetings table
CREATE TABLE meetings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_id INT NOT NULL,
    parent_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    meeting_date DATE NOT NULL,
    meeting_time TIME NOT NULL,
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id),
    FOREIGN KEY (parent_id) REFERENCES users(id)
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
