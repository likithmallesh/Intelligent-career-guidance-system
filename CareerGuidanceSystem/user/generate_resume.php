<?php
// user/generate_resume.php
session_start();
require_once '../config/config.php';

// Check if user is logged in and is a regular user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's basic details including all new fields for resume
$user_details = [];
$sql_user_details = "SELECT name, email, major, gpa, experience_summary, projects_summary, phone_number, linkedin_url, summary_text, university_name, graduation_year FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql_user_details)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_details = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} else {
    // Log error if statement preparation fails
    error_log("Failed to prepare user details fetch statement for resume: " . mysqli_error($conn));
    // Provide default empty values to prevent errors on page load
    $user_details = [
        'name' => '', 'email' => '', 'major' => '', 'gpa' => '',
        'experience_summary' => '', 'projects_summary' => '',
        'phone_number' => '', 'linkedin_url' => '', 'summary_text' => '',
        'university_name' => '', 'graduation_year' => ''
    ];
}


// Fetch user's selected skills
$user_skills = [];
$sql_user_skills = "SELECT s.name FROM user_skills us JOIN skills s ON us.skill_id = s.id WHERE us.user_id = ?";
if ($stmt = mysqli_prepare($conn, $sql_user_skills)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $user_skills[] = $row['name'];
    }
    mysqli_stmt_close($stmt);
} else {
    error_log("Failed to prepare user skills fetch statement for resume: " . mysqli_error($conn));
}


