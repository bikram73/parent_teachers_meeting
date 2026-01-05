<?php
session_start();
// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /parent-teacher-meeting-management/index.php");
    exit();
}

// Include database connection
include '../config/db.php';

$table = $_SESSION['user_role'] === 'parent' ? 'parent_users' : 'teacher_users';
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM $table WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent-Teacher Meeting Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard-page">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">PTM System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="meetings.php">Meetings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="student-performance.php">Student Performance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="page-content">
            <?php
            // Display success message
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        ' . $_SESSION['success'] . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
                unset($_SESSION['success']);
            }

            // Display error message
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ' . $_SESSION['error'] . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
                unset($_SESSION['error']);
            }
            ?>
            <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'teacher'): ?>
                <!-- Teacher Form -->
                <div class="form-container">
                    <h2 class="mb-4">Schedule Parent-Teacher Meeting</h2>
                    <div class="card">
                        <div class="card-body">
                            <form action="../actions/save_meeting.php" method="POST">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="parent_name" class="form-label">Parent Name</label>
                                        <input type="text" class="form-control" id="parent_name" name="parent_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="student_name" class="form-label">Student Name</label>
                                        <input type="text" class="form-control" id="student_name" name="student_name" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" class="form-control" id="subject" name="subject" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="meeting_date" class="form-label">Meeting Date</label>
                                        <input type="date" class="form-control" id="meeting_date" name="meeting_date" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="meeting_time" class="form-label">Meeting Time</label>
                                        <input type="time" class="form-control" id="meeting_time" name="meeting_time" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="status" class="form-label">Meeting Status</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="scheduled" selected>Scheduled</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Schedule Meeting</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Meeting List -->
            <div class="meeting-list">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Scheduled Meetings</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Parent Name</th>
                                        <th>Student Name</th>
                                        <th>Subject</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Response Status</th>
                                        <th>Rejection Reason</th>
                                        <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'teacher'): ?>
                                            <th>Actions</th>
                                        <?php endif; ?>
                                        <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'parent'): ?>
                                            <th>Actions</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // For parents, show only their meetings
                                    if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'parent') {
                                        $parent_name = $user['name'];
                                        $query = "SELECT * FROM meetings WHERE parent_name = ? ORDER BY meeting_date, meeting_time";
                                        $stmt = $conn->prepare($query);
                                        $stmt->bind_param("s", $parent_name);
                                    } else {
                                        // For teachers, show all meetings
                                        $query = "SELECT * FROM meetings ORDER BY meeting_date, meeting_time";
                                        $stmt = $conn->prepare($query);
                                    }
                                    
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['parent_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['subject']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['meeting_date']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['meeting_time']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                        // Display response status with color
                                        if($row['response_status'] == 'accepted') {
                                            echo "<td><span class='badge bg-success'>Accepted</span></td>";
                                        } elseif($row['response_status'] == 'rejected') {
                                            echo "<td><span class='badge bg-danger'>Rejected</span></td>";
                                        } else {
                                            echo "<td><span class='badge bg-warning'>Pending</span></td>";
                                        }
                                        
                                        // Display rejection reason if exists
                                        echo "<td>" . 
                                            ($row['response_status'] == 'rejected' ? 
                                                htmlspecialchars($row['rejection_reason']) : 
                                                '') . 
                                            "</td>";
                                        
                                        if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'teacher') {
                                            echo "<td>
                                                    <button onclick='editMeeting(" . json_encode($row) . ")' class='btn btn-sm btn-primary'>Edit</button>
                                                    <a href='../actions/delete_meeting.php?id=" . $row['id'] . "' class='btn btn-sm btn-danger' 
                                                       onclick='return confirm(\"Are you sure you want to delete this meeting?\")'>Delete</a>
                                                 </td>";
                                        } elseif(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'parent') {
                                            echo "<td>
                                                    <button onclick='acceptMeeting(" . json_encode($row) . ")' class='btn btn-sm btn-success'>Accept</button>
                                                    <button onclick='rejectMeeting(" . json_encode($row) . ")' class='btn btn-sm btn-danger'>Reject</button>
                                                 </td>";
                                        }
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><br><br><br><br>

    <footer class="footer mt-auto py-3 bg-light">
        <div class="container text-center">
            <p>&copy; 2025 Parent-Teacher Meeting Management System</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editMeeting(meeting) {
            document.getElementById('edit-meeting-id').value = meeting.id;
            document.getElementById('edit-parent-name').value = meeting.parent_name;
            document.getElementById('edit-student-name').value = meeting.student_name;
            document.getElementById('edit-subject').value = meeting.subject;
            document.getElementById('edit-meeting-date').value = meeting.meeting_date;
            document.getElementById('edit-meeting-time').value = meeting.meeting_time;
            document.getElementById('edit-status').value = meeting.status;

            var editMeetingModal = new bootstrap.Modal(document.getElementById('editMeetingModal'));
            editMeetingModal.show();
        }
        
        function acceptMeeting(meeting) {
            console.log('Attempting to accept meeting:', meeting);
            if (confirm("Are you sure you want to accept this meeting?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "../actions/accept_meeting.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                
                // Ensure we're sending the numeric ID
                var formData = "id=" + encodeURIComponent(meeting.id);
                
                xhr.onload = function() {
                    console.log('Accept meeting response:', xhr.responseText);
                    // Reload the page to reflect changes
                    window.location.reload();
                };
                
                xhr.onerror = function() {
                    console.error('Error in accept meeting request');
                };
                
                xhr.send(formData);
            }
        }
        
        function rejectMeeting(meeting) {
            var rejectionModal = new bootstrap.Modal(document.getElementById('rejectionReasonModal'));
            document.getElementById('rejectionMeetingId').value = meeting.id;
            rejectionModal.show();
        }
    </script>

    <!-- Edit Meeting Modal -->
    <div class="modal fade" id="editMeetingModal" tabindex="-1" aria-labelledby="editMeetingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMeetingModalLabel">Edit Meeting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="../actions/update_meeting.php">
                    <div class="modal-body">
                        <input type="hidden" id="edit-meeting-id" name="id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit-parent-name" class="form-label">Parent Name</label>
                                <input type="text" class="form-control" id="edit-parent-name" name="parent_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit-student-name" class="form-label">Student Name</label>
                                <input type="text" class="form-control" id="edit-student-name" name="student_name" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit-subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="edit-subject" name="subject" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit-meeting-date" class="form-label">Meeting Date</label>
                                <input type="date" class="form-control" id="edit-meeting-date" name="meeting_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit-meeting-time" class="form-label">Meeting Time</label>
                                <input type="time" class="form-control" id="edit-meeting-time" name="meeting_time" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit-status" class="form-label">Status</label>
                            <select class="form-control" id="edit-status" name="status">
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Meeting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rejection Reason Modal -->
    <div class="modal fade" id="rejectionReasonModal" tabindex="-1" aria-labelledby="rejectionReasonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectionReasonModalLabel">Reason for Rejecting Meeting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rejectionReasonForm" action="../actions/reject_meeting.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="rejectionMeetingId">
                        
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Reason for Rejection</label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Submit Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
