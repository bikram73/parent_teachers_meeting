<?php
session_start();
include '../config/db.php';

// Check if user is logged in as teacher
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Meeting Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Teacher Dashboard</h2>
        
        <!-- Add Meeting Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Schedule New Meeting</h4>
            </div>
            <div class="card-body">
                <form id="meetingForm" method="POST" action="../actions/save_meeting.php">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="parent_name" class="form-label">Parent Name</label>
                            <input type="text" class="form-control" id="parent_name" name="parent_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="student_name" class="form-label">Student Name</label>
                            <input type="text" class="form-control" id="student_name" name="student_name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="meeting_date" class="form-label">Meeting Date</label>
                            <input type="date" class="form-control" id="meeting_date" name="meeting_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="meeting_time" class="form-label">Meeting Time</label>
                            <input type="time" class="form-control" id="meeting_time" name="meeting_time" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Schedule Meeting</button>
                </form>
            </div>
        </div>

        <!-- Meetings List -->
        <div class="card">
            <div class="card-header">
                <h4>Scheduled Meetings</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Parent Name</th>
                            <th>Student Name</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM meetings ORDER BY meeting_date, meeting_time";
                        $result = mysqli_query($conn, $query);
                        
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['parent_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['subject']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['meeting_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['meeting_time']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td>
                                    <a href='edit_meeting.php?id=" . $row['id'] . "' class='btn btn-sm btn-primary'>Edit</a>
                                    <a href='../actions/delete_meeting.php?id=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                    <a href='../actions/update_status.php?id=" . $row['id'] . "&status=completed' class='btn btn-sm btn-success'>Complete</a>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
