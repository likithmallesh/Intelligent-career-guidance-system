<?php
// admin/manage_skills.php
session_start();
require_once '../config/config.php';

// Check if user is logged in and is an administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../admin_login.php'); // Redirect to admin login if not authorized
    exit();
}

$message = '';
$message_type = ''; // 'success' or 'error'

// Handle form submissions for Add, Edit, Delete
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_skill'])) {
        $name = trim($_POST['name']);

        if (!empty($name)) {
            // Check if skill already exists
            $sql_check = "SELECT id FROM skills WHERE name = ?";
            if ($stmt_check = mysqli_prepare($conn, $sql_check)) {
                mysqli_stmt_bind_param($stmt_check, "s", $name);
                mysqli_stmt_execute($stmt_check);
                mysqli_stmt_store_result($stmt_check);
                if (mysqli_stmt_num_rows($stmt_check) > 0) {
                    $message = "Skill with this name already exists.";
                    $message_type = "error";
                } else {
                    $sql = "INSERT INTO skills (name) VALUES (?)";
                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $name);
                        if (mysqli_stmt_execute($stmt)) {
                            $message = "Skill added successfully!";
                            $message_type = "success";
                        } else {
                            $message = "Error adding skill: " . mysqli_error($conn);
                            $message_type = "error";
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $message = "Failed to prepare add skill statement: " . mysqli_error($conn);
                        $message_type = "error";
                    }
                }
                mysqli_stmt_close($stmt_check);
            } else {
                $message = "Failed to prepare skill existence check: " . mysqli_error($conn);
                $message_type = "error";
            }
        } else {
            $message = "Skill Name cannot be empty.";
            $message_type = "error";
        }
    } elseif (isset($_POST['update_skill'])) {
        $id = $_POST['id'];
        $name = trim($_POST['name']);

        if (!empty($name) && !empty($id)) {
             // Check for duplicate name (excluding current skill itself)
            $sql_check = "SELECT id FROM skills WHERE name = ? AND id != ?";
            if ($stmt_check = mysqli_prepare($conn, $sql_check)) {
                mysqli_stmt_bind_param($stmt_check, "si", $name, $id);
                mysqli_stmt_execute($stmt_check);
                mysqli_stmt_store_result($stmt_check);
                if (mysqli_stmt_num_rows($stmt_check) > 0) {
                    $message = "Another skill with this name already exists.";
                    $message_type = "error";
                } else {
                    $sql = "UPDATE skills SET name = ? WHERE id = ?";
                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        mysqli_stmt_bind_param($stmt, "si", $name, $id);
                        if (mysqli_stmt_execute($stmt)) {
                            $message = "Skill updated successfully!";
                            $message_type = "success";
                        } else {
                            $message = "Error updating skill: " . mysqli_error($conn);
                            $message_type = "error";
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $message = "Failed to prepare update skill statement: " . mysqli_error($conn);
                        $message_type = "error";
                    }
                }
                mysqli_stmt_close($stmt_check);
            } else {
                $message = "Failed to prepare skill existence check for update: " . mysqli_error($conn);
                $message_type = "error";
            }
        } else {
            $message = "Skill Name is required for update.";
            $message_type = "error";
        }
    } elseif (isset($_POST['delete_skill'])) {
        $id = $_POST['id'];
        if (!empty($id)) {
            // Check for related records in 'user_skills' before deleting skill
            $sql_check_user_skills = "SELECT COUNT(*) FROM user_skills WHERE skill_id = ?";
            if ($stmt_check = mysqli_prepare($conn, $sql_check_user_skills)) {
                mysqli_stmt_bind_param($stmt_check, "i", $id);
                mysqli_stmt_execute($stmt_check);
                mysqli_stmt_bind_result($stmt_check, $count_user_skills);
                mysqli_stmt_fetch($stmt_check);
                mysqli_stmt_close($stmt_check);

                if ($count_user_skills > 0) {
                    $message = "Cannot delete skill. It is linked to " . $count_user_skills . " user(s). You must delete these user skills first (e.g., via phpMyAdmin, or by deleting the associated users).";
                    $message_type = "error";
                } else {
                    $sql = "DELETE FROM skills WHERE id = ?";
                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        mysqli_stmt_bind_param($stmt, "i", $id);
                        if (mysqli_stmt_execute($stmt)) {
                            $message = "Skill deleted successfully!";
                            $message_type = "success";
                        } else {
                            $message = "Error deleting skill: " . mysqli_error($conn);
                            $message_type = "error";
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $message = "Failed to prepare delete skill statement: " . mysqli_error($conn);
                        $message_type = "error";
                    }
                }
            } else {
                $message = "Failed to prepare check for user skills: " . mysqli_error($conn);
                $message_type = "error";
            }
        } else {
            $message = "Skill ID not provided for deletion.";
            $message_type = "error";
        }
    }
}

