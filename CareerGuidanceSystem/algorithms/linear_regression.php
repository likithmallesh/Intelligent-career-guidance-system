<?php
// algorithms/linear_regression.php

/**
 * Predicts success percentage for a given career based on user data using a weighted formula.
 *
 * @param array $user_skills_ids An array of skill IDs the user possesses.
 * @param array $user_answers An associative array of quiz questions and their answers.
 * @param array $career_info An array containing career details (e.g., id, title, description).
 * @param array $all_skills An array of all skills (id, name).
 * @param float $user_gpa User's GPA (optional, can be null).
 * @return float The predicted success score (0-100).
 */
function predictSuccessScore($user_skills_ids, $user_answers, $career_info, $all_skills, $user_gpa = null) {
    $score = 0;
    $max_possible_score = 0;

    // Map skill names to IDs for easier rule definition
    $skill_names_to_ids = [];
    foreach ($all_skills as $skill) {
        $skill_names_to_ids[strtolower($skill['name'])] = $skill['id'];
    }

    // Define career-specific requirements/preferences and their base scores
    $career_scoring_factors = [
        // Web Developer (ID: 1)
        1 => [
            'key_skills' => [
                $skill_names_to_ids['javascript'] ?? 0, $skill_names_to_ids['html5'] ?? 0,
                $skill_names_to_ids['css3'] ?? 0, $skill_names_to_ids['react.js'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['node.js'] ?? 0, $skill_names_to_ids['mysql'] ?? 0,
                $skill_names_to_ids['responsive design'] ?? 0, $skill_names_to_ids['git'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Flexible environment' => 5, 'Collaborative team-based environment' => 3],
                'How do you prefer to approach problem-solving?' => ['Systematically breaking down complex problems' => 5, 'Experimenting and iterating' => 4],
                'What kind of tasks do you find most engaging?' => ['Writing and debugging code' => 8, 'Designing and creating visual interfaces' => 6],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 7],
                'Which of the following areas interests you most?' => ['Web & Mobile Application Development' => 10, 'User Experience (UX) & Interface (UI) Design' => 5],
                'How important is innovation and creating new things to you?' => ['Extremely important' => 5, 'Very important' => 4]
            ],
            'gpa_weight' => 5 // Max points for GPA
        ],
        // Data Scientist (ID: 2)
        2 => [
            'key_skills' => [
                $skill_names_to_ids['python'] ?? 0, $skill_names_to_ids['pandas'] ?? 0,
                $skill_names_to_ids['numpy'] ?? 0, $skill_names_to_ids['data analysis'] ?? 0,
                $skill_names_to_ids['machine learning'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['r'] ?? 0, $skill_names_to_ids['sql query optimization'] ?? 0,
                $skill_names_to_ids['statistical analysis'] ?? 0, $skill_names_to_ids['deep learning'] ?? 0
            ],
            'ideal_answers' => [
                'How do you prefer to approach problem-solving?' => ['Systematically breaking down complex problems' => 7],
                'What kind of tasks do you find most engaging?' => ['Analyzing data and identifying patterns' => 10, 'Researching new technologies' => 7],
                'How do you prefer to learn new technologies or skills?' => ['Reading documentation and academic papers' => 8],
                'Which of the following areas interests you most?' => ['Artificial Intelligence & Machine Learning' => 10, 'Data Analysis & Business Intelligence' => 8],
                'Are you comfortable with abstract concepts and theoretical frameworks?' => ['Very comfortable' => 7],
                'How much do you enjoy working with mathematical or statistical concepts?' => ['Highly enjoy' => 8, 'Moderately enjoy' => 5]
            ],
            'gpa_weight' => 8 // Higher GPA importance
        ],
        // Cybersecurity Analyst (ID: 3)
        3 => [
            'key_skills' => [
                $skill_names_to_ids['network security'] ?? 0, $skill_names_to_ids['cybersecurity'] ?? 0,
                $skill_names_to_ids['vulnerability assessment'] ?? 0, $skill_names_to_ids['incident response'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['linux/unix'] ?? 0, $skill_names_to_ids['networking fundamentals'] ?? 0,
                $skill_names_to_ids['firewalls'] ?? 0, $skill_names_to_ids['penetration testing'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Structured office setting' => 7, 'Fast-paced, high-pressure environment' => 5],
                'How do you prefer to approach problem-solving?' => ['Systematically breaking down complex problems' => 7],
                'What kind of tasks do you find most engaging?' => ['Securing systems and preventing attacks' => 10, 'Analyzing data' => 5],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 8],
                'Which of the following areas interests you most?' => ['Cybersecurity & Network Defense' => 10],
                'How do you handle repetitive tasks?' => ['Very comfortable' => 7, 'Somewhat comfortable' => 4]
            ],
            'gpa_weight' => 5
        ],
        // Digital Marketing Specialist (ID: 4)
        4 => [
            'key_skills' => [
                $skill_names_to_ids['marketing'] ?? 0, $skill_names_to_ids['communication skills'] ?? 0,
                $skill_names_to_ids['content writing'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['seo'] ?? 0, $skill_names_to_ids['social media marketing'] ?? 0,
                $skill_names_to_ids['google analytics'] ?? 0, $skill_names_to_ids['data analysis'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Collaborative team-based environment' => 7, 'Flexible environment' => 5],
                'What kind of tasks do you find most engaging?' => ['Interacting directly with clients or users' => 8, 'Designing and creating visual interfaces' => 6],
                'How do you prefer to learn new technologies or skills?' => ['Watching video tutorials' => 6, 'Attending workshops' => 5],
                'How would you describe your communication style?' => ['Collaborative and consensus-driven' => 7, 'Empathetic and supportive' => 6],
                'How important is innovation and creating new things to you?' => ['Very important' => 6]
            ],
            'gpa_weight' => 3
        ],
        // Graphic Designer (ID: 5)
        5 => [
            'key_skills' => [
                $skill_names_to_ids['graphic design'] ?? 0, $skill_names_to_ids['ui/ux design principles'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['html5'] ?? 0, $skill_names_to_ids['css3'] ?? 0,
                $skill_names_to_ids['creativity'] ?? 0 // Assuming creativity skill
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Flexible environment' => 8],
                'How do you prefer to approach problem-solving?' => ['Brainstorming creative and unconventional solutions' => 10],
                'What kind of tasks do you find most engaging?' => ['Designing and creating visual interfaces or content' => 12],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 7, 'Watching video tutorials' => 5],
                'Which of the following areas interests you most?' => ['User Experience (UX) & Interface (UI) Design' => 10],
                'How important is innovation and creating new things to you?' => ['Extremely important' => 8, 'Very important' => 6]
            ],
            'gpa_weight' => 3
        ],
        // Network Administrator (ID: 6)
        6 => [
            'key_skills' => [
                $skill_names_to_ids['networking fundamentals'] ?? 0, $skill_names_to_ids['tcp/ip'] ?? 0,
                $skill_names_to_ids['system administration'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['linux/unix'] ?? 0, $skill_names_to_ids['windows server'] ?? 0,
                $skill_names_to_ids['firewalls'] ?? 0, $skill_names_to_ids['technical support'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Structured office setting' => 10],
                'How do you prefer to approach problem-solving?' => ['Systematically breaking down complex problems' => 7],
                'What kind of tasks do you find most engaging?' => ['Securing systems and preventing attacks' => 6],
                'How do you handle repetitive tasks?' => ['Very comfortable' => 8, 'Somewhat comfortable' => 5]
            ],
            'gpa_weight' => 4
        ],
        // Project Manager (ID: 7)
        7 => [
            'key_skills' => [
                $skill_names_to_ids['project management'] ?? 0, $skill_names_to_ids['communication skills'] ?? 0,
                $skill_names_to_ids['teamwork'] ?? 0, $skill_names_to_ids['agile methodologies'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['jira'] ?? 0, $skill_names_to_ids['leadership'] ?? 0,
                $skill_names_to_ids['negotiation'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Collaborative team-based environment' => 10, 'Fast-paced, high-pressure environment' => 7],
                'How do you prefer to approach problem-solving?' => ['Collaborating with others' => 8],
                'What kind of tasks do you find most engaging?' => ['Managing projects and coordinating teams' => 10, 'Interacting directly with clients or users' => 8],
                'How would you describe your communication style?' => ['Collaborative and consensus-driven' => 7, 'Direct and assertive' => 5],
                'Are you more of a detail-oriented person or a big-picture thinker?' => ['Mostly big-picture thinker' => 7, 'Strongly big-picture thinker' => 8],
                'What drives you most in a career?' => ['Making a significant impact' => 8, 'Work-life balance' => 6]
            ],
            'gpa_weight' => 4
        ],
        // Cloud Engineer (ID: 8)
        8 => [
            'key_skills' => [
                $skill_names_to_ids['aws (amazon web services)'] ?? 0, $skill_names_to_ids['azure (microsoft azure)'] ?? 0,
                $skill_names_to_ids['google cloud platform (gcp)'] ?? 0, $skill_names_to_ids['docker'] ?? 0,
                $skill_names_to_ids['kubernetes'] ?? 0, $skill_names_to_ids['terraform'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['linux/unix'] ?? 0, $skill_names_to_ids['networking fundamentals'] ?? 0,
                $skill_names_to_ids['serverless architecture'] ?? 0, $skill_names_to_ids['ci/cd pipelines'] ?? 0,
                $skill_names_to_ids['python'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Flexible environment' => 7, 'Fast-paced, high-pressure environment' => 5],
                'How do you prefer to approach problem-solving?' => ['Experimenting and iterating' => 7, 'Systematically breaking down complex problems' => 6],
                'What kind of tasks do you find most engaging?' => ['Researching new technologies and concepts' => 8, 'Writing and debugging code' => 7],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 8],
                'Which of the following areas interests you most?' => ['Cloud Computing & DevOps' => 10],
                'How comfortable are you with continuous learning and adapting to new tools?' => ['Extremely comfortable' => 8, 'Comfortable' => 6],
                'How do you handle repetitive tasks?' => ['Strongly dislike repetitive tasks, I seek automation' => 7]
            ],
            'gpa_weight' => 5
        ],
        // AI/ML Engineer (ID: 9)
        9 => [
            'key_skills' => [
                $skill_names_to_ids['python'] ?? 0, $skill_names_to_ids['tensorflow'] ?? 0,
                $skill_names_to_ids['pytorch'] ?? 0, $skill_names_to_ids['deep learning'] ?? 0,
                $skill_names_to_ids['machine learning'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['data cleaning'] ?? 0, $skill_names_to_ids['statistical analysis'] ?? 0,
                $skill_names_to_ids['big data'] ?? 0, $skill_names_to_ids['research skills'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Independent work' => 7, 'Flexible environment' => 5],
                'How do you prefer to approach problem-solving?' => ['Systematically breaking down complex problems' => 8, 'Experimenting and iterating' => 7],
                'What kind of tasks do you find most engaging?' => ['Analyzing data and identifying patterns' => 8, 'Researching new technologies and concepts' => 8, 'Writing and debugging code' => 7],
                'How do you prefer to learn new technologies or skills?' => ['Reading documentation and academic papers' => 8, 'Hands-on coding/building projects' => 7],
                'Which of the following areas interests you most?' => ['Artificial Intelligence & Machine Learning' => 12],
                'Are you comfortable with abstract concepts and theoretical frameworks?' => ['Very comfortable' => 8],
                'How much do you enjoy working with mathematical or statistical concepts?' => ['Highly enjoy' => 8]
            ],
            'gpa_weight' => 7
        ],
        // UX/UI Designer (ID: 10)
        10 => [
            'key_skills' => [
                $skill_names_to_ids['ui/ux design principles'] ?? 0, $skill_names_to_ids['graphic design'] ?? 0,
                $skill_names_to_ids['responsive design'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['html5'] ?? 0, $skill_names_to_ids['css3'] ?? 0,
                $skill_names_to_ids['javascript'] ?? 0, $skill_names_to_ids['communication skills'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Flexible environment' => 8, 'Collaborative team-based environment' => 7],
                'How do you prefer to approach problem-solving?' => ['Brainstorming creative and unconventional solutions' => 8],
                'What kind of tasks do you find most engaging?' => ['Designing and creating visual interfaces or content' => 10, 'Interacting directly with clients or users' => 7],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 7, 'Watching video tutorials' => 5],
                'Which of the following areas interests you most?' => ['User Experience (UX) & Interface (UI) Design' => 12, 'Web & Mobile Application Development' => 5],
                'How important is innovation and creating new things to you?' => ['Extremely important' => 8, 'Very important' => 7]
            ],
            'gpa_weight' => 3
        ],
        // DevOps Engineer (ID: 11)
        11 => [
            'key_skills' => [
                $skill_names_to_ids['docker'] ?? 0, $skill_names_to_ids['kubernetes'] ?? 0,
                $skill_names_to_ids['jenkins'] ?? 0, $skill_names_to_ids['git'] ?? 0,
                $skill_names_to_ids['ci/cd pipelines'] ?? 0, $skill_names_to_ids['ansible'] ?? 0,
                $skill_names_to_ids['terraform'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['linux/unix'] ?? 0, $skill_names_to_ids['aws (amazon web services)'] ?? 0,
                $skill_names_to_ids['azure (microsoft azure)'] ?? 0, $skill_names_to_ids['google cloud platform (gcp)'] ?? 0,
                $skill_names_to_ids['monitoring & logging'] ?? 0, $skill_names_to_ids['python'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Flexible environment' => 7, 'Fast-paced, high-pressure environment' => 5],
                'How do you prefer to approach problem-solving?' => ['Systematically breaking down complex problems' => 7, 'Experimenting and iterating' => 6],
                'What kind of tasks do you find most engaging?' => ['Writing and debugging code' => 8, 'Researching new technologies and concepts' => 7],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 8],
                'Which of the following areas interests you most?' => ['Cloud Computing & DevOps' => 12],
                'How comfortable are you with continuous learning and adapting to new tools?' => ['Extremely comfortable' => 8],
                'How do you handle repetitive tasks?' => ['Strongly dislike repetitive tasks, I seek automation' => 7]
            ],
            'gpa_weight' => 5
        ],
        // Mobile App Developer (ID: 12)
        12 => [
            'key_skills' => [
                $skill_names_to_ids['android development'] ?? 0, $skill_names_to_ids['ios development'] ?? 0,
                $skill_names_to_ids['react native'] ?? 0, $skill_names_to_ids['flutter'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['java'] ?? 0, $skill_names_to_ids['swift'] ?? 0,
                $skill_names_to_ids['ui/ux design principles'] ?? 0, $skill_names_to_ids['restful apis'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Flexible environment' => 7, 'Collaborative team-based environment' => 6],
                'How do you prefer to approach problem-solving?' => ['Experimenting and iterating' => 7, 'Systematically breaking down complex problems' => 6],
                'What kind of tasks do you find most engaging?' => ['Writing and debugging code' => 10, 'Designing and creating visual interfaces' => 7],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 8],
                'Which of the following areas interests you most?' => ['Web & Mobile Application Development' => 12],
                'How important is innovation and creating new things to you?' => ['Extremely important' => 7, 'Very important' => 6]
            ],
            'gpa_weight' => 4
        ],
        // Game Developer (ID: 13)
        13 => [
            'key_skills' => [
                $skill_names_to_ids['unity 3d'] ?? 0, $skill_names_to_ids['unreal engine'] ?? 0,
                $skill_names_to_ids['c# (game dev)'] ?? 0, $skill_names_to_ids['c++ (game dev)'] ?? 0,
                $skill_names_to_ids['game design'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['3d modeling'] ?? 0, $skill_names_to_ids['animation'] ?? 0,
                $skill_names_to_ids['physics engines'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Flexible environment' => 8, 'Collaborative team-based environment' => 7],
                'How do you prefer to approach problem-solving?' => ['Brainstorming creative and unconventional solutions' => 8, 'Experimenting and iterating' => 7],
                'What kind of tasks do you find most engaging?' => ['Designing and creating visual interfaces or content' => 10, 'Writing and debugging code' => 8],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 8],
                'Which of the following areas interests you most?' => ['Game Development & Interactive Media' => 12],
                'How important is innovation and creating new things to you?' => ['Extremely important' => 8]
            ],
            'gpa_weight' => 3
        ],
        // Data Analyst (ID: 14)
        14 => [
            'key_skills' => [
                $skill_names_to_ids['data analysis'] ?? 0, $skill_names_to_ids['sql query optimization'] ?? 0,
                $skill_names_to_ids['tableau'] ?? 0, $skill_names_to_ids['power bi'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['python'] ?? 0, $skill_names_to_ids['r'] ?? 0,
                $skill_names_to_ids['statistical analysis'] ?? 0, $skill_names_to_ids['data cleaning'] ?? 0,
                $skill_names_to_ids['communication skills'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Structured office setting' => 7, 'Independent work' => 5],
                'How do you prefer to approach problem-solving?' => ['Systematically breaking down complex problems' => 8],
                'What kind of tasks do you find most engaging?' => ['Analyzing data and identifying patterns' => 10, 'Researching new technologies and concepts' => 7],
                'How do you prefer to learn new technologies or skills?' => ['Reading documentation and academic papers' => 7, 'Hands-on coding/building projects' => 6],
                'Which of the following areas interests you most?' => ['Data Analysis & Business Intelligence' => 12],
                'How much do you enjoy working with mathematical or statistical concepts?' => ['Highly enjoy' => 7, 'Moderately enjoy' => 6],
                'Are you more of a detail-oriented person or a big-picture thinker?' => ['Strongly detail-oriented' => 8, 'Mostly detail-oriented' => 7]
            ],
            'gpa_weight' => 6
        ],
        // Technical Writer (ID: 15)
        15 => [
            'key_skills' => [
                $skill_names_to_ids['technical writing'] ?? 0, $skill_names_to_ids['communication skills'] ?? 0,
                $skill_names_to_ids['research skills'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['content writing'] ?? 0, $skill_names_to_ids['documentation'] ?? 0,
                $skill_names_to_ids['attention to detail'] ?? 0 // Assuming attention to detail skill
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Independent work' => 8, 'Structured office setting' => 7],
                'How do you prefer to approach problem-solving?' => ['Systematically breaking down complex problems' => 7],
                'What kind of tasks do you find most engaging?' => ['Researching new technologies and concepts' => 8, 'Interacting directly with clients or users' => 4],
                'How do you prefer to learn new technologies or skills?' => ['Reading documentation and academic papers' => 8, 'Learning from mentors' => 5],
                'How would you describe your communication style?' => ['Detailed and precise' => 8, 'Collaborative and consensus-driven' => 7],
                'Are you more of a detail-oriented person or a big-picture thinker?' => ['Strongly detail-oriented' => 8]
            ],
            'gpa_weight' => 3
        ],
    ];

    $career_id = $career_info['id'];

    if (!isset($career_scoring_factors[$career_id])) {
        return 0; // No scoring factors defined for this career
    }

    $factors = $career_scoring_factors[$career_id];
    $current_score = 0;
    $max_possible_score_for_career = 0;

    // Base score (every career starts with a baseline potential)
    $current_score += 10; // Base score
    $max_possible_score_for_career += 10;

    // Score based on key skills
    foreach ($factors['key_skills'] as $skill_id) {
        if ($skill_id > 0) { // Ensure skill_id is valid (not 0 from ?? 0)
            if (in_array($skill_id, $user_skills_ids)) {
                $current_score += 10; // Each key skill adds 10 points
            }
            $max_possible_score_for_career += 10;
        }
    }

    // Score based on relevant skills
    foreach ($factors['relevant_skills'] as $skill_id) {
        if ($skill_id > 0) {
            if (in_array($skill_id, $user_skills_ids)) {
                $current_score += 5; // Each relevant skill adds 5 points
            }
            $max_possible_score_for_career += 5;
        }
    }

    // Score based on quiz answers
    foreach ($factors['ideal_answers'] as $question_text => $answer_contributions) {
        if (isset($user_answers[$question_text])) {
            $user_answer = $user_answers[$question_text];
            $question_max_contribution = 0; // Max points for this specific quiz question

            // For checkbox answers, the stored answer is "Option1||Option2"
            $user_answer_parts = explode("||", $user_answer);

            foreach ($answer_contributions as $expected_answer_substring => $contribution) {
                // Add to max possible for this question
                $question_max_contribution += $contribution;

                // Check if user's answer (or any part of it for checkboxes) matches the ideal substring
                $matched_for_question = false;
                foreach ($user_answer_parts as $part) {
                    if (strpos(strtolower($part), strtolower($expected_answer_substring)) !== false) {
                        $current_score += $contribution;
                        $matched_for_question = true;
                        // For radio, we only match one. For checkboxes, we add points for each match.
                        // If you want only one contribution per question for checkboxes, add a break here.
                        // break; // Uncomment this if you want only the first matching option to contribute
                    }
                }
            }
            $max_possible_score_for_career += $question_max_contribution;
        }
    }

    // Score based on GPA (if provided and relevant)
    if ($user_gpa !== null && $factors['gpa_weight'] > 0) {
        // Normalize GPA to a 0-1 scale (assuming 4.0 max GPA)
        $normalized_gpa = $user_gpa / 4.0;
        $gpa_score = $normalized_gpa * $factors['gpa_weight'];
        $current_score += $gpa_score;
        $max_possible_score_for_career += $factors['gpa_weight'];
    }

    // Calculate percentage
    if ($max_possible_score_for_career > 0) {
        $percentage = ($current_score / $max_possible_score_for_career) * 100;
    } else {
        $percentage = 0;
    }

    // Cap at 100 and ensure non-negative
    return max(0, min(100, round($percentage, 2)));
}
?>
