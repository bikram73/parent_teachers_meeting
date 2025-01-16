<?php
session_start();
include 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent-Teacher Meeting Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            z-index: 1;
            position: relative;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.95);
        }
        .card-header {
            background: linear-gradient(to right, #1e3c72, #2a5298);
            color: white;
            text-align: center;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
        }
        .btn-primary {
            background: linear-gradient(to right, #1e3c72, #2a5298);
            border: none;
            padding: 10px 20px;
            width: 100%;
        }
        .btn-primary:hover {
            background: linear-gradient(to right, #2a5298, #1e3c72);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .btn-success {
            background-color: #198754;
            border: none;
            padding: 10px 20px;
            width: 100%;
        }
        .btn-success:hover {
            background-color: #146c43;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
        }
        body.dashboard-page {
            background: linear-gradient(to right, #1e3c72, #2a5298);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
    </style>
</head>

<body class="dashboard-page">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">PTM System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h1 class="display-4 mb-4 welcome-text">Welcome to Parent-Teacher Meeting Management System</h1>
                <p class="lead mb-5 welcome-text">Streamline communication between parents and teachers for better student success</p>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card feature-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">For Parents</h5>
                        <p class="card-text">Schedule meetings with teachers and track your child's progress.</p>
                        <a href="pages/register.php?role=parent" class="btn btn-success">Register as Parent</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">For Teachers</h5>
                        <p class="card-text">Manage your meeting schedule and communicate with parents effectively.</p>
                        <a href="pages/register.php?role=teacher" class="btn btn-success">Register as Teacher</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Already Registered?</h5>
                        <p class="card-text">Login to access your dashboard and manage your meetings.</p>
                        <a href="pages/login.php" class="btn btn-success">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-light text-center py-3 mt-5">
        <p style="color: #000;">&copy; 2025 Parent-Teacher Meeting Management System</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
