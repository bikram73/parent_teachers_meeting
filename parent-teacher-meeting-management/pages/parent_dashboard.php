<?php
session_start();
include '../config/db.php';

// Check if user is logged in as parent
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'parent') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard - Meeting Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Parent Dashboard</h2>
        
        <!-- View Meetings -->
        <div class="card">
            <div class="card-header">
                <h4>Scheduled Meetings</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Only show meetings for the logged-in parent
                        $parent_name = $_SESSION['name']; // Assuming the parent's name is stored in session
                        $query = "SELECT * FROM meetings WHERE parent_name = ? ORDER BY meeting_date, meeting_time";
                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, "s", $parent_name);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['subject']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['meeting_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['meeting_time']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
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
