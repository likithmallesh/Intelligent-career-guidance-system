<?php
// backend/process_registration.php
session_start();
require_once '../config/config.php'; // Path to your database config file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'user'; // Default role for new registrations

    // Server-side validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['registration_error'] = "All fields are required.";
        header("Location: ../register.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['registration_error'] = "Invalid email format.";
        header("Location: ../register.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['registration_error'] = "Passwords do not match.";
        header("Location: ../register.php");
        exit();
    }

    if (strlen($password) < 6) {
        $_SESSION['registration_error'] = "Password must be at least 6 characters long.";
        header("Location: ../register.php");
        exit();
    }

    // Check if email already exists
    $sql_check_email = "SELECT id FROM users WHERE email = ?";
    if ($stmt = mysqli_prepare($conn, $sql_check_email)) {
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $email;
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                $_SESSION['registration_error'] = "This email is already registered.";
                header("Location: ../register.php");
                exit();
            }
        } else {
            $_SESSION['registration_error'] = "Oops! Something went wrong. Please try again later.";
            header("Location: ../register.php");
            exit();
        }
        mysqli_stmt_close($stmt);
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $sql_insert_user = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql_insert_user)) {
        mysqli_stmt_bind_param($stmt, "ssss", $param_name, $param_email, $param_password, $param_role);
        $param_name = $name;
        $param_email = $email;
        $param_password = $hashed_password;
        $param_role = $role;

        if (mysqli_stmt_execute($stmt)) {
            // Registration successful, redirect to login page
            $_SESSION['registration_success'] = "Registration successful! Please log in.";
            header("Location: ../login.php");
            exit();
        } else {
            $_SESSION['registration_error'] = "Error: Could not register user. " . mysqli_error($conn);
            header("Location: ../register.php");
            exit();
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
} else {
    // If accessed directly without POST
    header("Location: ../register.php");
    exit();
}
?>
