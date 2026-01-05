<?php
session_start();
require_once '../config/db.php';

// Check if user is logged in as teacher
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'teacher') {
    header('Location: ../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Log all POST data
    error_log("POST Data: " . print_r($_POST, true));
    error_log("Session Data: " . print_r($_SESSION, true));

    // Validate input data
    $parent_name = trim($_POST['parent_name']);
    $student_name = trim($_POST['student_name']);
    $subject = trim($_POST['subject']);
    $meeting_date = $_POST['meeting_date'];
    $meeting_time = $_POST['meeting_time'];
    $status = $_POST['status'] ?? 'scheduled'; // Default to scheduled if not set

    // Server-side validation
    if (empty($parent_name) || empty($student_name) || empty($subject) || empty($meeting_date) || empty($meeting_time)) {
        $_SESSION['error'] = "All fields are required!";
        header('Location: ../pages/meetings.php');
        exit();
    }

    // Sanitize input data
    $parent_name = mysqli_real_escape_string($conn, $parent_name);
    $student_name = mysqli_real_escape_string($conn, $student_name);
    $subject = mysqli_real_escape_string($conn, $subject);
    $meeting_date = mysqli_real_escape_string($conn, $meeting_date);
    $meeting_time = mysqli_real_escape_string($conn, $meeting_time);
    $status = mysqli_real_escape_string($conn, $status);

    // Get teacher's name from session
    $teacher_name = $_SESSION['user_name'] ?? 'Unknown Teacher';

    // Prepare SQL statement
    $sql = "INSERT INTO meetings (
        parent_name, 
        student_name, 
        subject, 
        meeting_date, 
        meeting_time, 
        teacher_name, 
        status
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    // Prepare and bind
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        $_SESSION['error'] = "Database error occurred. Please try again.";
        header('Location: ../pages/meetings.php');
        exit();
    }

    $stmt->bind_param("sssssss", 
        $parent_name, 
        $student_name, 
        $subject, 
        $meeting_date, 
        $meeting_time,
        $teacher_name,
        $status
    );

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['success'] = "Meeting scheduled successfully!";
        error_log("Meeting scheduled: Parent $parent_name, Student $student_name, Date $meeting_date");
    } else {
        $_SESSION['error'] = "Error scheduling meeting: " . $stmt->error;
        error_log("Meeting scheduling failed: " . $stmt->error);
    }

    $stmt->close();
    header('Location: ../pages/meetings.php');
    exit();
} else {
    header('Location: ../pages/meetings.php');
    exit();
}
?>
