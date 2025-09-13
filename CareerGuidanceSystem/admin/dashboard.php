<?php
// admin/dashboard.php
session_start(); // Start the session at the very beginning of the script
require_once '../config/config.php'; // Include your database configuration file

// Check if user is logged in and is an administrator
// If not logged in or if the role is not 'admin', redirect to the admin login page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../admin_login.php'); // Redirect to the new separate admin login page
    exit(); // Always exit after a header redirect
}

// User is authenticated and has the 'admin' role, so we can display their dashboard.
$admin_name = htmlspecialchars($_SESSION['user_name']); // Get admin's name from session for display
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Career Guidance System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Specific styles for the admin dashboard */
        body {
            background-color: #e9ecef; /* Slightly different background for admin area to distinguish */
        }
        .admin-dashboard-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px; /* More rounded corners */
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); /* Stronger shadow */
            text-align: center;
        }
        .admin-dashboard-container h1 {
            color: #dc3545; /* Red color for admin distinction (e.g., danger/management) */
            margin-bottom: 25px;
            font-size: 2.2em;
        }
        .welcome-message {
            font-size: 1.3em;
            color: #555;
            margin-bottom: 30px;
        }
        .admin-nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap; /* Allow items to wrap on smaller screens */
            justify-content: center;
            gap: 25px; /* Space between navigation items */
            margin-bottom: 40px;
        }
        .admin-nav ul li {
            flex-basis: 200px; /* Minimum width for each item */
            flex-grow: 1; /* Allow items to grow */
        }
        .admin-nav ul li a {
            background-color: #dc3545; /* Red buttons for admin actions */
            color: white;
            padding: 15px 20px;
            border-radius: 8px; /* Rounded buttons */
            text-decoration: none;
            font-size: 1.1em;
            font-weight: bold;
            display: block; /* Make the whole area clickable */
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .admin-nav ul li a:hover {
            background-color: #c82333; /* Darker red on hover */
            transform: translateY(-3px); /* Slight lift effect on hover */
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .info-text {
            font-size: 1em;
            color: #777;
            margin-top: 30px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .admin-nav ul {
                flex-direction: column; /* Stack items vertically on small screens */
                align-items: center;
            }
            .admin-nav ul li {
                width: 80%; /* Make buttons wider on small screens */
            }
        }
    </style>
</head>
<body>
    <div class="admin-dashboard-container">
        <h1>Admin Dashboard</h1>
        <p class="welcome-message">Welcome, <?php echo $admin_name; ?>! Manage system data here.</p>

        <nav class="admin-nav">
            <ul>
                <li><a href="manage_careers.php">Manage Careers</a></li>
                <li><a href="manage_skills.php">Manage Skills</a></li>
                <li><a href="manage_users.php">Manage Users</a></li> <!-- Link to the user management page -->
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>

        <p class="info-text">
            Use the navigation above to add, edit, or delete careers and skills in the system, or manage user accounts.
        </p>
    </div>
</body>
</html>