// Fetch all skills for display
$skills = [];
$sql_fetch_skills = "SELECT id, name FROM skills ORDER BY name ASC";
$result_skills = mysqli_query($conn, $sql_fetch_skills);
if ($result_skills) {
    while ($row = mysqli_fetch_assoc($result_skills)) {
        $skills[] = $row;
    }
} else {
    $message = "Error fetching skills: " . mysqli_error($conn);
    $message_type = "error";
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Skills - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-container { max-width: 900px; margin: 50px auto; padding: 30px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .admin-container h1 { color: #dc3545; margin-bottom: 20px; text-align: center; }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .form-section { margin-bottom: 30px; padding: 20px; border: 1px solid #eee; border-radius: 5px; }
        .form-section h3 { margin-top: 0; color: #dc3545; border-bottom: 2px solid #dc3545; padding-bottom: 10px; margin-bottom: 20px;}
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="text"] {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .btn-submit, .btn-action {
            background-color: #dc3545;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .btn-submit:hover, .btn-action:hover { background-color: #c82333; }
        .btn-update { background-color: #007bff; }
        .btn-update:hover { background-color: #0056b3; }
        .btn-delete { background-color: #6c757d; }
        .btn-delete:hover { background-color: #5a6268; }

        .skill-list { margin-top: 30px; }
        .skill-item {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            border: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .skill-item h4 { margin: 0; color: #007bff; }
        .skill-actions { display: flex; align-items: center; }
        .skill-actions button { margin-left: 10px; }

        /* Edit form hidden by default */
        .edit-form-container {
            display: none; /* Hidden by default */
            margin-top: 20px;
            padding: 15px;
            background-color: #e2f0ff;
            border: 1px solid #a8d9ff;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Manage Skills</h1>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h3>Add New Skill</h3>
            <form action="manage_skills.php" method="POST">
                <div class="form-group">
                    <label for="add_name">Skill Name:</label>
                    <input type="text" id="add_name" name="name" required>
                </div>
                <button type="submit" name="add_skill" class="btn-submit">Add Skill</button>
            </form>
        </div>

        <div class="skill-list">
            <h3>Existing Skills</h3>
            <?php if (!empty($skills)): ?>
                <?php foreach ($skills as $skill): ?>
                    <div class="skill-item" id="skill-<?php echo $skill['id']; ?>">
                        <h4><?php echo htmlspecialchars($skill['name']); ?> (ID: <?php echo $skill['id']; ?>)</h4>
                        <div class="skill-actions">
                            <button class="btn-action btn-update" onclick="toggleEditForm(<?php echo $skill['id']; ?>, '<?php echo htmlspecialchars(addslashes($skill['name'])); ?>')">Edit</button>
                            <form action="manage_skills.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this skill? This cannot be undone if linked to users.');">
                                <input type="hidden" name="id" value="<?php echo $skill['id']; ?>">
                                <button type="submit" name="delete_skill" class="btn-action btn-delete">Delete</button>
                            </form>
                        </div>

                        <!-- Edit Form (hidden by default) -->
                        <div class="edit-form-container" id="edit-form-<?php echo $skill['id']; ?>">
                            <h4>Edit Skill</h4>
                            <form action="manage_skills.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $skill['id']; ?>">
                                <div class="form-group">
                                    <label for="edit_name_<?php echo $skill['id']; ?>">Skill Name:</label>
                                    <input type="text" id="edit_name_<?php echo $skill['id']; ?>" name="name" value="<?php echo htmlspecialchars($skill['name']); ?>" required>
                                </div>
                                <button type="submit" name="update_skill" class="btn-submit btn-update">Update Skill</button>
                                <button type="button" class="btn-action btn-delete" onclick="toggleEditForm(<?php echo $skill['id']; ?>)">Cancel</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No skills found. Add one above!</p>
            <?php endif; ?>
        </div>

        <div class="back-link" style="text-align: center; margin-top: 30px;">
            <p><a href="dashboard.php" class="btn-action">Back to Admin Dashboard</a></p>
        </div>
    </div>

    <script>
        function toggleEditForm(skillId, name = '') {
            const form = document.getElementById('edit-form-' + skillId);
            if (form.style.display === 'block') {
                form.style.display = 'none';
            } else {
                // Hide all other edit forms
                document.querySelectorAll('.edit-form-container').forEach(otherForm => {
                    if (otherForm.id !== 'edit-form-' + skillId) {
                        otherForm.style.display = 'none';
                    }
                });

                // Populate and show the clicked form
                document.getElementById('edit_name_' + skillId).value = name;
                form.style.display = 'block';
            }
        }
    </script>
</body>
</html>

