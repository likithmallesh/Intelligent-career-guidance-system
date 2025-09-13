<?php
// index.php (UPDATED)
session_start();

// Check if user is already logged in and redirect to their respective dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Guidance System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #e2eefd, #f6f9fc); /* Soft gradient background */
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .header {
            background-color: #007bff;
            color: white; /* This sets the default text color for the header */
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .header h1 {
            margin: 0;
            font-size: 2.8em;
            letter-spacing: 1px;
            color: white; /* Explicitly setting H1 color to white */
        }

        .header p {
            margin: 5px 0 0;
            font-size: 1.2em;
            opacity: 0.9;
        }

        .navbar {
            background-color: #0056b3; /* Darker blue for nav */
            padding: 10px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }

        .navbar ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }

        .navbar ul li {
            margin: 0 15px;
        }

        .navbar ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1em;
            padding: 5px 0;
            transition: color 0.3s ease;
        }

        .navbar ul li a:hover {
            color: #cceeff; /* Lighter blue on hover */
        }

        .main-content {
            flex-grow: 1; /* Allows content to take up available space */
            padding: 40px 20px;
            max-width: 960px;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            text-align: center;
        }

        .main-content h2 {
            color: #007bff;
            font-size: 2.2em;
            margin-bottom: 25px;
        }

        .main-content p {
            font-size: 1.1em;
            margin-bottom: 20px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .feature-item {
            background-color: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            text-align: left;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .feature-item h3 {
            color: #0056b3;
            margin-top: 0;
            font-size: 1.4em;
        }

        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>Welcome to Career Guidance System</h1>
        <p>Your Path to a Brighter Future</p>
    </header>

    <nav class="navbar">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">User Login</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="admin_login.php">Admin Login</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <h2>Discover Your Ideal Career!</h2>
        <p>
            Our Career Guidance System helps you explore career paths, enhance your skills, and predict your potential for success.
            Take our quiz, list your skills, and let our intelligent algorithms guide you.
        </p>

        <div class="features-grid">
            <div class="feature-item">
                <h3>Personalized Recommendations</h3>
                <p>Get tailored career suggestions based on your unique skills, interests, and quiz answers.</p>
            </div>
            <div class="feature-item">
                <h3>Skill Enhancement</h3>
                <p>Identify complementary skills to learn, boosting your profile for your desired career.</p>
            </div>
            <div class="feature-item">
                <h3>Success Prediction</h3>
                <p>See a compatibility score for various careers, helping you choose with confidence.</p>
            </div>
            <div class="feature-item">
                <h3>Instant Resume Generation</h3>
                <p>Generate a basic resume with your profile information and skills in just a few clicks.</p>
            </div>
        </div>

        <p style="margin-top: 40px;">
            Ready to start your journey? <a href="register.php" style="color: #007bff; font-weight: bold; text-decoration: none;">Register now</a> or <a href="login.php" style="color: #007bff; font-weight: bold; text-decoration: none;">Login</a> if you already have an account.
        </p>
    </main>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Career Guidance System. All rights reserved.</p>
    </footer>
</body>
</html>

