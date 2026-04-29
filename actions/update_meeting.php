<?php
session_start();
include '../config/db.php';

// Check if user is logged in as teacher
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'teacher') {
    $_SESSION['error'] = "You do not have permission to update meetings.";
    header('Location: ../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $parent_name = trim($_POST['parent_name']);
    $student_name = trim($_POST['student_name']);
    $subject = trim($_POST['subject']);
    $meeting_date = $_POST['meeting_date'];
    $meeting_time = $_POST['meeting_time'];
    $status = $_POST['status'];

    // Server-side validation
    if (empty($parent_name) || empty($student_name) || empty($subject) || empty($meeting_date) || empty($meeting_time)) {
        $_SESSION['error'] = "All fields are required.";
        header('Location: ../pages/edit_meeting.php?id=' . $id);
        exit();
    }

    // Sanitize input
    $parent_name = trim($parent_name);
    $student_name = trim($student_name);
    $subject = trim($subject);
    $meeting_date = trim($meeting_date);
    $meeting_time = trim($meeting_time);
    $status = trim($status);

    $query = "UPDATE meetings SET 
              parent_name = ?, 
              student_name = ?, 
              subject = ?, 
              meeting_date = ?, 
              meeting_time = ?, 
              status = ? 
              WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssi", $parent_name, $student_name, $subject, $meeting_date, $meeting_time, $status, $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Meeting updated successfully.";
        header('Location: ../pages/meetings.php');
    } else {
        $_SESSION['error'] = "Failed to update meeting: " . $stmt->error;
        header('Location: ../pages/edit_meeting.php?id=' . $id);
    }
    exit();
} else {
    $_SESSION['error'] = "Invalid request method.";
    header('Location: ../pages/meetings.php');
    exit();
}
?>