// Assign fetched data to variables, with default placeholders if empty or not set
$user_major = htmlspecialchars($user_details['major'] ?? 'Not specified');
$user_gpa = htmlspecialchars($user_details['gpa'] ?? 'Not specified');
$experience_summary = htmlspecialchars($user_details['experience_summary'] ?? '');
$projects_summary = htmlspecialchars($user_details['projects_summary'] ?? '');
$phone_number = htmlspecialchars($user_details['phone_number'] ?? '');
$linkedin_url = htmlspecialchars($user_details['linkedin_url'] ?? '');
$summary_text = htmlspecialchars($user_details['summary_text'] ?? '');
$university_name = htmlspecialchars($user_details['university_name'] ?? 'Your University Name');
$graduation_year = htmlspecialchars($user_details['graduation_year'] ?? '[Graduation Year]');

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Resume - <?php echo htmlspecialchars($user_details['name'] ?? 'User'); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background-color: #f4f4f4; }
        .resume-container {
            max-width: 850px;
            margin: 30px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .resume-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        .resume-header h1 {
            margin: 0;
            color: #007bff;
            font-size: 2.5em;
        }
        .resume-header p {
            margin: 5px 0;
            font-size: 1.1em;
            color: #555;
        }
        .resume-section {
            margin-bottom: 25px;
        }
        .resume-section h2 {
            background-color: #f0f0f0;
            color: #007bff;
            padding: 10px 15px;
            margin-top: 0;
            margin-bottom: 15px;
            border-left: 5px solid #007bff;
            font-size: 1.5em;
        }
        .resume-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .resume-section ul li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }
        .resume-section ul li::before {
            content: '•';
            color: #007bff;
            font-weight: bold;
            display: inline-block;
            width: 1em;
            margin-left: -1em;
            position: absolute;
            left: 0;
        }
        .skills-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .skill-tag {
            background-color: #e9f7ff;
            color: #007bff;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9em;
            border: 1px solid #cceeff;
        }
        .back-link { display: block; text-align: center; margin-top: 30px; }
        .back-link a {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .back-link a:hover { background-color: #5a6268; }
        .print-button {
            text-align: center;
            margin-top: 20px;
        }
        .print-button button {
            background-color: #28a745;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            transition: background-color 0.3s ease;
        }
        .print-button button:hover {
            background-color: #218838;
        }
        .summary-text {
            white-space: pre-wrap; /* Preserves whitespace and line breaks */
        }
        @media print {
            .back-link, .print-button, .sidebar, .navbar {
                display: none; /* Hide navigation and buttons when printing */
            }
            body {
                background-color: #fff;
                margin: 0;
                padding: 0;
            }
            .resume-container {
                box-shadow: none;
                border: none;
                margin: 0;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <div class="resume-container">
        <div class="resume-header">
            <h1><?php echo htmlspecialchars($user_details['name'] ?? 'Your Name'); ?></h1>
            <p>
                <?php echo htmlspecialchars($user_details['email'] ?? 'your.email@example.com'); ?>
                <?php if (!empty($phone_number)): ?>
                    | <?php echo $phone_number; ?>
                <?php endif; ?>
                <?php if (!empty($linkedin_url)): ?>
                    | <a href="<?php echo $linkedin_url; ?>" target="_blank"><?php echo $linkedin_url; ?></a>
                <?php endif; ?>
            </p>
        </div>

        <div class="resume-section">
            <h2>Summary</h2>
            <?php if (!empty($summary_text)): ?>
                <p class="summary-text"><?php echo nl2br($summary_text); ?></p>
            <?php else: ?>
                <p>A dedicated and enthusiastic individual with a passion for [mention relevant field, e.g., web development/data analysis]. Possessing a strong foundation in [mention 2-3 key skills] and eager to apply learned knowledge to real-world challenges. Highly motivated to learn and grow in a dynamic professional environment.</p>
            <?php endif; ?>
        </div>

        <div class="resume-section">
            <h2>Education</h2>
            <ul>
                <?php
                // Check if any education detail is provided before showing the section
                if (!empty($university_name) && $university_name !== 'Your University Name' ||
                    !empty($user_major) && $user_major !== 'Not specified' ||
                    (!empty($user_gpa) && $user_gpa !== 'Not specified' && $user_gpa !== '') ||
                    !empty($graduation_year) && $graduation_year !== '[Graduation Year]') :
                ?>
                    <li>
                        <strong><?php echo $university_name; ?></strong>
                        <?php if (!empty($user_major) && $user_major !== 'Not specified'): ?>
                            - <?php echo $user_major; ?>
                        <?php endif; ?>
                    </li>
                    <?php if (!empty($user_gpa) && $user_gpa !== 'Not specified' && $user_gpa !== ''): ?>
                        <li>GPA: <?php echo $user_gpa; ?> (on 4.0 scale)</li>
                    <?php endif; ?>
                    <?php if (!empty($graduation_year) && $graduation_year !== '[Graduation Year]'): ?>
                        <li><?php echo $graduation_year; ?></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li>No education details added yet. Please update your profile.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="resume-section">
            <h2>Skills</h2>
            <?php if (!empty($user_skills)): ?>
                <div class="skills-list">
                    <?php foreach ($user_skills as $skill): ?>
                        <span class="skill-tag"><?php echo htmlspecialchars($skill); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No skills added yet. Please update your profile.</p>
            <?php endif; ?>
        </div>

        <div class="resume-section">
            <h2>Experience</h2>
            <?php if (!empty($experience_summary)): ?>
                <p class="summary-text"><?php echo nl2br($experience_summary); ?></p>
            <?php else: ?>
                <p>No experience details added yet. Please update your profile.</p>
            <?php endif; ?>
        </div>

        <div class="resume-section">
            <h2>Projects</h2>
            <?php if (!empty($projects_summary)): ?>
                <p class="summary-text"><?php echo nl2br($projects_summary); ?></p>
            <?php else: ?>
                <p>No project details added yet. Please update your profile.</p>
            <?php endif; ?>
        </div>

        <div class="print-button">
            <button onclick="window.print()">Print/Save as PDF</button>
        </div>

        <div class="back-link">
            <p><a href="dashboard.php">Back to Dashboard</a></p>
        </div>
    </div>
</body>
</html>

