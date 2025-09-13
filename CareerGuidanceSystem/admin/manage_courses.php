<?php
// admin/manage_courses.php
session_start();
require_once '../config/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../admin_login.php');
    exit();
}

$message = '';
$message_type = ''; // success or error

// Handle form submissions for Add, Edit, Delete
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'] ?? null;
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $url = trim($_POST['url']);
    $skill_id = $_POST['skill_id'] ?? null;

    // Convert 0 from dropdown to actual NULL for DB
    if ($skill_id == '0' || empty($skill_id)) {
        $skill_id = null;
    }

    if (empty($title)) {
        $message = "Course title cannot be empty.";
        $message_type = "error";
    } else {
        if ($course_id) {
            // Update existing course
            $sql = "UPDATE courses SET title = ?, description = ?, url = ?, skill_id = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssii", $title, $description, $url, $skill_id, $course_id);
                if (mysqli_stmt_execute($stmt)) {
                    $message = "Course updated successfully!";
                    $message_type = "success";
                } else {
                    $message = "Error updating course: " . mysqli_stmt_error($stmt);
                    $message_type = "error";
                }
                mysqli_stmt_close($stmt);
            } else {
                $message = "Error preparing update statement: " . mysqli_error($conn);
                $message_type = "error";
            }
        } else {
            // Add new course
            $sql = "INSERT INTO courses (title, description, url, skill_id) VALUES (?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssi", $title, $description, $url, $skill_id);
                if (mysqli_stmt_execute($stmt)) {
                    $message = "Course added successfully!";
                    $message_type = "success";
                } else {
                    $message = "Error adding course: " . mysqli_error($conn);
                    $message_type = "error";
                }
                mysqli_stmt_close($stmt);
            } else {
                $message = "Error preparing insert statement: " . mysqli_error($conn);
                $message_type = "error";
            }
        }
    }
}

// Handle Delete Course (moved from GET to POST for consistency and security)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_course'])) {
    $course_id_to_delete = $_POST['id'];
    $sql = "DELETE FROM courses WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $course_id_to_delete);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Course deleted successfully!";
            $message_type = "success";
        } else {
            $message = "Error deleting course: " . mysqli_error($conn);
            $message_type = "error";
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "Error preparing delete statement: " . mysqli_error($conn);
        $message_type = "error";
    }
}

// Handle message from redirect (if any, e.g., from a previous delete action)
if (isset($_GET['message']) && isset($_GET['type'])) {
    $message = htmlspecialchars($_GET['message']);
    $message_type = htmlspecialchars($_GET['type']);
}


// Fetch all skills for the dropdown (needed for both add and edit forms)
$all_skills = [];
$sql_fetch_skills = "SELECT id, name FROM skills ORDER BY name ASC";
$result_skills = mysqli_query($conn, $sql_fetch_skills);
if ($result_skills) {
    while ($row = mysqli_fetch_assoc($result_skills)) {
        $all_skills[] = $row;
    }
} else {
    error_log("Error fetching skills for dropdown: " . mysqli_error($conn));
}


// Fetch all courses for display (this must happen AFTER all POST/GET handling)
$courses = [];
$sql_fetch_courses = "SELECT c.id, c.title, c.description, c.url, s.name AS skill_name, s.id AS skill_id
                      FROM courses c
                      LEFT JOIN skills s ON c.skill_id = s.id
                      ORDER BY c.title ASC";
$result_courses = mysqli_query($conn, $sql_fetch_courses);
if ($result_courses) {
    while ($row = mysqli_fetch_assoc($result_courses)) {
        $courses[] = $row;
    }
} else {
    error_log("Error fetching courses for display: " . mysqli_error($conn));
}

