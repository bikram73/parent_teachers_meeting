<?php
session_start();
include 'config/db.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parent_name = $_POST['parent_name'];
    $student_name = $_POST['student_name'];
    $subject = $_POST['subject'];
    $meeting_date = $_POST['meeting_date'];
    $meeting_time = $_POST['meeting_time'];

    $sql = "INSERT INTO meetings (parent_name, student_name, subject, meeting_date, meeting_time) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $parent_name, $student_name, $subject, $meeting_date, $meeting_time);
    
    if ($stmt->execute()) {
        $success_message = "Meeting scheduled successfully!";
    } else {
        $error_message = "Error: " . $conn->error;
    }
    $stmt->close();
}

// Fetch existing meetings
$sql = "SELECT * FROM meetings ORDER BY meeting_date, meeting_time";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Meeting Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 900px; }
        .form-container { margin-bottom: 40px; }
        .meeting-list { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Schedule Parent-Teacher Meeting</h2>

        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Meeting Form -->
        <div class="form-container">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
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

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Schedule Meeting</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Meetings List -->
        <div class="meeting-list">
            <h3 class="mb-4">Scheduled Meetings</h3>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
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
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['parent_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                <td><?php echo htmlspecialchars($row['meeting_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['meeting_time']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="edit_meeting.php?id=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-primary">Edit</a>
                                        <a href="delete_meeting.php?id=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Are you sure you want to delete this meeting?')">Delete</a>
                                        <button type="button" 
                                                class="btn btn-sm btn-secondary dropdown-toggle" 
                                                data-bs-toggle="dropdown">
                                            Status
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" 
                                                  href="update_status.php?id=<?php echo $row['id']; ?>&status=scheduled">Scheduled</a></li>
                                            <li><a class="dropdown-item" 
                                                  href="update_status.php?id=<?php echo $row['id']; ?>&status=completed">Completed</a></li>
                                            <li><a class="dropdown-item" 
                                                  href="update_status.php?id=<?php echo $row['id']; ?>&status=cancelled">Cancelled</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
