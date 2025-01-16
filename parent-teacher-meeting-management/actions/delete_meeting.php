<?php
session_start();
include '../config/db.php';

// Check if user is logged in as teacher
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../pages/login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $query = "DELETE FROM meetings WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header('Location: ../pages/teacher_dashboard.php?success=2');
    } else {
        header('Location: ../pages/teacher_dashboard.php?error=2');
    }
} else {
    header('Location: ../pages/teacher_dashboard.php');
}
?>
