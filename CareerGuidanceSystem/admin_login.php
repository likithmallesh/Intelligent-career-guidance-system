<?php
// admin_login.php
session_start();

// If an admin is already logged in, redirect them to the admin dashboard
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}
// If a regular user is logged in, redirect them to the user dashboard (or logout first)
// For simplicity, we'll just redirect them to their dashboard if they try to access admin login
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user') {
    header('Location: user/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Career Guidance System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .login-container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 350px; text-align: center; }
        .login-container h2 { margin-bottom: 20px; color: #dc3545; /* Admin color */ }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-group .error { color: red; font-size: 0.9em; margin-top: 5px; }
        .btn {
            background-color: #dc3545; /* Admin button color */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }
        .btn:hover { background-color: #c82333; }
        .link-text { margin-top: 20px; font-size: 0.9em; }
        .link-text a { color: #007bff; text-decoration: none; }
        .link-text a:hover { text-decoration: underline; }
        .success { color: green; font-size: 0.9em; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php
        if (isset($_SESSION['admin_login_error'])) {
            echo '<p class="error">' . htmlspecialchars($_SESSION['admin_login_error']) . '</p>';
            unset($_SESSION['admin_login_error']);
        }
        ?>
        <form action="backend/process_admin_login.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login as Admin</button>
        </form>
        <p class="link-text">Are you a regular user? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>
