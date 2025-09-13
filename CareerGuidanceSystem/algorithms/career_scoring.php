<?php
// algorithms/career_scoring.php

/**
 * Calculates a compatibility score for each career based on user's skills and quiz answers.
 * This replaces the strict rule-based filtering with a more flexible scoring system.
 *
 * @param array $user_skills_ids An array of skill IDs the user possesses.
 * @param array $user_answers An associative array of quiz questions and their answers.
 * @param array $all_careers An array of all available careers (id, title, description).
 * @param array $all_skills An array of all available skills (id, name).
 * @return array An associative array of career_title => score, sorted descending.
 */
function getCareerCompatibilityScores($user_skills_ids, $user_answers, $all_careers, $all_skills) {
    $career_scores = [];

    // Map skill names to IDs for easier rule definition
    $skill_names_to_ids = [];
    foreach ($all_skills as $skill) {
        $skill_names_to_ids[strtolower($skill['name'])] = $skill['id'];
    }

    // Define scoring factors for each career
    // Each career has:
    // - 'key_skills': skill IDs crucial for this career (high points)
    // - 'relevant_skills': skill IDs beneficial for this career (medium points)
    // - 'ideal_answers': quiz answers that strongly align (question => expected_answer_substring => points)
    // Points are adjusted to reflect the importance of each factor.
    $scoring_factors = [
        // Web Developer (ID: 1)
        1 => [
            'key_skills' => [
                $skill_names_to_ids['javascript'] ?? 0,
                $skill_names_to_ids['html5'] ?? 0,
                $skill_names_to_ids['css3'] ?? 0,
                $skill_names_to_ids['react.js'] ?? 0, // Assuming React is a common frontend skill
                $skill_names_to_ids['node.js'] ?? 0 // Assuming Node.js for backend
            ],
            'relevant_skills' => [
                $skill_names_to_ids['mysql'] ?? 0,
                $skill_names_to_ids['bootstrap'] ?? 0,
                $skill_names_to_ids['git'] ?? 0,
                $skill_names_to_ids['problem solving'] ?? 0,
                $skill_names_to_ids['restful apis'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Flexible environment' => 10, 'Collaborative team-based environment' => 5],
                'How do you prefer to approach problem-solving?' => ['Systematically breaking down complex problems' => 8, 'Experimenting and iterating' => 7],
                'What kind of tasks do you find most engaging?' => ['Writing and debugging code' => 15, 'Designing and creating visual interfaces' => 10, 'Analyzing data' => 5],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 12],
                'Which of the following areas interests you most?' => ['Web & Mobile Application Development' => 20, 'User Experience (UX) & Interface (UI) Design' => 10],
                'How important is innovation and creating new things to you?' => ['Extremely important' => 10, 'Very important' => 8]
            ],
            'base_score' => 20 // Starting score for any career
        ],
        // Data Scientist (ID: 2)
        2 => [
            'key_skills' => [
                $skill_names_to_ids['python'] ?? 0,
                $skill_names_to_ids['r'] ?? 0,
                $skill_names_to_ids['pandas'] ?? 0,
                $skill_names_to_ids['numpy'] ?? 0,
                $skill_names_to_ids['scikit-learn'] ?? 0,
                $skill_names_to_ids['data analysis'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['sql query optimization'] ?? 0,
                $skill_names_to_ids['statistical analysis'] ?? 0,
                $skill_names_to_ids['machine learning'] ?? 0,
                $skill_names_to_ids['deep learning'] ?? 0,
                $skill_names_to_ids['problem solving'] ?? 0,
                $skill_names_to_ids['critical thinking'] ?? 0
            ],
            'ideal_answers' => [
                'How do you prefer to approach problem-solving?' => ['Systematically breaking down complex problems' => 10],
                'What kind of tasks do you find most engaging?' => ['Analyzing data and identifying patterns' => 20, 'Researching new technologies' => 10],
                'How do you prefer to learn new technologies or skills?' => ['Reading documentation and academic papers' => 12],
                'Which of the following areas interests you most?' => ['Artificial Intelligence & Machine Learning' => 20, 'Data Analysis & Business Intelligence' => 15],
                'Are you comfortable with abstract concepts and theoretical frameworks?' => ['Very comfortable' => 10],
                'How much do you enjoy working with mathematical or statistical concepts?' => ['Highly enjoy' => 15, 'Moderately enjoy' => 10]
            ],
            'base_score' => 20
        ],
        // Cybersecurity Analyst (ID: 3)
        3 => [
            'key_skills' => [
                $skill_names_to_ids['network security'] ?? 0,
                $skill_names_to_ids['cybersecurity'] ?? 0,
                $skill_names_to_ids['vulnerability assessment'] ?? 0,
                $skill_names_to_ids['incident response'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['linux/unix'] ?? 0,
                $skill_names_to_ids['networking fundamentals'] ?? 0,
                $skill_names_to_ids['firewalls'] ?? 0,
                $skill_names_to_ids['problem solving'] ?? 0,
                $skill_names_to_ids['critical thinking'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Structured office setting' => 10, 'Fast-paced, high-pressure environment' => 8],
                'How do you prefer to approach problem-solving?' => ['Systematically breaking down complex problems' => 10],
                'What kind of tasks do you find most engaging?' => ['Securing systems and preventing attacks' => 20, 'Analyzing data and identifying patterns' => 10],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 12],
                'Which of the following areas interests you most?' => ['Cybersecurity & Network Defense' => 20],
                'Are you comfortable with abstract concepts and theoretical frameworks?' => ['Somewhat comfortable' => 5],
                'How do you handle repetitive tasks?' => ['Very comfortable' => 8, 'Somewhat comfortable' => 5]
            ],
            'base_score' => 20
        ],
        // Digital Marketing Specialist (ID: 4)
        4 => [
            'key_skills' => [
                $skill_names_to_ids['marketing'] ?? 0, // Assuming a skill 'Marketing' exists
                $skill_names_to_ids['communication skills'] ?? 0,
                $skill_names_to_ids['content writing'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['seo'] ?? 0, // Assuming SEO skill exists
                $skill_names_to_ids['social media marketing'] ?? 0, // Assuming Social Media Marketing skill exists
                $skill_names_to_ids['google analytics'] ?? 0, // Assuming Google Analytics skill exists
                $skill_names_to_ids['data analysis'] ?? 0 // For campaign performance
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Collaborative team-based environment' => 10, 'Flexible environment' => 8],
                'What kind of tasks do you find most engaging?' => ['Interacting directly with clients or users' => 15, 'Designing and creating visual interfaces' => 10],
                'How do you prefer to learn new technologies or skills?' => ['Watching video tutorials and online courses' => 8, 'Attending workshops and conferences' => 7],
                'Which of the following areas interests you most?' => ['Digital Marketing Specialist' => 20], // Assuming this is an interest area
                'How would you describe your communication style?' => ['Collaborative and consensus-driven' => 10, 'Empathetic and supportive' => 8],
                'How important is innovation and creating new things to you?' => ['Very important' => 10]
            ],
            'base_score' => 20
        ],
        // Graphic Designer (ID: 5)
        5 => [
            'key_skills' => [
                $skill_names_to_ids['graphic design'] ?? 0,
                $skill_names_to_ids['ui/ux design principles'] ?? 0 // UI/UX is critical for modern design
            ],
            'relevant_skills' => [
                $skill_names_to_ids['adobe photoshop'] ?? 0, // Assuming specific tool skills exist
                $skill_names_to_ids['adobe illustrator'] ?? 0,
                $skill_names_to_ids['web design (html/css)'] ?? 0,
                $skill_names_to_ids['creativity'] ?? 0 // Assuming creativity is a skill
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Flexible environment' => 12],
                'How do you prefer to approach problem-solving?' => ['Brainstorming creative and unconventional solutions' => 15],
                'What kind of tasks do you find most engaging?' => ['Designing and creating visual interfaces or content' => 20],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 10, 'Watching video tutorials' => 8],
                'Which of the following areas interests you most?' => ['User Experience (UX) & Interface (UI) Design' => 20],
                'How important is innovation and creating new things to you?' => ['Extremely important' => 15, 'Very important' => 10]
            ],
            'base_score' => 20
        ],
        // Network Administrator (ID: 6)
        6 => [
            'key_skills' => [
                $skill_names_to_ids['networking fundamentals'] ?? 0,
                $skill_names_to_ids['tcp/ip'] ?? 0,
                $skill_names_to_ids['dns'] ?? 0,
                $skill_names_to_ids['dhcp'] ?? 0,
                $skill_names_to_ids['system administration'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['linux/unix'] ?? 0,
                $skill_names_to_ids['windows server'] ?? 0,
                $skill_names_to_ids['firewalls'] ?? 0,
                $skill_names_to_ids['problem solving'] ?? 0,
                $skill_names_to_ids['technical support'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Structured office setting' => 15],
                'How do you prefer to approach problem-solving?' => ['Systematically breaking down complex problems' => 10],
                'What kind of tasks do you find most engaging?' => ['Securing systems and preventing attacks' => 10],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 10, 'Learning from mentors' => 5],
                'How do you handle repetitive tasks?' => ['Very comfortable' => 15, 'Somewhat comfortable' => 10]
            ],
            'base_score' => 20
        ],
        // Project Manager (ID: 7)
        7 => [
            'key_skills' => [
                $skill_names_to_ids['project management'] ?? 0,
                $skill_names_to_ids['communication skills'] ?? 0,
                $skill_names_to_ids['teamwork'] ?? 0,
                $skill_names_to_ids['agile methodologies'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['jira'] ?? 0,
                $skill_names_to_ids['confluence'] ?? 0,
                $skill_names_to_ids['leadership'] ?? 0,
                $skill_names_to_ids['negotiation'] ?? 0,
                $skill_names_to_ids['critical thinking'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Collaborative team-based environment' => 15, 'Fast-paced, high-pressure environment' => 10],
                'How do you prefer to approach problem-solving?' => ['Collaborating with others' => 12],
                'What kind of tasks do you find most engaging?' => ['Managing projects and coordinating teams' => 20, 'Interacting directly with clients or users' => 15],
                'How do you prefer to learn new technologies or skills?' => ['Learning from mentors' => 10, 'Attending workshops and conferences' => 8],
                'How would you describe your communication style?' => ['Collaborative and consensus-driven' => 10, 'Direct and assertive' => 8],
                'Are you more of a detail-oriented person or a big-picture thinker?' => ['Mostly big-picture thinker' => 10, 'Strongly big-picture thinker' => 12],
                'What drives you most in a career?' => ['Making a significant impact' => 15, 'Work-life balance' => 10, 'Collaborative and supportive team culture' => 10]
            ],
            'base_score' => 20
        ],
        // Add more careers and their detailed scoring factors here
        // Example: Cloud Engineer
        8 => [ // Assuming ID 8 for Cloud Engineer, add this career via admin panel if not exists
            'key_skills' => [
                $skill_names_to_ids['aws (amazon web services)'] ?? 0,
                $skill_names_to_ids['azure (microsoft azure)'] ?? 0,
                $skill_names_to_ids['google cloud platform (gcp)'] ?? 0,
                $skill_names_to_ids['docker'] ?? 0,
                $skill_names_to_ids['kubernetes'] ?? 0,
                $skill_names_to_ids['terraform'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['linux/unix'] ?? 0,
                $skill_names_to_ids['networking fundamentals'] ?? 0,
                $skill_names_to_ids['serverless architecture'] ?? 0,
                $skill_names_to_ids['ci/cd pipelines'] ?? 0,
                $skill_names_to_ids['python'] ?? 0 // for scripting
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Flexible environment' => 10, 'Fast-paced, high-pressure environment' => 8],
                'How do you prefer to approach problem-solving?' => ['Experimenting and iterating' => 10, 'Systematically breaking down complex problems' => 8],
                'What kind of tasks do you find most engaging?' => ['Researching new technologies and concepts' => 15, 'Writing and debugging code' => 10],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 15],
                'Which of the following areas interests you most?' => ['Cloud Computing & DevOps' => 20],
                'How comfortable are you with continuous learning and adapting to new tools?' => ['Extremely comfortable' => 15, 'Comfortable' => 10],
                'How important is innovation and creating new things to you?' => ['Extremely important' => 12]
            ],
            'base_score' => 20
        ],
        // Example: AI/ML Engineer
        9 => [ // Assuming ID 9 for AI/ML Engineer
            'key_skills' => [
                $skill_names_to_ids['python'] ?? 0,
                $skill_names_to_ids['tensorflow'] ?? 0,
                $skill_names_to_ids['pytorch'] ?? 0,
                $skill_names_to_ids['deep learning'] ?? 0,
                $skill_names_to_ids['machine learning'] ?? 0,
                $skill_names_to_ids['natural language processing (nlp)'] ?? 0, // if specialized
                $skill_names_to_ids['computer vision'] ?? 0 // if specialized
            ],
            'relevant_skills' => [
                $skill_names_to_ids['data cleaning'] ?? 0,
                $skill_names_to_ids['statistical analysis'] ?? 0,
                $skill_names_to_ids['big data'] ?? 0,
                $skill_names_to_ids['problem solving'] ?? 0,
                $skill_names_to_ids['research skills'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Independent work' => 10, 'Flexible environment' => 8],
                'How do you prefer to approach problem-solving?' => ['Systematically breaking down complex problems' => 12, 'Experimenting and iterating' => 10],
                'What kind of tasks do you find most engaging?' => ['Analyzing data and identifying patterns' => 15, 'Researching new technologies and concepts' => 15, 'Writing and debugging code' => 10],
                'How do you prefer to learn new technologies or skills?' => ['Reading documentation and academic papers' => 15, 'Hands-on coding/building projects' => 10],
                'Which of the following areas interests you most?' => ['Artificial Intelligence & Machine Learning' => 25],
                'Are you comfortable with abstract concepts and theoretical frameworks?' => ['Very comfortable' => 15],
                'How much do you enjoy working with mathematical or statistical concepts?' => ['Highly enjoy' => 15]
            ],
            'base_score' => 20
        ],
        // Example: UX/UI Designer
        10 => [ // Assuming ID 10 for UX/UI Designer
            'key_skills' => [
                $skill_names_to_ids['ui/ux design principles'] ?? 0,
                $skill_names_to_ids['graphic design'] ?? 0,
                $skill_names_to_ids['responsive design'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['html5'] ?? 0,
                $skill_names_to_ids['css3'] ?? 0,
                $skill_names_to_ids['javascript'] ?? 0,
                $skill_names_to_ids['communication skills'] ?? 0,
                $skill_names_to_ids['critical thinking'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Flexible environment' => 15, 'Collaborative team-based environment' => 10],
                'How do you prefer to approach problem-solving?' => ['Brainstorming creative and unconventional solutions' => 15],
                'What kind of tasks do you find most engaging?' => ['Designing and creating visual interfaces or content' => 25, 'Interacting directly with clients or users' => 10],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 10, 'Watching video tutorials' => 8],
                'Which of the following areas interests you most?' => ['User Experience (UX) & Interface (UI) Design' => 25, 'Web & Mobile Application Development' => 10],
                'How important is innovation and creating new things to you?' => ['Extremely important' => 15, 'Very important' => 10]
            ],
            'base_score' => 20
        ],
        // Example: DevOps Engineer
        11 => [ // Assuming ID 11 for DevOps Engineer
            'key_skills' => [
                $skill_names_to_ids['docker'] ?? 0,
                $skill_names_to_ids['kubernetes'] ?? 0,
                $skill_names_to_ids['jenkins'] ?? 0,
                $skill_names_to_ids['git'] ?? 0,
                $skill_names_to_ids['ci/cd pipelines'] ?? 0,
                $skill_names_to_ids['ansible'] ?? 0,
                $skill_names_to_ids['terraform'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['linux/unix'] ?? 0,
                $skill_names_to_ids['aws (amazon web services)'] ?? 0,
                $skill_names_to_ids['azure (microsoft azure)'] ?? 0,
                $skill_names_to_ids['google cloud platform (gcp)'] ?? 0,
                $skill_names_to_ids['monitoring & logging'] ?? 0,
                $skill_names_to_ids['python'] ?? 0 // for scripting automation
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Flexible environment' => 10, 'Fast-paced, high-pressure environment' => 8],
                'How do you prefer to approach problem-solving?' => ['Systematically breaking down complex problems' => 10, 'Experimenting and iterating' => 8],
                'What kind of tasks do you find most engaging?' => ['Writing and debugging code' => 15, 'Researching new technologies and concepts' => 10],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 15],
                'Which of the following areas interests you most?' => ['Cloud Computing & DevOps' => 25],
                'How comfortable are you with continuous learning and adapting to new tools?' => ['Extremely comfortable' => 15],
                'How do you handle repetitive tasks?' => ['Strongly dislike repetitive tasks, I seek automation' => 10]
            ],
            'base_score' => 20
        ],
        // Example: Mobile App Developer
        12 => [ // Assuming ID 12 for Mobile App Developer
            'key_skills' => [
                $skill_names_to_ids['android development'] ?? 0,
                $skill_names_to_ids['ios development'] ?? 0,
                $skill_names_to_ids['react native'] ?? 0,
                $skill_names_to_ids['flutter'] ?? 0,
                $skill_names_to_ids['java'] ?? 0, // for Android
                $skill_names_to_ids['swift'] ?? 0 // for iOS
            ],
            'relevant_skills' => [
                $skill_names_to_ids['javascript'] ?? 0,
                $skill_names_to_ids['ui/ux design principles'] ?? 0,
                $skill_names_to_ids['restful apis'] ?? 0,
                $skill_names_to_ids['debugging'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Flexible environment' => 10, 'Collaborative team-based environment' => 8],
                'How do you prefer to approach problem-solving?' => ['Experimenting and iterating' => 10, 'Systematically breaking down complex problems' => 8],
                'What kind of tasks do you find most engaging?' => ['Writing and debugging code' => 20, 'Designing and creating visual interfaces' => 10],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 15],
                'Which of the following areas interests you most?' => ['Web & Mobile Application Development' => 25],
                'How important is innovation and creating new things to you?' => ['Extremely important' => 12, 'Very important' => 10]
            ],
            'base_score' => 20
        ],
        // Example: Game Developer
        13 => [ // Assuming ID 13 for Game Developer
            'key_skills' => [
                $skill_names_to_ids['unity 3d'] ?? 0,
                $skill_names_to_ids['unreal engine'] ?? 0,
                $skill_names_to_ids['c# (game dev)'] ?? 0,
                $skill_names_to_ids['c++ (game dev)'] ?? 0,
                $skill_names_to_ids['game design'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['3d modeling'] ?? 0,
                $skill_names_to_ids['animation'] ?? 0,
                $skill_names_to_ids['physics engines'] ?? 0,
                $skill_names_to_ids['problem solving'] ?? 0,
                $skill_names_to_ids['creativity'] ?? 0 // Assuming creativity is a skill
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Flexible environment' => 15, 'Collaborative team-based environment' => 10],
                'How do you prefer to approach problem-solving?' => ['Brainstorming creative and unconventional solutions' => 15, 'Experimenting and iterating' => 10],
                'What kind of tasks do you find most engaging?' => ['Designing and creating visual interfaces or content' => 20, 'Writing and debugging code' => 15],
                'How do you prefer to learn new technologies or skills?' => ['Hands-on coding/building projects' => 15],
                'Which of the following areas interests you most?' => ['Game Development & Interactive Media' => 25],
                'How important is innovation and creating new things to you?' => ['Extremely important' => 15]
            ],
            'base_score' => 20
        ],
        // Example: Data Analyst
        14 => [ // Assuming ID 14 for Data Analyst
            'key_skills' => [
                $skill_names_to_ids['data analysis'] ?? 0,
                $skill_names_to_ids['sql query optimization'] ?? 0,
                $skill_names_to_ids['excel'] ?? 0, // Assuming Excel skill exists
                $skill_names_to_ids['tableau'] ?? 0,
                $skill_names_to_ids['power bi'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['python'] ?? 0,
                $skill_names_to_ids['r'] ?? 0,
                $skill_names_to_ids['statistical analysis'] ?? 0,
                $skill_names_to_ids['data cleaning'] ?? 0,
                $skill_names_to_ids['critical thinking'] ?? 0,
                $skill_names_to_ids['communication skills'] ?? 0
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Structured office setting' => 10, 'Independent work' => 8],
                'How do you prefer to approach problem-solving?' => ['Systematically breaking down complex problems' => 15],
                'What kind of tasks do you find most engaging?' => ['Analyzing data and identifying patterns' => 20, 'Researching new technologies and concepts' => 10],
                'How do you prefer to learn new technologies or skills?' => ['Reading documentation and academic papers' => 10, 'Hands-on coding/building projects' => 8],
                'Which of the following areas interests you most?' => ['Data Analysis & Business Intelligence' => 25],
                'How much do you enjoy working with mathematical or statistical concepts?' => ['Highly enjoy' => 10, 'Moderately enjoy' => 8],
                'Are you more of a detail-oriented person or a big-picture thinker?' => ['Strongly detail-oriented' => 15, 'Mostly detail-oriented' => 10]
            ],
            'base_score' => 20
        ],
        // Example: Technical Writer
        15 => [ // Assuming ID 15 for Technical Writer
            'key_skills' => [
                $skill_names_to_ids['technical writing'] ?? 0,
                $skill_names_to_ids['communication skills'] ?? 0,
                $skill_names_to_ids['research skills'] ?? 0
            ],
            'relevant_skills' => [
                $skill_names_to_ids['content writing'] ?? 0,
                $skill_names_to_ids['documentation'] ?? 0, // Assuming documentation skill
                $skill_names_to_ids['attention to detail'] ?? 0 // Assuming attention to detail skill
            ],
            'ideal_answers' => [
                'What is your ideal work environment?' => ['Independent work' => 15, 'Structured office setting' => 10],
                'How do you prefer to approach problem-solving?' => ['Systematically breaking down complex problems' => 10],
                'What kind of tasks do you find most engaging?' => ['Researching new technologies and concepts' => 15, 'Interacting directly with clients or users' => 5],
                'How do you prefer to learn new technologies or skills?' => ['Reading documentation and academic papers' => 12, 'Learning from mentors' => 8],
                'How would you describe your communication style?' => ['Detailed and precise' => 15, 'Collaborative and consensus-driven' => 10],
                'Are you more of a detail-oriented person or a big-picture thinker?' => ['Strongly detail-oriented' => 15]
            ],
            'base_score' => 20
        ],
    ];

    foreach ($all_careers as $career) {
        $current_career_id = $career['id'];
        $current_career_title = $career['title'];
        $score = 0;

        if (isset($scoring_factors[$current_career_id])) {
            $factors = $scoring_factors[$current_career_id];
            $score += $factors['base_score'];

            // Score based on key skills
            foreach ($factors['key_skills'] as $skill_id) {
                if ($skill_id > 0 && in_array($skill_id, $user_skills_ids)) {
                    $score += 15; // High points for key skills
                }
            }

            // Score based on relevant skills
            foreach ($factors['relevant_skills'] as $skill_id) {
                if ($skill_id > 0 && in_array($skill_id, $user_skills_ids)) {
                    $score += 5; // Medium points for relevant skills
                }
            }

            // Score based on quiz answers
            foreach ($factors['ideal_answers'] as $question_text => $answer_contributions) {
                if (isset($user_answers[$question_text])) {
                    $user_answer = $user_answers[$question_text]; // This is the stored answer from DB
                    foreach ($answer_contributions as $expected_answer_substring => $contribution) {
                        // For checkbox answers, the stored answer is "Option1||Option2"
                        // So we need to check if the expected substring is within any of the selected options
                        $user_answer_parts = explode("||", $user_answer);
                        foreach ($user_answer_parts as $part) {
                            if (strpos(strtolower($part), strtolower($expected_answer_substring)) !== false) {
                                $score += $contribution;
                                break; // Add contribution once per question if any part matches
                            }
                        }
                    }
                }
            }
        }
        $career_scores[$current_career_title] = $score;
    }

    // Sort careers by score in descending order
    arsort($career_scores);

    return $career_scores;
}
?>
