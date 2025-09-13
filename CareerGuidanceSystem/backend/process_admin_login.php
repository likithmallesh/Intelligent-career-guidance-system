<?php
// backend/process_admin_login.php
session_start();
require_once '../config/config.php'; // Path to your database config file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Server-side validation
    if (empty($email) || empty($password)) {
        $_SESSION['admin_login_error'] = "Please enter email and password.";
        header("Location: ../admin_login.php");
        exit();
    }

    // Query to specifically check for an admin user
    $sql = "SELECT id, name, email, password, role FROM users WHERE email = ? AND role = 'admin'";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $email;

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $name, $email, $hashed_password, $role);
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct and user is an admin, start a new session
                        session_regenerate_id(true); // Regenerate session ID for security
                        $_SESSION['user_id'] = $id;
                        $_SESSION['user_name'] = $name;
                        $_SESSION['user_email'] = $email;
                        $_SESSION['role'] = $role; // This will be 'admin'

                        header("Location: ../admin/dashboard.php");
                        exit();
                    } else {
                        // Password is not valid
                        $_SESSION['admin_login_error'] = "Invalid password.";
                    }
                }
            } else {
                // Email not found or not an admin
                $_SESSION['admin_login_error'] = "No admin account found with that email or invalid credentials.";
            }
        } else {
            $_SESSION['admin_login_error'] = "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);

    header("Location: ../admin_login.php");
    exit();
} else {
    // If accessed directly without POST
    header("Location: ../admin_login.php");
    exit();
}
?>