mysqli_close($conn); // Close connection at the very end
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Styles copied from admin/dashboard.php and admin/manage_skills.php for consistency */
        body {
            background-color: #e9ecef; /* Slightly different background for admin area to distinguish */
        }
        .admin-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px; /* More rounded corners */
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); /* Stronger shadow */
            text-align: center;
        }
        .admin-container h1 {
            color: #dc3545; /* Red color for admin distinction (e.g., danger/management) */
            margin-bottom: 20px;
            font-size: 2.2em;
        }
        .admin-container p {
            color: #666;
            margin-bottom: 30px;
        }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* Form Section Styles (reused from other admin pages) */
        .form-section {
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 5px;
            margin-bottom: 30px;
            text-align: left; /* Align form labels/inputs left */
        }
        .form-section h3 {
            margin-top: 0;
            color: #dc3545; /* Consistent admin color */
            border-bottom: 2px solid #dc3545;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-group .btn-submit,
        .btn-group .btn-info {
            flex: 1;
        }
        /* Button Styles (reused from other admin pages) */
        .btn-submit {
            background-color: #28a745; /* Green for save/submit */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            text-decoration: none; /* For button-like links */
            display: inline-block;
            text-align: center;
        }
        .btn-submit:hover { background-color: #218838; }

        .btn-info {
            background-color: #17a2b8; /* Blue for info/edit */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            text-decoration: none; /* For button-like links */
            display: inline-block;
            text-align: center;
        }
        .btn-info:hover { background-color: #138496; }

        .btn-danger {
            background-color: #dc3545; /* Red for delete */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            text-decoration: none; /* For button-like links */
            display: inline-block;
            text-align: center;
        }
        .btn-danger:hover { background-color: #c82333; }

        /* Table Styles (reused and enhanced for responsiveness) */
        .table-responsive {
            overflow-x: auto; /* Allows horizontal scrolling on small screens */
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 0.95em;
            background-color: #fff; /* Ensure table background is white */
            border-radius: 8px; /* Rounded corners for the table */
            overflow: hidden; /* Ensures border-radius applies to content */
            box-shadow: 0 2px 8px rgba(0,0,0,0.05); /* Subtle shadow for table */
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 12px 8px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .action-buttons {
            display: flex;
            gap: 5px; /* Space between buttons */
            flex-wrap: wrap; /* Allow buttons to wrap on small screens */
            justify-content: center; /* Center buttons if they wrap */
        }

        .action-buttons .btn-info,
        .action-buttons .btn-danger {
            padding: 8px 12px; /* Smaller padding for action buttons */
            font-size: 0.9em; /* Smaller font for action buttons */
            width: auto; /* Allow buttons to size naturally */
            min-width: 70px; /* Ensure minimum width for readability */
        }

        /* Back link styles */
        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
        }
        .back-link a {
            background-color: #6c757d; /* Grey button for back link */
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            display: inline-block;
        }
        .back-link a:hover {
            background-color: #5a6268;
        }

        /* Edit form hidden by default */
        .edit-form-container {
            display: none; /* Hidden by default */
            margin-top: 20px;
            padding: 15px;
            background-color: #e2f0ff;
            border: 1px solid #a8d9ff;
            border-radius: 5px;
            text-align: left; /* Align form labels/inputs left */
        }

        /* Responsive adjustments for tables on smaller screens */
        @media only screen and (max-width: 600px) {
            .admin-container {
                margin: 20px auto;
                padding: 15px;
                box-shadow: none;
                border-radius: 0;
            }
            .admin-container h1 {
                font-size: 1.8em;
            }
            .form-section {
                padding: 15px;
            }
            .form-section h3 {
                font-size: 1.2em;
            }
            .form-group input[type="text"],
            .form-group textarea,
            .form-group select {
                font-size: 14px;
                padding: 8px;
            }
            .btn-group {
                flex-direction: column;
                gap: 8px;
            }
            .btn-group .btn-submit,
            .btn-group .btn-info {
                width: 100%;
            }

            table, thead, tbody, th, td, tr {
                display: block; /* Make table elements behave like blocks */
            }
            thead tr {
                position: absolute;
                top: -9999px; /* Hide table headers visually */
                left: -9999px;
            }
            tr {
                border: 1px solid #ccc;
                margin-bottom: 10px;
                border-radius: 5px;
                overflow: hidden; /* Hide overflow from border-radius */
            }
            td {
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%; /* Space for the data label */
                text-align: right;
            }
            td:last-child {
                border-bottom: 0;
            }
            td::before {
                /* Use the data-label attribute to display column headers */
                content: attr(data-label);
                position: absolute;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                text-align: left;
                font-weight: bold;
                color: #0056b3;
            }
            .action-buttons {
                justify-content: flex-end; /* Align action buttons to the right */
                padding: 10px;
                background-color: #f9f9f9;
                border-top: 1px solid #eee;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Manage Courses</h1>
        <p style="text-align: center;">Add, edit, or delete online courses and link them to relevant skills.</p>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="form-section">
            <h3>Add New Course</h3>
            <form action="manage_courses.php" method="POST">
                <input type="hidden" name="course_id" value="">

                <div class="form-group">
                    <label for="add_title">Course Title:</label>
                    <input type="text" id="add_title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="add_description">Description:</label>
                    <textarea id="add_description" name="description" rows="4" placeholder="Brief description of the course content."></textarea>
                </div>
                <div class="form-group">
                    <label for="add_url">Course URL:</label>
                    <input type="text" id="add_url" name="url" placeholder="e.g., https://www.udemy.com/course/python-basics">
                </div>
                <div class="form-group">
                    <label for="add_skill_id">Related Skill:</label>
                    <select id="add_skill_id" name="skill_id">
                        <option value="0">-- Select a Skill (Optional) --</option>
                        <?php foreach ($all_skills as $skill): ?>
                            <option value="<?php echo $skill['id']; ?>">
                                <?php echo htmlspecialchars($skill['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small>Link this course to a specific skill if applicable.</small>
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn-submit">Add Course</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <h3>Existing Courses</h3>
            <?php if (!empty($courses)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Description</th> <!-- Corrected typo here -->
                            <th>URL</th>
                            <th>Related Skill</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                            <tr id="course-row-<?php echo $course['id']; ?>">
                                <td data-label="ID"><?php echo htmlspecialchars($course['id']); ?></td>
                                <td data-label="Title"><?php echo htmlspecialchars($course['title']); ?></td>
                                <td data-label="Description"><?php echo htmlspecialchars(substr($course['description'], 0, 100)) . (strlen($course['description']) > 100 ? '...' : ''); ?></td>
                                <td data-label="URL">
                                    <?php if (!empty($course['url'])): ?>
                                        <a href="<?php echo htmlspecialchars($course['url']); ?>" target="_blank">Link</a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td data-label="Related Skill"><?php echo htmlspecialchars($course['skill_name'] ?? 'N/A'); ?></td>
                                <td class="action-buttons" data-label="Actions">
                                    <button class="btn-info" onclick="toggleEditForm(<?php echo $course['id']; ?>, '<?php echo htmlspecialchars(addslashes($course['title'])); ?>', '<?php echo htmlspecialchars(addslashes($course['description'])); ?>', '<?php echo htmlspecialchars(addslashes($course['url'])); ?>', '<?php echo htmlspecialchars($course['skill_id'] ?? '0'); ?>')">Edit</button>
                                    <form action="manage_courses.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                        <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
                                        <button type="submit" name="delete_course" class="btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <!-- Edit Form (hidden by default) -->
                            <tr class="edit-form-row" id="edit-form-row-<?php echo $course['id']; ?>" style="display: none;">
                                <td colspan="6"> <!-- Span across all columns -->
                                    <div class="edit-form-container">
                                        <h4>Edit Course (ID: <?php echo htmlspecialchars($course['id']); ?>)</h4>
                                        <form action="manage_courses.php" method="POST">
                                            <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course['id']); ?>">
                                            <div class="form-group">
                                                <label for="edit_title_<?php echo $course['id']; ?>">Course Title:</label>
                                                <input type="text" id="edit_title_<?php echo $course['id']; ?>" name="title" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_description_<?php echo $course['id']; ?>">Description:</label>
                                                <textarea id="edit_description_<?php echo $course['id']; ?>" name="description" rows="4" placeholder="Brief description of the course content."></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_url_<?php echo $course['id']; ?>">Course URL:</label>
                                                <input type="text" id="edit_url_<?php echo $course['id']; ?>" name="url" placeholder="e.g., https://www.udemy.com/course/python-basics">
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_skill_id_<?php echo $course['id']; ?>">Related Skill:</label>
                                                <select id="edit_skill_id_<?php echo $course['id']; ?>" name="skill_id">
                                                    <option value="0">-- Select a Skill (Optional) --</option>
                                                    <?php foreach ($all_skills as $skill): ?>
                                                        <option value="<?php echo $skill['id']; ?>">
                                                            <?php echo htmlspecialchars($skill['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <small>Link this course to a specific skill if applicable.</small>
                                            </div>
                                            <div class="btn-group">
                                                <button type="submit" class="btn-submit">Update Course</button>
                                                <button type="button" class="btn-info" onclick="toggleEditForm(<?php echo $course['id']; ?>)">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No courses added yet. Use the form above to add your first course!</p>
            <?php endif; ?>
        </div>

        <div class="back-link">
            <p><a href="dashboard.php">Back to Admin Dashboard</a></p>
        </div>
    </div>

    <script>
        function toggleEditForm(courseId, title = '', description = '', url = '', skillId = '0') {
            const formRow = document.getElementById('edit-form-row-' + courseId);
            const editContainer = formRow.querySelector('.edit-form-container'); // Get the inner div

            if (formRow.style.display === 'table-row') {
                formRow.style.display = 'none';
                editContainer.style.display = 'none'; // Ensure inner div is also hidden
            } else {
                // Hide all other edit forms
                document.querySelectorAll('.edit-form-row').forEach(otherFormRow => {
                    if (otherFormRow.id !== 'edit-form-row-' + courseId) {
                        otherFormRow.style.display = 'none';
                        // Ensure the inner container is also hidden for other forms
                        const otherEditContainer = otherFormRow.querySelector('.edit-form-container');
                        if (otherEditContainer) {
                            otherEditContainer.style.display = 'none';
                        }
                    }
                });

                // Populate and show the clicked form
                document.getElementById('edit_title_' + courseId).value = title;
                document.getElementById('edit_description_' + courseId).value = description;
                document.getElementById('edit_url_' + courseId).value = url;
                document.getElementById('edit_skill_id_' + courseId).value = skillId;

                formRow.style.display = 'table-row'; // Show the table row
                editContainer.style.display = 'block'; // Show the inner container

                // Scroll to the form
                formRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    </script>
</body>
</html>
