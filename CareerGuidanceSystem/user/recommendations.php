<?php
session_start();
require_once '../config/config.php';
require_once '../algorithms/career_scoring.php';
require_once '../algorithms/association_rule_mining.php';
require_once '../algorithms/linear_regression.php';
require_once '../algorithms/course_recommendation.php';

// --- Auth check ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// --- Fetch Data ---
$user_skills_ids = [];
$sql_user_skills = "SELECT skill_id FROM user_skills WHERE user_id = ?";
if ($stmt = mysqli_prepare($conn, $sql_user_skills)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $user_skills_ids[] = $row['skill_id'];
    }
    mysqli_stmt_close($stmt);
}

$user_answers = [];
$sql_user_answers = "SELECT question, answer FROM user_answers WHERE user_id = ?";
if ($stmt = mysqli_prepare($conn, $sql_user_answers)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $user_answers[$row['question']] = $row['answer'];
    }
    mysqli_stmt_close($stmt);
}

$user_major = null;
$user_gpa = null;
$sql_user_academic = "SELECT major, gpa FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql_user_academic)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $fetched_major, $fetched_gpa);
    if (mysqli_stmt_fetch($stmt)) {
        $user_major = $fetched_major;
        $user_gpa = $fetched_gpa;
    }
    mysqli_stmt_close($stmt);
}

// --- Fetch Skills and Careers ---
$all_skills = [];
$result_all_skills = mysqli_query($conn, "SELECT id, name FROM skills");
while ($row = mysqli_fetch_assoc($result_all_skills)) {
    $all_skills[] = $row;
}

$all_careers = [];
$result_all_careers = mysqli_query($conn, "SELECT id, title, description FROM careers");
while ($row = mysqli_fetch_assoc($result_all_careers)) {
    $all_careers[] = $row;
}

// --- Run Algorithms ---
$career_compatibility_scores = [];
$skill_suggestions = [];
$success_predictions = [];
$recommended_courses = [];

if (!empty($user_skills_ids) || !empty($user_answers) || $user_gpa !== null) {
    $career_compatibility_scores = getCareerCompatibilityScores($user_skills_ids, $user_answers, $all_careers, $all_skills);
    $skill_suggestions = getAssociationSkillSuggestions($user_skills_ids, $all_skills);

    foreach ($all_careers as $career) {
        $score = predictSuccessScore($user_skills_ids, $user_answers, $career, $all_skills, $user_gpa);
        $success_predictions[$career['title']] = $score;
    }

    // Convert skill_suggestions (names) to skill_ids
    $suggested_skill_ids = [];
    foreach ($skill_suggestions as $name) {
        foreach ($all_skills as $skill) {
            if (strtolower($skill['name']) === strtolower($name)) {
                $suggested_skill_ids[] = $skill['id'];
                break;
            }
        }
    }

    // Get Course Recommendations
    if (!empty($suggested_skill_ids)) {
        $recommended_courses = getCourseRecommendations($conn, $suggested_skill_ids);
    }

    // Store recommended careers in DB
    $sql_delete_recommendations = "DELETE FROM recommendations WHERE user_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql_delete_recommendations)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    $sql_insert = "INSERT INTO recommendations (user_id, career_id, success_score) VALUES (?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql_insert)) {
        foreach ($career_compatibility_scores as $career_title => $score) {
            if ($score > 25) {
                foreach ($all_careers as $c) {
                    if ($c['title'] === $career_title) {
                        $career_id = $c['id'];
                        $store_score = $success_predictions[$career_title] ?? $score;
                        mysqli_stmt_bind_param($stmt, "iid", $user_id, $career_id, $store_score);
                        mysqli_stmt_execute($stmt);
                        break;
                    }
                }
            }
        }
        mysqli_stmt_close($stmt);
    }

} else {
    $_SESSION['recommendation_message'] = "Please fill out your profile, skills, and quiz to get recommendations.";
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Recommendations</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 0;
        }

        .page-layout {
            display: flex;
            max-width: 1200px;
            margin: 40px auto;
            gap: 30px;
        }

        .left-content {
            flex: 2;
        }

        .right-sidebar {
            flex: 1;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        .right-sidebar h3 {
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .recommendations-container h1 {
            text-align: center;
            color: #0056b3;
        }

        .recommendation-section {
            background: #fff;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .recommendation-section h3 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .list-item {
            padding: 12px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
        }

        .score {
            color: #28a745;
            font-weight: bold;
        }

        .course-item {
            margin-bottom: 25px;
            padding: 15px;
            background: #f9fbff;
            border-left: 5px solid #007bff;
            border-radius: 8px;
        }

        .course-item strong {
            font-size: 1.1em;
        }

        .course-item p {
            margin: 8px 0;
            font-size: 0.95em;
            color: #333;
        }

        .course-item a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .course-item a:hover {
            text-decoration: underline;
        }

        .no-data-message {
            text-align: center;
            font-style: italic;
            color: #888;
        }

        .back-link {
            text-align: center;
            margin-top: 30px;
        }

        .back-link a {
            color: #007bff;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="page-layout">
    <div class="left-content">
        <div class="recommendations-container">
            <h1>My Career Recommendations</h1>

            <?php if (isset($_SESSION['recommendation_message'])): ?>
                <p class="no-data-message"><?php echo $_SESSION['recommendation_message']; unset($_SESSION['recommendation_message']); ?></p>
            <?php else: ?>

                <div class="recommendation-section">
                    <h3>Top Career Paths</h3>
                    <?php
                    $displayed = 0;
                    foreach ($career_compatibility_scores as $career => $score) {
                        if ($score > 25 && $displayed < 5) {
                            echo "<div class='list-item'><span><strong>" . htmlspecialchars($career) . "</strong></span><span class='score'>Score: " . round($score, 2) . "</span></div>";
                            $displayed++;
                        }
                    }
                    if ($displayed === 0) {
                        echo "<p class='no-data-message'>No strong career matches found.</p>";
                    }
                    ?>
                </div>

                <div class="recommendation-section">
                <h3>Skill Enhancement Suggestions (Association Rule Mining)</h3>
                <?php if (!empty($skill_suggestions)): ?>
                    <?php foreach ($skill_suggestions as $skill_name): ?>
                        <div class="list-item">
                            <span>Consider learning: <strong><?php echo htmlspecialchars($skill_name); ?></strong></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-data-message">No specific skill enhancement suggestions at this time based on your current skills.</p>
                <?php endif; ?>
            </div>

                <div class="recommendation-section">
                    <h3>Success Predictions (Linear Regression)</h3>
                    <?php
                    arsort($success_predictions);
                    foreach ($success_predictions as $title => $score) {
                        echo "<div class='list-item'><span><strong>" . htmlspecialchars($title) . "</strong></span><span class='score'>" . round($score, 2) . "%</span></div>";
                    }
                    ?>
                </div>

            <?php endif; ?>

            <div class="back-link">
                <a href="dashboard.php">← Back to Dashboard</a>
            </div>
        </div>
    </div>

    <!-- ✅ Course Recommendations Sidebar -->
    <div class="right-sidebar">
        <h3>Recommended Courses</h3>
        <?php if (!empty($recommended_courses)): ?>
            <?php foreach ($recommended_courses as $course): ?>
                <div class="course-item">
                    <strong><?php echo htmlspecialchars($course['title']); ?></strong>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                    <small><em>Skill: <?php echo $course['skill_name'] ?: 'General'; ?></em></small><br>
                    <a href="<?php echo htmlspecialchars($course['url']); ?>" target="_blank">View Course →</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-data-message">No course recommendations found.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
