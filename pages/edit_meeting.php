<?php
session_start();
include '../config/db.php';

// Check if user is logged in as teacher
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

// Get meeting details
$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : 0;
$query = "SELECT * FROM meetings WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$meeting = mysqli_fetch_assoc($result);

if (!$meeting) {
    header('Location: teacher_dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Meeting - Meeting Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Meeting</h2>
        
        <div class="card">
            <div class="card-header">
                <h4>Update Meeting Details</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="../actions/update_meeting.php">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($meeting['id']); ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="parent_name" class="form-label">Parent Name</label>
                            <input type="text" class="form-control" id="parent_name" name="parent_name" 
                                   value="<?php echo htmlspecialchars($meeting['parent_name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="student_name" class="form-label">Student Name</label>
                            <input type="text" class="form-control" id="student_name" name="student_name" 
                                   value="<?php echo htmlspecialchars($meeting['student_name']); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" 
                               value="<?php echo htmlspecialchars($meeting['subject']); ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="meeting_date" class="form-label">Meeting Date</label>
                            <input type="date" class="form-control" id="meeting_date" name="meeting_date" 
                                   value="<?php echo htmlspecialchars($meeting['meeting_date']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="meeting_time" class="form-label">Meeting Time</label>
                            <input type="time" class="form-control" id="meeting_time" name="meeting_time" 
                                   value="<?php echo htmlspecialchars($meeting['meeting_time']); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="scheduled" <?php echo $meeting['status'] == 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                            <option value="completed" <?php echo $meeting['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $meeting['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Update Meeting</button>
                        <a href="teacher_dashboard.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
