<?php
// algorithms/course_recommendation.php

function getCourseRecommendations($conn, $suggested_skill_ids) {
    $recommended_courses = [];

    if (empty($suggested_skill_ids)) {
        return $recommended_courses; // No skills, no recommendations
    }

    // Create placeholders and bind types
    $placeholders = implode(',', array_fill(0, count($suggested_skill_ids), '?'));
    $types = str_repeat('i', count($suggested_skill_ids));

    // SQL query with LIMIT 5
    $sql = "
        SELECT 
            courses.id, 
            courses.title, 
            courses.description, 
            courses.url, 
            skills.name AS skill_name
        FROM 
            courses
        LEFT JOIN 
            skills ON courses.skill_id = skills.id
        WHERE 
            courses.skill_id IN ($placeholders)
            OR courses.skill_id IS NULL
        ORDER BY 
            CASE 
                WHEN courses.skill_id IS NULL THEN 1
                ELSE 0
            END ASC
        LIMIT 3
    ";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, $types, ...$suggested_skill_ids);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $recommended_courses[] = $row;
        }

        mysqli_stmt_close($stmt);
    }

    return $recommended_courses;
}
