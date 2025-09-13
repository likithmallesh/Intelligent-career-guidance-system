<?php
// user/profile.php
session_start();
require_once '../config/config.php';

// Check if user is logged in and is a regular user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch existing user details, including all new fields
$user_details = [];
$sql_user_details = "SELECT name, email, major, gpa, experience_summary, projects_summary, phone_number, linkedin_url, summary_text, university_name, graduation_year FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql_user_details)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_details = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Fetch all available skills
$all_skills = [];
$sql_all_skills = "SELECT id, name FROM skills ORDER BY name ASC";
$result_all_skills = mysqli_query($conn, $sql_all_skills);
if ($result_all_skills) {
    while ($row = mysqli_fetch_assoc($result_all_skills)) {
        $all_skills[] = $row;
    }
}

// Fetch user's currently selected skills
$user_selected_skills_ids = [];
$sql_user_skills = "SELECT skill_id FROM user_skills WHERE user_id = ?";
if ($stmt = mysqli_prepare($conn, $sql_user_skills)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $user_selected_skills_ids[] = $row['skill_id'];
    }
    mysqli_stmt_close($stmt);
}

// Fetch user's previous quiz answers (if any)
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

// Define quiz questions and options (as per previous version)
$quiz_questions = [
    // Section 1: Work Environment & Preferences
    [
        'question' => 'What is your ideal work environment?',
        'type' => 'radio',
        'name' => 'work_environment',
        'options' => [
            'Structured office setting with clear hierarchy and processes',
            'Flexible environment with autonomy, creativity, and less rigid structure',
            'Highly collaborative team-based environment with constant interaction',
            'Independent work, where I can focus deeply with minimal interruptions',
            'Fast-paced, high-pressure environment with tight deadlines'
        ]
    ],
    [
        'question' => 'How do you prefer to approach problem-solving?',
        'type' => 'radio',
        'name' => 'problem_solving_approach',
        'options' => [
            'Systematically breaking down complex problems into smaller parts',
            'Brainstorming creative and unconventional solutions',
            'Collaborating with others to find a consensus solution',
            'Experimenting and iterating quickly to find solutions'
        ]
    ],
    [
        'question' => 'What kind of tasks do you find most engaging?',
        'type' => 'checkbox',
        'name' => 'engaging_tasks',
        'options' => [
            'Analyzing data and identifying patterns',
            'Designing and creating visual interfaces or content',
            'Writing and debugging code',
            'Managing projects and coordinating teams',
            'Interacting directly with clients or users',
            'Researching new technologies and concepts',
            'Securing systems and preventing attacks'
        ]
    ],
    // Section 2: Learning & Growth
    [
        'question' => 'How do you prefer to learn new technologies or skills?',
        'type' => 'radio',
        'name' => 'learning_preference',
        'options' => [
            'Hands-on coding/building projects',
            'Reading documentation and academic papers',
            'Watching video tutorials and online courses',
            'Learning from mentors or experienced colleagues',
            'Attending workshops and conferences'
        ]
    ],
    [
        'question' => 'How comfortable are you with continuous learning and adapting to new tools?',
        'type' => 'radio',
        'name' => 'continuous_learning',
        'options' => [
            'Extremely comfortable, I thrive on new challenges',
            'Comfortable, I enjoy learning new things regularly',
            'Somewhat comfortable, I prefer stability but can adapt',
            'Not very comfortable, I prefer mastering a few tools'
        ]
    ],
    // Section 3: Interests & Aptitudes
    [
        'question' => 'Which of the following areas interests you most?',
        'type' => 'checkbox',
        'name' => 'interest_areas',
        'options' => [
            'Artificial Intelligence & Machine Learning',
            'Cloud Computing & DevOps',
            'Cybersecurity & Network Defense',
            'Web & Mobile Application Development',
            'Data Analysis & Business Intelligence',
            'Game Development & Interactive Media',
            'User Experience (UX) & Interface (UI) Design',
            'Database Management & Optimization',
            'Technical Writing & Documentation',
            'System Administration & IT Support'
        ]
    ],
    [
        'question' => 'Are you comfortable with abstract concepts and theoretical frameworks?',
        'type' => 'radio',
        'name' => 'abstract_concepts',
        'options' => [
            'Very comfortable, I enjoy theoretical exploration',
            'Somewhat comfortable, I prefer practical applications',
            'Not very comfortable, I prefer concrete tasks'
        ]
    ],
    [
        'question' => 'How much do you enjoy working with mathematical or statistical concepts?',
        'type' => 'radio',
        'name' => 'math_enjoyment',
        'options' => [
            'Highly enjoy, I seek out opportunities to use them',
            'Moderately enjoy, I can apply them when needed',
            'Neutral, I can do it but don\'t prefer it',
            'Dislike, I prefer to avoid them'
        ]
    ],
    // Section 4: Personality & Soft Skills
    [
        'question' => 'How would you describe your communication style?',
        'type' => 'radio',
        'name' => 'communication_style',
        'options' => [
            'Direct and assertive',
            'Collaborative and consensus-driven',
            'Detailed and precise',
            'Empathetic and supportive'
        ]
    ],
    [
        'question' => 'Are you more of a detail-oriented person or a big-picture thinker?',
        'type' => 'radio',
        'name' => 'detail_vs_big_picture',
        'options' => [
            'Strongly detail-oriented',
            'Mostly detail-oriented, but can see the big picture',
            'Mostly big-picture thinker, but can handle details',
            'Strongly big-picture thinker'
        ]
    ],
    [
        'question' => 'How do you handle repetitive tasks?',
        'type' => 'radio',
        'name' => 'repetitive_tasks',
        'options' => [
            'Very comfortable, I appreciate routine and consistency',
            'Somewhat comfortable, if they are necessary for a larger goal',
            'Not very comfortable, I prefer variety and new challenges',
            'Strongly dislike repetitive tasks, I seek automation'
        ]
    ],
    [
        'question' => 'How important is innovation and creating new things to you?',
        'type' => 'radio',
        'name' => 'innovation_importance',
        'options' => [
            'Extremely important, I want to be at the forefront of creation',
            'Very important, I enjoy contributing to new ideas',
            'Moderately important, I can work on existing systems',
            'Not very important, I prefer maintenance or established processes'
        ]
    ],
    // Section 5: Career Values
    [
        'question' => 'What drives you most in a career?',
        'type' => 'checkbox',
        'name' => 'career_drivers',
        'options' => [
            'High salary and financial stability',
            'Opportunities for continuous learning and growth',
            'Making a significant impact or solving real-world problems',
            'Work-life balance and flexibility',
            'Recognition and career advancement',
            'Working with cutting-edge technologies',
            'Collaborative and supportive team culture'
        ]
    ]
];


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Process Personal & Academic Details
    $major = trim($_POST['major'] ?? '');
    $gpa = $_POST['gpa'] ?? null;
    $param_gpa = (empty($gpa) && $gpa !== 0.0 && $gpa !== '0') ? null : (float)$gpa;


    // New: Process Experience and Projects Summaries
    $experience_summary = trim($_POST['experience_summary'] ?? '');
    $projects_summary = trim($_POST['projects_summary'] ?? '');

    // New: Process Contact and Summary Details
    $phone_number = trim($_POST['phone_number'] ?? '');
    $linkedin_url = trim($_POST['linkedin_url'] ?? '');
    $summary_text = trim($_POST['summary_text'] ?? '');
    $university_name = trim($_POST['university_name'] ?? '');
    $graduation_year = trim($_POST['graduation_year'] ?? '');


    // Update users table with all profile fields
    $sql_update_user_profile = "UPDATE users SET major = ?, gpa = ?, experience_summary = ?, projects_summary = ?, phone_number = ?, linkedin_url = ?, summary_text = ?, university_name = ?, graduation_year = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql_update_user_profile)) {
        // 'sssssssssi' -> s=major, s=gpa, s=experience_summary, s=projects_summary, s=phone_number, s=linkedin_url, s=summary_text, s=university_name, s=graduation_year, i=user_id
        mysqli_stmt_bind_param($stmt, "sssssssssi", $major, $param_gpa, $experience_summary, $projects_summary, $phone_number, $linkedin_url, $summary_text, $university_name, $graduation_year, $user_id);
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Error updating user profile: " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("Failed to prepare user profile update statement: " . mysqli_error($conn));
    }


    // 2. Process Skills
    // Remove existing skills for the user
    $sql_delete_skills = "DELETE FROM user_skills WHERE user_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql_delete_skills)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Insert new skills
    if (isset($_POST['skills']) && is_array($_POST['skills'])) {
        $selected_skill_ids = $_POST['skills'];
        $sql_insert_skill = "INSERT INTO user_skills (user_id, skill_id) VALUES (?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql_insert_skill)) {
            foreach ($selected_skill_ids as $skill_id) {
                mysqli_stmt_bind_param($stmt, "ii", $user_id, $skill_id);
                mysqli_stmt_execute($stmt);
            }
            mysqli_stmt_close($stmt);
        }
    }

    // 3. Process Quiz Answers
    // Clear previous answers to avoid duplicates on resubmission
    $sql_delete_answers = "DELETE FROM user_answers WHERE user_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql_delete_answers)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    $sql_insert_answer = "INSERT INTO user_answers (user_id, question, answer) VALUES (?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql_insert_answer)) {
        foreach ($quiz_questions as $q) {
            $question_text = $q['question'];
            $input_name = $q['name'];

            if ($q['type'] == 'radio' && isset($_POST[$input_name])) {
                $answer = $_POST[$input_name];
                mysqli_stmt_bind_param($stmt, "iss", $user_id, $question_text, $answer);
                mysqli_stmt_execute($stmt);
            } elseif ($q['type'] == 'checkbox' && isset($_POST[$input_name]) && is_array($_POST[$input_name])) {
                // For checkboxes, store answers as a comma-separated string
                $answers_str = implode("||", $_POST[$input_name]); // Using || as delimiter for easier parsing
                mysqli_stmt_bind_param($stmt, "iss", $user_id, $question_text, $answers_str);
                mysqli_stmt_execute($stmt);
            }
        }
        mysqli_stmt_close($stmt);
    }

    $_SESSION['profile_success'] = "Your profile information has been saved successfully!";
    // --- CHANGE HERE: Redirect to dashboard.php instead of profile.php ---
    header("Location: dashboard.php"); // Redirect to dashboard
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - User Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .profile-container { max-width: 800px; margin: 50px auto; padding: 30px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .profile-container h1 { color: #0056b3; margin-bottom: 20px; text-align: center; }
        .form-section { margin-bottom: 30px; padding: 20px; border: 1px solid #eee; border-radius: 5px; }
        .form-section h3 { margin-top: 0; color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-bottom: 20px;}
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="number"],
        .form-group textarea {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .skills-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .skills-header h3 {
            margin: 0;
            border-bottom: none; /* Remove border from H3 inside flex */
            padding-bottom: 0;
        }
        .skill-search-bar {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            width: 250px; /* Adjust width as needed */
        }
        .skills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 10px;
            margin-top: 10px;
            max-height: 400px; /* Limit height */
            overflow-y: auto; /* Add scroll for many skills */
            padding-right: 10px; /* Space for scrollbar */
        }
        .skills-grid label {
            display: flex;
            align-items: center;
            background-color: #f0f0f0;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .skills-grid label:hover {
            background-color: #e0e0e0;
        }
        .skills-grid input[type="checkbox"] {
            margin-right: 8px;
        }
        .quiz-question { margin-bottom: 20px; }
        .quiz-question p { font-weight: bold; margin-bottom: 10px; }
        .quiz-options label { display: block; margin-bottom: 5px; cursor: pointer; }
        .quiz-options input[type="radio"],
        .quiz-options input[type="checkbox"] { margin-right: 8px; }

        .btn-submit {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            width: 100%;
            box-sizing: border-box;
            transition: background-color 0.3s ease;
        }
        .btn-submit:hover { background-color: #218838; }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .back-link { display: block; text-align: center; margin-top: 20px; }
        .back-link a { color: #007bff; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="profile-container">
        <h1>My Profile</h1>
        <p style="text-align: center;">Update your details to get personalized career recommendations.</p>

        <?php
        if (isset($_SESSION['profile_success'])) {
            echo '<div class="success-message">' . $_SESSION['profile_success'] . '</div>';
            unset($_SESSION['profile_success']);
        }
        ?>

        <form action="profile.php" method="POST">
            <div class="form-section">
                <h3>Personal & Contact Details</h3>
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_details['name'] ?? ''); ?>" required readonly>
                    <small>Name is from your registration and cannot be changed here.</small>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_details['email'] ?? ''); ?>" required readonly>
                    <small>Email is from your registration and cannot be changed here.</small>
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number:</label>
                    <input type="text" id="phone_number" name="phone_number" placeholder="e.g., +1 (123) 456-7890" value="<?php echo htmlspecialchars($user_details['phone_number'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="linkedin_url">LinkedIn Profile URL:</label>
                    <input type="text" id="linkedin_url" name="linkedin_url" placeholder="e.g., https://linkedin.com/in/yourprofile" value="<?php echo htmlspecialchars($user_details['linkedin_url'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-section">
                <h3>Academic Details</h3>
                <div class="form-group">
                    <label for="university_name">University Name:</label>
                    <input type="text" id="university_name" name="university_name" placeholder="e.g., XYZ University" value="<?php echo htmlspecialchars($user_details['university_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="major">Major/Field of Study:</label>
                    <input type="text" id="major" name="major" placeholder="e.g., Computer Science, Business" value="<?php echo htmlspecialchars($user_details['major'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="gpa">GPA (on 4.0 scale):</label>
                    <input type="number" id="gpa" name="gpa" step="0.01" min="0" max="4" placeholder="e.g., 3.5" value="<?php echo htmlspecialchars($user_details['gpa'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="graduation_year">Graduation Year:</label>
                    <input type="text" id="graduation_year" name="graduation_year" placeholder="e.g., 2025 or Expected 2026" value="<?php echo htmlspecialchars($user_details['graduation_year'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-section">
                <h3>Resume Summary/Objective</h3>
                <div class="form-group">
                    <label for="summary_text">Write a brief professional summary or objective:</label>
                    <textarea id="summary_text" name="summary_text" rows="5" placeholder="e.g., Highly motivated software engineering student with a strong passion for full-stack development and a proven ability to learn new technologies quickly. Seeking a challenging internship to apply theoretical knowledge and contribute to innovative projects."><?php echo htmlspecialchars($user_details['summary_text'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <div class="skills-header">
                    <h3>Your Skills</h3>
                    <input type="text" id="skillSearch" class="skill-search-bar" placeholder="Search skills...">
                </div>
                <p>Select all skills that apply to you:</p>
                <div class="skills-grid">
                    <?php if (!empty($all_skills)): ?>
                        <?php foreach ($all_skills as $skill): ?>
                            <label class="skill-item-label">
                                <input type="checkbox" name="skills[]" value="<?php echo $skill['id']; ?>"
                                    <?php echo in_array($skill['id'], $user_selected_skills_ids) ? 'checked' : ''; ?>>
                                <?php echo htmlspecialchars($skill['name']); ?>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No skills available. Please contact an admin.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-section">
                <h3>Work Experience Summary</h3>
                <div class="form-group">
                    <label for="experience_summary">Describe your work experience:</label>
                    <textarea id="experience_summary" name="experience_summary" rows="8" placeholder="e.g., Developed web applications using PHP and MySQL. Managed a team of 3 junior developers. Implemented new features, reducing bug reports by 20%." ><?php echo htmlspecialchars($user_details['experience_summary'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <h3>Projects Summary</h3>
                <div class="form-group">
                    <label for="projects_summary">Describe your key projects:</label>
                    <textarea id="projects_summary" name="projects_summary" rows="8" placeholder="e.g., Built a full-stack e-commerce platform using React and Node.js. Developed a machine learning model for sentiment analysis. Designed and implemented a secure network architecture." ><?php echo htmlspecialchars($user_details['projects_summary'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <h3>Career Interest Quiz</h3>
                <?php foreach ($quiz_questions as $q_index => $question_data): ?>
                    <div class="quiz-question">
                        <p><?php echo ($q_index + 1) . ". " . htmlspecialchars($question_data['question']); ?></p>
                        <div class="quiz-options">
                            <?php foreach ($question_data['options'] as $opt_index => $option): ?>
                                <label>
                                    <input type="<?php echo $question_data['type']; ?>"
                                           name="<?php echo htmlspecialchars($question_data['name']); ?><?php echo ($question_data['type'] == 'checkbox' ? '[]' : ''); ?>"
                                           value="<?php echo htmlspecialchars($option); ?>"
                                           <?php
                                           $prev_answer = $user_answers[$question_data['question']] ?? '';
                                           if ($question_data['type'] == 'radio') {
                                               echo ($prev_answer == $option) ? 'checked' : '';
                                           } elseif ($question_data['type'] == 'checkbox') {
                                               // Check if this option was part of previously selected checkbox answers
                                               $prev_answers_array = explode("||", $prev_answer); // Use || as delimiter
                                               echo in_array($option, $prev_answers_array) ? 'checked' : '';
                                           }
                                           ?>>
                                    <?php echo htmlspecialchars($option); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn-submit">Save Profile and Quiz Answers</button>
        </form>

        <div class="back-link">
            <p><a href="dashboard.php">Back to Dashboard</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const skillSearchInput = document.getElementById('skillSearch');
            const skillsGrid = document.querySelector('.skills-grid');
            const skillLabels = skillsGrid ? skillsGrid.querySelectorAll('.skill-item-label') : [];

            if (skillSearchInput) {
                skillSearchInput.addEventListener('keyup', function() {
                    const searchTerm = skillSearchInput.value.toLowerCase();

                    skillLabels.forEach(label => {
                        const skillName = label.textContent.toLowerCase();
                        if (skillName.includes(searchTerm)) {
                            label.style.display = 'flex'; // Show the label
                        } else {
                            label.style.display = 'none'; // Hide the label
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
