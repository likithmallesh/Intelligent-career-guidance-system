<?php
// admin/manage_users.php
session_start();
require_once '../config/config.php'; // Include your database configuration file

// Check if user is logged in and is an administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../admin_login.php'); // Redirect to admin login if not authorized
    exit();
}

$message = '';
$message_type = ''; // 'success' or 'error'

// Handle user deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id_to_delete = $_POST['user_id'];

    if (!empty($user_id_to_delete)) {
        // Prevent admin from deleting themselves
        if ($user_id_to_delete == $_SESSION['user_id']) {
            $message = "You cannot delete your own admin account.";
            $message_type = "error";
        } else {
            // Start a transaction: Disable autocommit
            mysqli_autocommit($conn, FALSE);
            $success = true;
            $error_detail = ''; // To store specific error messages

            // 1. Delete from user_skills table
            $sql_delete_user_skills = "DELETE FROM user_skills WHERE user_id = ?";
            if ($stmt = mysqli_prepare($conn, $sql_delete_user_skills)) {
                mysqli_stmt_bind_param($stmt, "i", $user_id_to_delete);
                if (!mysqli_stmt_execute($stmt)) {
                    $success = false;
                    $error_detail = "Failed to delete user skills: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $success = false;
                $error_detail = "Failed to prepare user_skills delete statement: " . mysqli_error($conn);
            }

            // 2. Delete from user_answers table (only if previous step was successful)
            if ($success) {
                $sql_delete_user_answers = "DELETE FROM user_answers WHERE user_id = ?";
                if ($stmt = mysqli_prepare($conn, $sql_delete_user_answers)) {
                    mysqli_stmt_bind_param($stmt, "i", $user_id_to_delete);
                    if (!mysqli_stmt_execute($stmt)) {
                        $success = false;
                        $error_detail = "Failed to delete user answers: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $success = false;
                    $error_detail = "Failed to prepare user_answers delete statement: " . mysqli_error($conn);
                }
            }

            // 3. Delete from recommendations table (only if previous steps were successful)
            if ($success) {
                $sql_delete_recommendations = "DELETE FROM recommendations WHERE user_id = ?";
                if ($stmt = mysqli_prepare($conn, $sql_delete_recommendations)) {
                    mysqli_stmt_bind_param($stmt, "i", $user_id_to_delete);
                    if (!mysqli_stmt_execute($stmt)) {
                        $success = false;
                        $error_detail = "Failed to delete recommendations: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $success = false;
                    $error_detail = "Failed to prepare recommendations delete statement: " . mysqli_error($conn);
                }
            }

            // 4. Finally, delete from users table (only if all previous steps were successful)
            if ($success) {
                $sql_delete_user = "DELETE FROM users WHERE id = ?";
                if ($stmt = mysqli_prepare($conn, $sql_delete_user)) {
                    mysqli_stmt_bind_param($stmt, "i", $user_id_to_delete);
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_commit($conn); // Commit the transaction if all operations succeed
                        $message = "User and associated data deleted successfully!";
                        $message_type = "success";
                    } else {
                        mysqli_rollback($conn); // Rollback on user delete failure
                        $message = "Error deleting user: " . mysqli_stmt_error($stmt);
                        $message_type = "error";
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    mysqli_rollback($conn); // Rollback if user delete statement fails to prepare
                    $message = "Error preparing user delete statement: " . mysqli_error($conn);
                    $message_type = "error";
                }
            } else {
                mysqli_rollback($conn); // Rollback if any of the related data deletion steps failed
                $message = "Error deleting user's associated data. User not deleted. Detail: " . $error_detail;
                $message_type = "error";
            }

            // Re-enable autocommit
            mysqli_autocommit($conn, TRUE);
        }
    } else {
        $message = "User ID not provided for deletion.";
        $message_type = "error";
    }
}

// Fetch all users for display
$users = [];
$sql_fetch_users = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
$result_users = mysqli_query($conn, $sql_fetch_users);
if ($result_users) {
    while ($row = mysqli_fetch_assoc($result_users)) {
        $users[] = $row;
    }
} else {
    $message = "Error fetching users: " . mysqli_error($conn);
    $message_type = "error";
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-container { max-width: 1000px; margin: 50px auto; padding: 30px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .admin-container h1 { color: #dc3545; margin-bottom: 20px; text-align: center; }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .user-list-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .user-list-table th, .user-list-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .user-list-table th {
            background-color: #f2f2f2;
            color: #555;
            font-weight: bold;
        }
        .user-list-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .user-list-table .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center; /* Center buttons within cell */
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        .current-admin-row {
            background-color: #fff3cd !important; /* Highlight current admin */
            color: #856404;
        }
        .current-admin-row .btn-delete {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Manage Users</h1>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (!empty($users)): ?>
            <table class="user-list-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr <?php echo ($user['id'] == $_SESSION['user_id'] && $user['role'] == 'admin') ? 'class="current-admin-row"' : ''; ?>>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($user['created_at']))); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($user['id'] != $_SESSION['user_id']): // Prevent admin from deleting themselves ?>
                                        <form action="manage_users.php" method="POST" onsubmit="return confirm('WARNING: Are you sure you want to delete user <?php echo htmlspecialchars(addslashes($user['name'])); ?>? This will delete ALL associated data (skills, answers, recommendations) and cannot be undone!');">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="delete_user" class="btn-delete">Delete</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn-delete" disabled title="You cannot delete your own account">Delete</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No users registered yet.</p>
        <?php endif; ?>

        <div class="back-link" style="text-align: center; margin-top: 30px;">
            <p><a href="dashboard.php" class="btn-action">Back to Admin Dashboard</a></p>
        </div>
    </div>
</body>
</html>
<?php
// admin/manage_users.php
session_start();
require_once '../config/config.php'; // Include your database configuration file

// Check if user is logged in and is an administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit(); // Always exit after a header redirect
}

$message = '';
$message_type = ''; // 'success' or 'error'

// Handle user deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id_to_delete = $_POST['user_id'];

    if (!empty($user_id_to_delete)) {
        // Prevent admin from deleting themselves
        if ($user_id_to_delete == $_SESSION['user_id']) {
            $message = "You cannot delete your own admin account.";
            $message_type = "error";
        } else {
            // Start a transaction: Disable autocommit
            // This ensures that if any delete operation fails, all previous ones are rolled back.
            mysqli_autocommit($conn, FALSE);
            $success = true;
            $error_detail = ''; // To store specific error messages

            // 1. Delete from user_skills table
            $sql_delete_user_skills = "DELETE FROM user_skills WHERE user_id = ?";
            if ($stmt = mysqli_prepare($conn, $sql_delete_user_skills)) {
                mysqli_stmt_bind_param($stmt, "i", $user_id_to_delete);
                if (!mysqli_stmt_execute($stmt)) {
                    $success = false;
                    $error_detail = "Failed to delete user skills: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $success = false;
                $error_detail = "Failed to prepare user_skills delete statement: " . mysqli_error($conn);
            }

            // 2. Delete from user_answers table (only if previous step was successful)
            if ($success) {
                $sql_delete_user_answers = "DELETE FROM user_answers WHERE user_id = ?";
                if ($stmt = mysqli_prepare($conn, $sql_delete_user_answers)) {
                    mysqli_stmt_bind_param($stmt, "i", $user_id_to_delete);
                    if (!mysqli_stmt_execute($stmt)) {
                        $success = false;
                        $error_detail = "Failed to delete user answers: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $success = false;
                    $error_detail = "Failed to prepare user_answers delete statement: " . mysqli_error($conn);
                }
            }

            // 3. Delete from recommendations table (only if previous steps were successful)
            if ($success) {
                $sql_delete_recommendations = "DELETE FROM recommendations WHERE user_id = ?";
                if ($stmt = mysqli_prepare($conn, $sql_delete_recommendations)) {
                    mysqli_stmt_bind_param($stmt, "i", $user_id_to_delete);
                    if (!mysqli_stmt_execute($stmt)) {
                        $success = false;
                        $error_detail = "Failed to delete recommendations: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $success = false;
                    $error_detail = "Failed to prepare recommendations delete statement: " . mysqli_error($conn);
                }
            }

            // 4. Finally, delete from users table (only if all previous steps were successful)
            if ($success) {
                $sql_delete_user = "DELETE FROM users WHERE id = ?";
                if ($stmt = mysqli_prepare($conn, $sql_delete_user)) {
                    mysqli_stmt_bind_param($stmt, "i", $user_id_to_delete);
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_commit($conn); // Commit the transaction if all operations succeed
                        $message = "User and associated data deleted successfully!";
                        $message_type = "success";
                    } else {
                        mysqli_rollback($conn); // Rollback on user delete failure
                        $message = "Error deleting user: " . mysqli_stmt_error($stmt);
                        $message_type = "error";
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    mysqli_rollback($conn); // Rollback if user delete statement fails to prepare
                    $message = "Error preparing user delete statement: " . mysqli_error($conn);
                    $message_type = "error";
                }
            } else {
                mysqli_rollback($conn); // Rollback if any of the related data deletion steps failed
                $message = "Error deleting user's associated data. User not deleted. Detail: " . $error_detail;
                $message_type = "error";
            }

            // Re-enable autocommit
            mysqli_autocommit($conn, TRUE);
        }
    } else {
        $message = "User ID not provided for deletion.";
        $message_type = "error";
    }
}

// Fetch all users for display
$users = [];
$sql_fetch_users = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
$result_users = mysqli_query($conn, $sql_fetch_users);
if ($result_users) {
    while ($row = mysqli_fetch_assoc($result_users)) {
        $users[] = $row;
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-container { max-width: 1000px; margin: 50px auto; padding: 30px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .admin-container h1 { color: #dc3545; margin-bottom: 20px; text-align: center; }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .user-list-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .user-list-table th, .user-list-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .user-list-table th {
            background-color: #f2f2f2;
            color: #555;
            font-weight: bold;
        }
        .user-list-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .user-list-table .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center; /* Center buttons within cell */
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        .current-admin-row {
            background-color: #fff3cd !important; /* Highlight current admin */
            color: #856404;
        }
        .current-admin-row .btn-delete {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Manage Users</h1>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (!empty($users)): ?>
            <table class="user-list-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr <?php echo ($user['id'] == $_SESSION['user_id'] && $user['role'] == 'admin') ? 'class="current-admin-row"' : ''; ?>>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($user['created_at']))); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($user['id'] != $_SESSION['user_id']): // Prevent admin from deleting themselves ?>
                                        <form action="manage_users.php" method="POST" onsubmit="return confirm('WARNING: Are you sure you want to delete user <?php echo htmlspecialchars(addslashes($user['name'])); ?>? This will delete ALL associated data (skills, answers, recommendations) and cannot be undone!');">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="delete_user" class="btn-delete">Delete</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn-delete" disabled title="You cannot delete your own account">Delete</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No users registered yet.</p>
        <?php endif; ?>

        <div class="back-link" style="text-align: center; margin-top: 30px;">
            <p><a href="dashboard.php" class="btn-action">Back to Admin Dashboard</a></p>
        </div>
    </div>
</body>
</html>

