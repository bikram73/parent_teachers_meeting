<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $conn->real_escape_string($_POST['role']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $subject = isset($_POST['subject']) ? $conn->real_escape_string($_POST['subject']) : '';
    
    // Get the appropriate table name
    $table = $role === 'parent' ? 'parent_users' : 'teacher_users';
    
    // Check if email already exists
    $check_sql = "SELECT id FROM $table WHERE email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result === false) {
        $error = "Database error: " . $conn->error;
    } elseif ($result->num_rows > 0) {
        $error = "Email already registered";
    } else {
        if ($role === 'teacher') {
            $sql = "INSERT INTO $table (name, email, password, phone, subject) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $name, $email, $password, $phone, $subject);
        } else {
            $sql = "INSERT INTO $table (name, email, password, phone) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $name, $email, $password, $phone);
        }
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Registration successful! Please login.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Registration failed: " . $conn->error;
        }
    }
}

// Get role from URL parameter if available
$selected_role = isset($_GET['role']) ? $_GET['role'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PTM System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard-page">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">PTM System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="auth-form">
                    <h2 class="text-center mb-4">Register</h2>
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control" id="role" name="role" required onchange="toggleSubject()">
                                <option value="">Select Role</option>
                                <option value="parent" <?php echo $selected_role === 'parent' ? 'selected' : ''; ?>>Parent</option>
                                <option value="teacher" <?php echo $selected_role === 'teacher' ? 'selected' : ''; ?>>Teacher</option>
                            </select>
                        </div>
                        <div class="mb-3" id="subjectField" style="display: none;">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Register</button>
                        <a href="login.php" class="btn btn-link">Already have an account? Login</a>
                    </form>
                </div>
            </div>
        </div>
    </div><br><br><br><br>

    <footer class="bg-light text-center py-3 mt-5">
        <p>&copy; 2025 Parent-Teacher Meeting Management System</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSubject() {
            const role = document.getElementById('role').value;
            const subjectField = document.getElementById('subjectField');
            if (role === 'teacher') {
                subjectField.style.display = 'block';
                document.getElementById('subject').required = true;
            } else {
                subjectField.style.display = 'none';
                document.getElementById('subject').required = false;
            }
        }
        // Call toggleSubject on page load to set initial state
        document.addEventListener('DOMContentLoaded', function() {
            toggleSubject();
        });
    </script>
</body>
</html>
