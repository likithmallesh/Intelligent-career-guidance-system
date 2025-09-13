<?php
// backend/process_login.php
session_start();
require_once '../config/config.php'; // Path to your database config file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Server-side validation
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Please enter email and password.";
        header("Location: ../login.php");
        exit();
    }

    $sql = "SELECT id, name, email, password, role FROM users WHERE email = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $email;

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $name, $email, $hashed_password, $role);
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start a new session
                        session_regenerate_id(true); // Regenerate session ID for security
                        $_SESSION['user_id'] = $id;
                        $_SESSION['user_name'] = $name;
                        $_SESSION['user_email'] = $email;
                        $_SESSION['role'] = $role;

                        // Redirect user based on role
                        if ($role == 'admin') {
                            header("Location: ../admin/dashboard.php");
                        } else {
                            header("Location: ../user/dashboard.php");
                        }
                        exit();
                    } else {
                        // Password is not valid
                        $_SESSION['login_error'] = "Invalid password.";
                    }
                }
            } else {
                // Email not found
                $_SESSION['login_error'] = "No account found with that email.";
            }
        } else {
            $_SESSION['login_error'] = "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);

    header("Location: ../login.php");
    exit();
} else {
    // If accessed directly without POST
    header("Location: ../login.php");
    exit();
}
?>
