<?php
session_start();
include '../config/db.php';

// Check if user is logged in and is a parent
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'parent') {
    $_SESSION['error'] = "You do not have permission to reject meetings.";
    header('Location: ../pages/meetings.php');
    exit();
}

// Check if meeting ID is provided
if (!isset($_POST['id'])) {
    $_SESSION['error'] = "Invalid meeting ID.";
    header('Location: ../pages/meetings.php');
    exit();
}

$meeting_id = $_POST['id'];

// Fetch the current meeting details for logging
$check_query = "SELECT * FROM meetings WHERE id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("i", $meeting_id);
$check_stmt->execute();
$result = $check_stmt->get_result();
$meeting = $result->fetch_assoc();

// Log meeting details before update
error_log("Attempting to reject meeting: " . json_encode($meeting));

// Validate rejection reason
if (!isset($_POST['rejection_reason']) || trim($_POST['rejection_reason']) === '') {
    $_SESSION['error'] = "Rejection reason is required.";
    error_log("Rejection reason is empty for meeting ID: " . $meeting_id);
    header('Location: ../pages/meetings.php');
    exit();
}

$rejection_reason = trim($_POST['rejection_reason']);

// Update meeting status to rejected with reason
$query = "UPDATE meetings SET 
            response_status = 'rejected', 
            rejection_reason = ? 
          WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $rejection_reason, $meeting_id);

if ($stmt->execute()) {
    // Check if any rows were actually updated
    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = "Meeting rejected successfully.";
        
        // Fetch and log the updated meeting details
        $check_query = "SELECT * FROM meetings WHERE id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $meeting_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $updated_meeting = $result->fetch_assoc();
        
        error_log("Meeting rejected. Details: " . json_encode($updated_meeting));
    } else {
        $_SESSION['error'] = "No meeting found with ID: " . $meeting_id;
        error_log("No meeting found with ID: " . $meeting_id);
    }
} else {
    $_SESSION['error'] = "Failed to reject meeting: " . $stmt->error;
    error_log("Failed to reject meeting: " . $stmt->error);
}

$stmt->close();

header('Location: ../pages/meetings.php');
exit();
?>
