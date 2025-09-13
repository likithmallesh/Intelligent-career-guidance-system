<?php
// admin/manage_careers.php
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
    if (isset($_POST['add_career'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);

        if (!empty($title) && !empty($description)) {
            // Check if career title already exists
            $sql_check = "SELECT id FROM careers WHERE title = ?";
            if ($stmt_check = mysqli_prepare($conn, $sql_check)) {
                mysqli_stmt_bind_param($stmt_check, "s", $title);
                mysqli_stmt_execute($stmt_check);
                mysqli_stmt_store_result($stmt_check);
                if (mysqli_stmt_num_rows($stmt_check) > 0) {
                    $message = "A career with this title already exists.";
                    $message_type = "error";
                } else {
                    $sql = "INSERT INTO careers (title, description) VALUES (?, ?)";
                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        mysqli_stmt_bind_param($stmt, "ss", $title, $description);
                        if (mysqli_stmt_execute($stmt)) {
                            $message = "Career added successfully!";
                            $message_type = "success";
                        } else {
                            $message = "Error adding career: " . mysqli_error($conn);
                            $message_type = "error";
                        }
                        mysqli_stmt_close($stmt);
                    }
                }
                mysqli_stmt_close($stmt_check);
            }
        } else {
            $message = "Title and Description cannot be empty.";
            $message_type = "error";
        }
    } elseif (isset($_POST['update_career'])) {
        $id = $_POST['id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);

        if (!empty($title) && !empty($description) && !empty($id)) {
            // Check for duplicate title (excluding current career itself)
            $sql_check = "SELECT id FROM careers WHERE title = ? AND id != ?";
            if ($stmt_check = mysqli_prepare($conn, $sql_check)) {
                mysqli_stmt_bind_param($stmt_check, "si", $title, $id);
                mysqli_stmt_execute($stmt_check);
                mysqli_stmt_store_result($stmt_check);
                if (mysqli_stmt_num_rows($stmt_check) > 0) {
                    $message = "Another career with this title already exists.";
                    $message_type = "error";
                } else {
                    $sql = "UPDATE careers SET title = ?, description = ? WHERE id = ?";
                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        mysqli_stmt_bind_param($stmt, "ssi", $title, $description, $id);
                        if (mysqli_stmt_execute($stmt)) {
                            $message = "Career updated successfully!";
                            $message_type = "success";
                        } else {
                            $message = "Error updating career: " . mysqli_error($conn);
                            $message_type = "error";
                        }
                        mysqli_stmt_close($stmt);
                    }
                }
                mysqli_stmt_close($stmt_check);
            }
        } else {
            $message = "All fields are required for update.";
            $message_type = "error";
        }
    } elseif (isset($_POST['delete_career'])) {
        $id = $_POST['id'];
        if (!empty($id)) {
            // Check for related records in 'recommendations' before deleting career
            $sql_check_recommendations = "SELECT COUNT(*) FROM recommendations WHERE career_id = ?";
            if ($stmt_check = mysqli_prepare($conn, $sql_check_recommendations)) {
                mysqli_stmt_bind_param($stmt_check, "i", $id);
                mysqli_stmt_execute($stmt_check);
                mysqli_stmt_bind_result($stmt_check, $count_recommendations);
                mysqli_stmt_fetch($stmt_check);
                mysqli_stmt_close($stmt_check);

                if ($count_recommendations > 0) {
                    $message = "Cannot delete career. It is linked to " . $count_recommendations . " user recommendation(s). You must delete these recommendations first (e.g., via phpMyAdmin, or by deleting the associated users).";
                    $message_type = "error";
                } else {
                    $sql = "DELETE FROM careers WHERE id = ?";
                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        mysqli_stmt_bind_param($stmt, "i", $id);
                        if (mysqli_stmt_execute($stmt)) {
                            $message = "Career deleted successfully!";
                            $message_type = "success";
                        } else {
                            $message = "Error deleting career: " . mysqli_error($conn);
                            $message_type = "error";
                        }
                        mysqli_stmt_close($stmt);
                    }
                }
            } else {
                $message = "Failed to prepare check for recommendations: " . mysqli_error($conn);
                $message_type = "error";
            }
        } else {
            $message = "Career ID not provided for deletion.";
            $message_type = "error";
        }
    }
}

