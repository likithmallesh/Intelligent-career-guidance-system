<?php
// algorithms/association_rule_mining.php

/**
 * Recommends new skills based on existing user skills using pre-defined association rules.
 *
 * @param array $user_skills_ids An array of skill IDs the user possesses.
 * @param array $all_skills An array of all available skills (id, name).
 * @return array An array of suggested skill names.
 */
function getAssociationSkillSuggestions($user_skills_ids, $all_skills) {
    $suggested_skills_ids = [];
    $all_skill_names_map = []; // Map ID to Name for output
    foreach ($all_skills as $skill) {
        $all_skill_names_map[$skill['id']] = $skill['name'];
        $skill_names_to_ids[strtolower($skill['name'])] = $skill['id']; // Also create name to ID map
    }

    // Define expanded association rules:
    // If a user has skill A, suggest skill B, C, etc.
    // Key = existing skill ID, Value = array of suggested skill IDs
    $association_rules = [
        // Core Programming Language Associations
        ($skill_names_to_ids['python'] ?? 0) => [
            $skill_names_to_ids['pandas'] ?? 0, $skill_names_to_ids['numpy'] ?? 0, $skill_names_to_ids['tensorflow'] ?? 0,
            $skill_names_to_ids['flask'] ?? 0, $skill_names_to_ids['django'] ?? 0, $skill_names_to_ids['data analysis'] ?? 0
        ],
        ($skill_names_to_ids['java'] ?? 0) => [
            $skill_names_to_ids['spring boot'] ?? 0, $skill_names_to_ids['android development'] ?? 0, $skill_names_to_ids['microservices'] ?? 0
        ],
        ($skill_names_to_ids['javascript'] ?? 0) => [
            $skill_names_to_ids['react.js'] ?? 0, $skill_names_to_ids['node.js'] ?? 0, $skill_names_to_ids['express.js'] ?? 0,
            $skill_names_to_ids['angular'] ?? 0, $skill_names_to_ids['vue.js'] ?? 0, $skill_names_to_ids['typescript'] ?? 0
        ],
        ($skill_names_to_ids['c++'] ?? 0) => [
            $skill_names_to_ids['unreal engine'] ?? 0, $skill_names_to_ids['game development'] ?? 0, $skill_names_to_ids['data structures'] ?? 0 // Assuming DS skill
        ],
        ($skill_names_to_ids['c#'] ?? 0) => [
            $skill_names_to_ids['.net core'] ?? 0, $skill_names_to_ids['unity 3d'] ?? 0, $skill_names_to_ids['game development'] ?? 0
        ],

        // Web Development Associations
        ($skill_names_to_ids['html5'] ?? 0) => [
            $skill_names_to_ids['css3'] ?? 0, $skill_names_to_ids['javascript'] ?? 0, $skill_names_to_ids['responsive design'] ?? 0
        ],
        ($skill_names_to_ids['css3'] ?? 0) => [
            $skill_names_to_ids['tailwind css'] ?? 0, $skill_names_to_ids['sass/less'] ?? 0, $skill_names_to_ids['ui/ux design principles'] ?? 0
        ],
        ($skill_names_to_ids['react.js'] ?? 0) => [
            $skill_names_to_ids['node.js'] ?? 0, $skill_names_to_ids['redux'] ?? 0, $skill_names_to_ids['graphql'] ?? 0 // Assuming Redux skill
        ],

        // Database Associations
        ($skill_names_to_ids['mysql'] ?? 0) => [
            $skill_names_to_ids['sql query optimization'] ?? 0, $skill_names_to_ids['database design'] ?? 0
        ],
        ($skill_names_to_ids['mongodb'] ?? 0) => [
            $skill_names_to_ids['node.js'] ?? 0, $skill_names_to_ids['nosql databases'] ?? 0
        ],

        // Cloud & DevOps Associations
        ($skill_names_to_ids['aws (amazon web services)'] ?? 0) => [
            $skill_names_to_ids['docker'] ?? 0, $skill_names_to_ids['kubernetes'] ?? 0, $skill_names_to_ids['terraform'] ?? 0,
            $skill_names_to_ids['cloud security'] ?? 0, $skill_names_to_ids['serverless computing'] ?? 0
        ],
        ($skill_names_to_ids['docker'] ?? 0) => [
            $skill_names_to_ids['kubernetes'] ?? 0, $skill_names_to_ids['ci/cd pipelines'] ?? 0, $skill_names_to_ids['microservices'] ?? 0
        ],
        ($skill_names_to_ids['git'] ?? 0) => [
            $skill_names_to_ids['github'] ?? 0, $skill_names_to_ids['gitlab'] ?? 0, $skill_names_to_ids['ci/cd pipelines'] ?? 0
        ],
        ($skill_names_to_ids['jenkins'] ?? 0) => [
            $skill_names_to_ids['ci/cd pipelines'] ?? 0, $skill_names_to_ids['docker'] ?? 0, $skill_names_to_ids['ansible'] ?? 0
        ],

        // Data Science Associations
        ($skill_names_to_ids['data analysis'] ?? 0) => [
            $skill_names_to_ids['statistical analysis'] ?? 0, $skill_names_to_ids['data cleaning'] ?? 0,
            $skill_names_to_ids['tableau'] ?? 0, $skill_names_to_ids['power bi'] ?? 0
        ],
        ($skill_names_to_ids['machine learning'] ?? 0) => [
            $skill_names_to_ids['deep learning'] ?? 0, $skill_names_to_ids['tensorflow'] ?? 0, $skill_names_to_ids['pytorch'] ?? 0
        ],

        // Cybersecurity Associations
        ($skill_names_to_ids['network security'] ?? 0) => [
            $skill_names_to_ids['firewalls'] ?? 0, $skill_names_to_ids['vulnerability assessment'] ?? 0,
            $skill_names_to_ids['incident response'] ?? 0
        ],
        ($skill_names_to_ids['cybersecurity'] ?? 0) => [
            $skill_names_to_ids['penetration testing'] ?? 0, $skill_names_to_ids['cryptography'] ?? 0,
            $skill_names_to_ids['siem'] ?? 0 // Assuming SIEM skill
        ],

        // General IT & Soft Skill Associations
        ($skill_names_to_ids['problem solving'] ?? 0) => [
            $skill_names_to_ids['critical thinking'] ?? 0, $skill_names_to_ids['debugging'] ?? 0
        ],
        ($skill_names_to_ids['project management'] ?? 0) => [
            $skill_names_to_ids['agile methodologies'] ?? 0, $skill_names_to_ids['scrum'] ?? 0, $skill_names_to_ids['jira'] ?? 0
        ],
        ($skill_names_to_ids['communication skills'] ?? 0) => [
            $skill_names_to_ids['technical writing'] ?? 0, $skill_names_to_ids['public speaking'] ?? 0
        ],
    ];

    foreach ($user_skills_ids as $skill_id) {
        if (isset($association_rules[$skill_id])) {
            foreach ($association_rules[$skill_id] as $suggested_id) {
                // Only suggest if the user doesn't already have the skill
                if (!in_array($suggested_id, $user_skills_ids) && !in_array($suggested_id, $suggested_skills_ids)) {
                    $suggested_skills_ids[] = $suggested_id;
                }
            }
        }
    }

    // Convert suggested skill IDs to names
    $suggested_skill_names = [];
    foreach ($suggested_skills_ids as $id) {
        if (isset($all_skill_names_map[$id])) {
            $suggested_skill_names[] = $all_skill_names_map[$id];
        }
    }

    // Limit suggestions to a reasonable number, e.g., top 10 or 15
    return array_slice($suggested_skill_names, 0, 15);
}
?>
