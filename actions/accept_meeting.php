<?php
session_start();
include '../config/db.php';

// Check if user is logged in and is a parent
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'parent') {
    $_SESSION['error'] = "You do not have permission to accept meetings.";
    header('Location: ../pages/meetings.php');
    exit();
}

// Log all incoming POST data for debugging
error_log("ACCEPT MEETING - Incoming POST data: " . json_encode($_POST));
error_log("ACCEPT MEETING - Current Session: " . json_encode($_SESSION));

// Ensure meeting ID is an integer
$meeting_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Log the parsed meeting ID
error_log("ACCEPT MEETING - Parsed meeting ID: " . $meeting_id);

// Validate meeting ID
if ($meeting_id <= 0) {
    $_SESSION['error'] = "Invalid meeting ID provided.";
    error_log("ACCEPT MEETING - Invalid meeting ID: " . $meeting_id);
    header('Location: ../pages/meetings.php');
    exit();
}

// Fetch the current meeting details before update
$pre_update_query = "SELECT * FROM meetings WHERE id = ?";
$pre_update_stmt = $conn->prepare($pre_update_query);
$pre_update_stmt->bind_param("i", $meeting_id);
$pre_update_stmt->execute();
$pre_update_result = $pre_update_stmt->get_result();
$pre_update_meeting = $pre_update_result->fetch_assoc();
error_log("ACCEPT MEETING - Pre-update meeting details: " . json_encode($pre_update_meeting));

// Update meeting status to accepted and clear rejection reason
$query = "UPDATE meetings SET 
            response_status = 'accepted', 
            rejection_reason = NULL 
          WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $meeting_id);

// Execute the update
if ($stmt->execute()) {
    // Check if any rows were actually updated
    if ($stmt->affected_rows > 0) {
        // Fetch the updated meeting details
        $post_update_query = "SELECT * FROM meetings WHERE id = ?";
        $post_update_stmt = $conn->prepare($post_update_query);
        $post_update_stmt->bind_param("i", $meeting_id);
        $post_update_stmt->execute();
        $post_update_result = $post_update_stmt->get_result();
        $post_update_meeting = $post_update_result->fetch_assoc();
        
        error_log("ACCEPT MEETING - Post-update meeting details: " . json_encode($post_update_meeting));
        
        $_SESSION['success'] = "Meeting accepted successfully.";
    } else {
        $_SESSION['error'] = "No meeting found with ID: " . $meeting_id;
        error_log("ACCEPT MEETING - No meeting found with ID: " . $meeting_id);
    }
} else {
    $_SESSION['error'] = "Failed to accept meeting: " . $stmt->error;
    error_log("ACCEPT MEETING - Failed to accept meeting: " . $stmt->error);
}

$stmt->close();

header('Location: ../pages/meetings.php');
exit();
?>
