<?php
// user/dashboard.php
session_start();
require_once '../config/config.php'; // Include your database configuration

// Check if user is logged in and is a regular user
// If not logged in or if the role is not 'user', redirect to the login page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit(); // Always exit after a header redirect
}

// User is authenticated and has the 'user' role, so we can display their dashboard.
$user_name = htmlspecialchars($_SESSION['user_name']); // Get user's name from session for display
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Career Guidance System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Specific styles for the user dashboard */
        body {
            background-color: #f8f9fa; /* Lighter background */
        }
        .dashboard-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px; /* More rounded corners */
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); /* Stronger shadow */
            text-align: center;
        }
        .dashboard-container h1 {
            color: #0056b3;
            margin-bottom: 25px;
            font-size: 2.2em;
        }
        .welcome-message {
            font-size: 1.3em;
            color: #555;
            margin-bottom: 30px;
        }
        .dashboard-nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap; /* Allow items to wrap on smaller screens */
            justify-content: center;
            gap: 25px; /* Space between navigation items */
            margin-bottom: 40px;
        }
        .dashboard-nav ul li {
            flex-basis: 200px; /* Minimum width for each item */
            flex-grow: 1; /* Allow items to grow */
        }
        .dashboard-nav ul li a {
            background-color: #007bff;
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
        .dashboard-nav ul li a:hover {
            background-color: #0056b3;
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
            .dashboard-nav ul {
                flex-direction: column; /* Stack items vertically on small screens */
                align-items: center;
            }
            .dashboard-nav ul li {
                width: 80%; /* Make buttons wider on small screens */
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>User Dashboard</h1>
        <p class="welcome-message">Welcome, <?php echo $user_name; ?>! Your personalized career journey starts here.</p>

        <nav class="dashboard-nav">
            <ul>
                <li><a href="profile.php">My Profile</a></li>
                <!-- The quiz is integrated into the profile page, so this link can be removed or repurposed if you add a separate quiz module later -->
                <!-- <li><a href="quiz.php">Take Quiz</a></li> -->
                <li><a href="recommendations.php">My Recommendations</a></li>
                <li><a href="generate_resume.php">Generate Resume</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>

        <p class="info-text">
            Use the navigation above to update your information, get career insights, or generate your resume.
        </p>
    </div>
</body>
</html>
