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
    $parent_name = mysqli_real_escape_string($conn, $parent_name);
    $student_name = mysqli_real_escape_string($conn, $student_name);
    $subject = mysqli_real_escape_string($conn, $subject);
    $meeting_date = mysqli_real_escape_string($conn, $meeting_date);
    $meeting_time = mysqli_real_escape_string($conn, $meeting_time);
    $status = mysqli_real_escape_string($conn, $status);

    $query = "UPDATE meetings SET 
              parent_name = ?, 
              student_name = ?, 
              subject = ?, 
              meeting_date = ?, 
              meeting_time = ?, 
              status = ? 
              WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssssi", $parent_name, $student_name, $subject, $meeting_date, $meeting_time, $status, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Meeting updated successfully.";
        header('Location: ../pages/meetings.php');
    } else {
        $_SESSION['error'] = "Failed to update meeting: " . mysqli_error($conn);
        header('Location: ../pages/edit_meeting.php?id=' . $id);
    }
    exit();
} else {
    $_SESSION['error'] = "Invalid request method.";
    header('Location: ../pages/meetings.php');
    exit();
}
?>
