<?php
session_start();
include '../config/db.php';

// Check if user is logged in as teacher
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../pages/login.php');
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int) $_GET['id'];
    $status = trim($_GET['status']);
    
    $query = "UPDATE meetings SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $id);
    
    if ($stmt->execute()) {
        header('Location: ../pages/teacher_dashboard.php?success=3');
    } else {
        header('Location: ../pages/teacher_dashboard.php?error=3');
    }
} else {
    header('Location: ../pages/teacher_dashboard.php');
}
?>
