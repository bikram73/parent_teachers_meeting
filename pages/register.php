<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = trim($_POST['role']);
    $phone = trim($_POST['phone']);
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $signup_code = isset($_POST['signup_code']) ? trim($_POST['signup_code']) : '';
    
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
            if ($signup_code === '') {
                $error = "Teacher signup code is required";
            } else {
                $code_sql = "SELECT id FROM teacher_invite_codes WHERE code = ? AND is_used = FALSE";
                $code_stmt = $conn->prepare($code_sql);
                $code_stmt->bind_param("s", $signup_code);
                $code_stmt->execute();
                $code_result = $code_stmt->get_result();

                if ($code_result === false || $code_result->num_rows === 0) {
                    $error = "Invalid or already used teacher signup code";
                } else {
                    $conn->begin_transaction();

                    try {
                        $sql = "INSERT INTO teacher_users (name, email, password, phone, subject, signup_code) VALUES (?, ?, ?, ?, ?, ?) RETURNING id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssssss", $name, $email, $password, $phone, $subject, $signup_code);

                        if (!$stmt->execute()) {
                            throw new Exception($stmt->error ?: 'Failed to register teacher');
                        }

                        $insert_result = $stmt->get_result();
                        $insert_row = $insert_result ? $insert_result->fetch_assoc() : null;
                        $teacher_user_id = isset($insert_row['id']) ? (int)$insert_row['id'] : 0;

                        if ($teacher_user_id <= 0) {
                            throw new Exception('Failed to retrieve teacher account ID');
                        }

                        $update_code_sql = "UPDATE teacher_invite_codes SET is_used = TRUE, teacher_user_id = ?, used_at = NOW() WHERE code = ?";
                        $update_stmt = $conn->prepare($update_code_sql);
                        $update_stmt->bind_param("is", $teacher_user_id, $signup_code);

                        if (!$update_stmt->execute()) {
                            throw new Exception($update_stmt->error ?: 'Failed to mark signup code as used');
                        }

                        $conn->commit();
                        $_SESSION['success'] = "Registration successful! Please login.";
                        header("Location: login.php");
                        exit();
                    } catch (Throwable $exception) {
                        $conn->rollback();
                        $error = "Registration failed: " . $exception->getMessage();
                    }
                }
            }
        } else {
            $sql = "INSERT INTO parent_users (name, email, password, phone) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $name, $email, $password, $phone);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Registration successful! Please login.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Registration failed: " . $conn->error;
            }
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
                        <div class="mb-3" id="signupCodeField" style="display: none;">
                            <label for="signup_code" class="form-label">Teacher Signup Code</label>
                            <input type="text" class="form-control" id="signup_code" name="signup_code" value="<?php echo isset($_POST['signup_code']) ? htmlspecialchars($_POST['signup_code']) : ''; ?>">
                            <small class="text-muted">Required for teacher accounts only.</small>
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
            const signupCodeField = document.getElementById('signupCodeField');
            const signupCodeInput = document.getElementById('signup_code');
            if (role === 'teacher') {
                subjectField.style.display = 'block';
                signupCodeField.style.display = 'block';
                document.getElementById('subject').required = true;
                signupCodeInput.required = true;
            } else {
                subjectField.style.display = 'none';
                signupCodeField.style.display = 'none';
                document.getElementById('subject').required = false;
                signupCodeInput.required = false;
            }
        }
        // Call toggleSubject on page load to set initial state
        document.addEventListener('DOMContentLoaded', function() {
            toggleSubject();
        });
    </script>
</body>
</html>
