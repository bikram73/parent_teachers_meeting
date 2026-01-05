<?php
session_start();
include '../config/db.php';

// Logging function
function logDeletionAttempt($id, $success, $error = null) {
    $log_file = '../logs/meeting_deletion.log';
    $timestamp = date('Y-m-d H:i:s');
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Unknown';
    $log_message = "[{$timestamp}] User {$user_id} attempted to delete meeting {$id}: " . 
                   ($success ? 'SUCCESS' : 'FAILED') . 
                   ($error ? " - Error: {$error}" : '') . "\n";
    
    // Ensure log directory exists
    $log_dir = dirname($log_file);
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

// Check if user is logged in as teacher
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'teacher') {
    logDeletionAttempt(null, false, 'Unauthorized access');
    header('Location: ../pages/login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Begin transaction for safer deletion
    mysqli_begin_transaction($conn);
    
    try {
        // First, verify the meeting exists and belongs to the teacher's subject
        $check_query = "SELECT teacher_name FROM meetings WHERE id = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "i", $id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) === 0) {
            throw new Exception("Meeting not found");
        }
        
        // Delete the meeting
        $delete_query = "DELETE FROM meetings WHERE id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "i", $id);
        
        if (!mysqli_stmt_execute($delete_stmt)) {
            throw new Exception("Failed to delete meeting");
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Log successful deletion
        logDeletionAttempt($id, true);
        
        // Redirect with success message
        $_SESSION['success'] = "Meeting successfully deleted.";
        header('Location: ../pages/teacher_dashboard.php');
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        
        // Log failed deletion
        logDeletionAttempt($id, false, $e->getMessage());
        
        // Redirect with error message
        $_SESSION['error'] = "Failed to delete meeting: " . $e->getMessage();
        header('Location: ../pages/teacher_dashboard.php');
        exit();
    }
} else {
    // No meeting ID provided
    logDeletionAttempt(null, false, 'No meeting ID provided');
    header('Location: ../pages/teacher_dashboard.php');
    exit();
}
?>