// Fetch all careers for display
$careers = [];
$sql_fetch_careers = "SELECT id, title, description FROM careers ORDER BY title ASC";
$result_careers = mysqli_query($conn, $sql_fetch_careers);
if ($result_careers) {
    while ($row = mysqli_fetch_assoc($result_careers)) {
        $careers[] = $row;
    }
} else {
    $message = "Error fetching careers: " . mysqli_error($conn);
    $message_type = "error";
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Careers - Admin Panel</title>
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
        .form-group input[type="text"],
        .form-group textarea {
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

        .career-list { margin-top: 30px; }
        .career-item {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #eee;
            display: flex;
            flex-direction: column;
        }
        .career-item h4 { margin: 0 0 10px 0; color: #007bff; }
        .career-item p { margin: 0 0 10px 0; color: #555; font-size: 0.95em;}
        .career-actions { text-align: right; }
        .career-actions button { margin-left: 10px; }

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
        <h1>Manage Careers</h1>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h3>Add New Career</h3>
            <form action="manage_careers.php" method="POST">
                <div class="form-group">
                    <label for="add_title">Career Title:</label>
                    <input type="text" id="add_title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="add_description">Description:</label>
                    <textarea id="add_description" name="description" rows="4" required></textarea>
                </div>
                <button type="submit" name="add_career" class="btn-submit">Add Career</button>
            </form>
        </div>

        <div class="career-list">
            <h3>Existing Careers</h3>
            <?php if (!empty($careers)): ?>
                <?php foreach ($careers as $career): ?>
                    <div class="career-item" id="career-<?php echo $career['id']; ?>">
                        <h4><?php echo htmlspecialchars($career['title']); ?> (ID: <?php echo $career['id']; ?>)</h4>
                        <p><?php echo nl2br(htmlspecialchars($career['description'])); ?></p>
                        <div class="career-actions">
                            <button class="btn-action btn-update" onclick="toggleEditForm(<?php echo $career['id']; ?>, '<?php echo htmlspecialchars(addslashes($career['title'])); ?>', '<?php echo htmlspecialchars(addslashes($career['description'])); ?>')">Edit</button>
                            <form action="manage_careers.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this career? This cannot be undone if linked to user recommendations.');">
                                <input type="hidden" name="id" value="<?php echo $career['id']; ?>">
                                <button type="submit" name="delete_career" class="btn-action btn-delete">Delete</button>
                            </form>
                        </div>

                        <!-- Edit Form (hidden by default) -->
                        <div class="edit-form-container" id="edit-form-<?php echo $career['id']; ?>">
                            <h4>Edit Career</h4>
                            <form action="manage_careers.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $career['id']; ?>">
                                <div class="form-group">
                                    <label for="edit_title_<?php echo $career['id']; ?>">Title:</label>
                                    <input type="text" id="edit_title_<?php echo $career['id']; ?>" name="title" value="<?php echo htmlspecialchars($career['title']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_description_<?php echo $career['id']; ?>">Description:</label>
                                    <textarea id="edit_description_<?php echo $career['id']; ?>" name="description" rows="4" required><?php echo htmlspecialchars($career['description']); ?></textarea>
                                </div>
                                <button type="submit" name="update_career" class="btn-submit btn-update">Update Career</button>
                                <button type="button" class="btn-action btn-delete" onclick="toggleEditForm(<?php echo $career['id']; ?>)">Cancel</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No careers found. Add one above!</p>
            <?php endif; ?>
        </div>

        <div class="back-link" style="text-align: center; margin-top: 30px;">
            <p><a href="dashboard.php" class="btn-action">Back to Admin Dashboard</a></p>
        </div>
    </div>

    <script>
        function toggleEditForm(careerId, title = '', description = '') {
            const form = document.getElementById('edit-form-' + careerId);
            if (form.style.display === 'block') {
                form.style.display = 'none';
            } else {
                // Hide all other edit forms
                document.querySelectorAll('.edit-form-container').forEach(otherForm => {
                    if (otherForm.id !== 'edit-form-' + careerId) {
                        otherForm.style.display = 'none';
                    }
                });

                // Populate and show the clicked form
                document.getElementById('edit_title_' + careerId).value = title;
                document.getElementById('edit_description_' + careerId).value = description;
                form.style.display = 'block';
            }
        }
    </script>
</body>
</html>

