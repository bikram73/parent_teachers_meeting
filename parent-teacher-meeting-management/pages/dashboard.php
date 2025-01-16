<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to index page if not logged in
    header("Location: /parent-teacher-meeting-management/index.php");
    exit();
}

// Include database connection
include '../config/db.php';

// Get user details based on role
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
    <title>Dashboard - PTM System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="meetings.php">Meetings</a>
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
            <h2 class="mb-4">Dashboard</h2>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h4>
                        </div>
                        <div class="card-body">
                            <h5>Your Details:</h5>
                            <ul>
                                <li>Name: <?php echo htmlspecialchars($user['name']); ?></li>
                                <li>Email: <?php echo htmlspecialchars($user['email']); ?></li>
                                <li>Role: <?php echo ucfirst($_SESSION['user_role']); ?></li>
                                <?php if($_SESSION['user_role'] === 'teacher'): ?>
                                    <li>Subject: <?php echo htmlspecialchars($user['subject']); ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-light text-center py-3 mt-5">
        <p>&copy; 2025 Parent-Teacher Meeting Management System</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
