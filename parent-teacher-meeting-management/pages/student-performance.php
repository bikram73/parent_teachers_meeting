<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: /parent-teacher-meeting-management/index.php");
    exit();
}

include '../config/db.php';

// Debug database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions for teachers only
if ($_SESSION['user_role'] === 'teacher' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $usn = $conn->real_escape_string($_POST['usn']);
                $student_name = $conn->real_escape_string($_POST['student_name']);
                $parent_name = $conn->real_escape_string($_POST['parent_name']);
                $marks = floatval($_POST['marks']);
                $subject = $conn->real_escape_string($_POST['subject']);

                $sql = "INSERT INTO student_performance (usn, student_name, parent_name, marks, subject) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("sssds", $usn, $student_name, $parent_name, $marks, $subject);
                if (!$stmt->execute()) {
                    die("Execute failed: " . $stmt->error);
                }
                header("Location: student-performance.php");
                exit();
                break;

            case 'delete':
                $id = intval($_POST['id']);
                $sql = "DELETE FROM student_performance WHERE id = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("i", $id);
                if (!$stmt->execute()) {
                    die("Execute failed: " . $stmt->error);
                }
                header("Location: student-performance.php");
                exit();
                break;

            case 'update':
                $id = intval($_POST['id']);
                $usn = $conn->real_escape_string($_POST['usn']);
                $student_name = $conn->real_escape_string($_POST['student_name']);
                $parent_name = $conn->real_escape_string($_POST['parent_name']);
                $marks = floatval($_POST['marks']);
                $subject = $conn->real_escape_string($_POST['subject']);

                $sql = "UPDATE student_performance 
                        SET usn = ?, student_name = ?, parent_name = ?, marks = ?, subject = ? 
                        WHERE id = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("sssdsi", $usn, $student_name, $parent_name, $marks, $subject, $id);
                if (!$stmt->execute()) {
                    die("Execute failed: " . $stmt->error);
                }
                header("Location: student-performance.php");
                exit();
                break;
        }
    }
}

// Fetch records based on user role
if ($_SESSION['user_role'] === 'parent') {
    $parent_name = $conn->real_escape_string($_SESSION['user_name']);
    $sql = "SELECT * FROM student_performance WHERE parent_name = ? ORDER BY usn ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $parent_name);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM student_performance ORDER BY usn ASC";
    $result = $conn->query($sql);
}

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Performance - PTM System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard-page">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">PTM System</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <?php if ($_SESSION['user_role'] === 'teacher'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="meetings.php">Meetings</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['user_role'] === 'parent'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="meetings.php">Meetings</a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link active" href="student-performance.php">Student Performance</a>
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
            <h2 class="mb-4">Student Performance Records</h2>
            
            <div class="alert alert-info">
                Number of records: <?php echo $result->num_rows; ?>
            </div>
            
            <?php if($_SESSION['user_role'] === 'parent' && $result->num_rows === 0): ?>
            <div class="alert alert-warning">
                No performance records found for your student(s).
            </div>
            <?php endif; ?>
            
            <?php if($_SESSION['user_role'] === 'teacher'): ?>
            <!-- Teacher Form - Only show to teachers -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Add Student Performance</h5>
                    <form method="POST">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="usn" class="form-label">USN</label>
                                <input type="text" class="form-control" id="usn" name="usn" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="student_name" class="form-label">Student Name</label>
                                <input type="text" class="form-control" id="student_name" name="student_name" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="parent_name" class="form-label">Parent Name</label>
                                <input type="text" class="form-control" id="parent_name" name="parent_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="marks" class="form-label">Marks</label>
                                <input type="number" step="0.01" class="form-control" id="marks" name="marks" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Add Record</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Performance Records Table -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Student Performance Records</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>USN</th>
                                    <th>Student Name</th>
                                    <th>Parent Name</th>
                                    <th>Subject</th>
                                    <th>Marks</th>
                                    <?php if($_SESSION['user_role'] === 'teacher'): ?>
                                    <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['usn']); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['parent_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($row['marks']); ?></td>
                                    <?php if($_SESSION['user_role'] === 'teacher'): ?>
                                    <td>
                                        <button onclick="openEditModal(
                                            '<?php echo htmlspecialchars($row['id']); ?>',
                                            '<?php echo htmlspecialchars($row['usn']); ?>',
                                            '<?php echo htmlspecialchars($row['student_name']); ?>',
                                            '<?php echo htmlspecialchars($row['parent_name']); ?>',
                                            '<?php echo htmlspecialchars($row['subject']); ?>',
                                            '<?php echo htmlspecialchars($row['marks']); ?>'
                                        )" class="btn btn-sm btn-success">Edit</button>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div><br><br><br><br>

    <!-- Modal for editing student performance records -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Student Performance Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <div class="modal-body">
                        <input type="hidden" id="edit-record-id" name="id">
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="edit-usn" class="form-label">USN</label>
                                <input type="text" class="form-control" id="edit-usn" name="usn" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="edit-student-name" class="form-label">Student Name</label>
                                <input type="text" class="form-control" id="edit-student-name" name="student_name" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="edit-parent-name" class="form-label">Parent Name</label>
                                <input type="text" class="form-control" id="edit-parent-name" name="parent_name" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="edit-subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="edit-subject" name="subject" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="edit-marks" class="form-label">Marks</label>
                                <input type="number" step="0.01" class="form-control" id="edit-marks" name="marks" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer mt-auto py-3 bg-light">
        <div class="container text-center">
            <p>&copy; 2025 Parent-Teacher Meeting Management System</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function openEditModal(id, usn, studentName, parentName, subject, marks) {
        // Populate form with record details
        document.getElementById('edit-record-id').value = id;
        document.getElementById('edit-usn').value = usn;
        document.getElementById('edit-student-name').value = studentName;
        document.getElementById('edit-parent-name').value = parentName;
        document.getElementById('edit-subject').value = subject;
        document.getElementById('edit-marks').value = marks;
        
        // Show the modal
        var modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
    }
    </script>
</body>
</html>
