<?php
// Edit User Page - Access restricted to admin users only
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

// Get user ID from URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id <= 0) {
    header("Location: Users.php");
    exit();
}

// Include database connection
include '../Database/db_connect.php';

// Get user data
$sql = "SELECT first_name, last_name, email, contact_number, gender FROM users WHERE id = ? AND role != 'admin'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: Users.php");
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit User - Admin Panel</title>
    <link rel="stylesheet" href="css/Admin.css" />
    <link rel="stylesheet" href="css/sidebar.css" />
    <link rel="stylesheet" href="css/users.css" />
    <script src="js/sidebar.js"></script>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="welcome-message">
                    <h1>Edit User</h1>
                    <p>Admin Panel - <?php echo date('F j, Y'); ?></p>
                    <?php
                    if (isset($_SESSION['user_success'])) {
                        echo '<div class="success-message" style="color: green; margin-top: 10px;">' . htmlspecialchars($_SESSION['user_success']) . '</div>';
                        unset($_SESSION['user_success']);
                    }
                    if (isset($_SESSION['user_error'])) {
                        echo '<div class="error-message" style="color: red; margin-top: 10px;">' . htmlspecialchars($_SESSION['user_error']) . '</div>';
                        unset($_SESSION['user_error']);
                    }
                    ?>
                </div>
                <a href="Users.php" class="logout-btn">Back to Users</a>
            </div>

            <!-- Edit User Form -->
            <div class="content-section">
                <div class="edit-user-form">
                    <h3>Edit User Details</h3>
                    <form method="POST" action="process_edit_user.php">
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

                        <div class="form-group">
                            <label for="first_name">First Name:</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name:</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_number">Contact Number:</label>
                            <input type="tel" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender:</label>
                            <select id="gender" name="gender" required>
                                <option value="Male" <?php echo $user['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo $user['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo $user['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password (leave blank to keep current):</label>
                            <input type="password" id="new_password" name="new_password">
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Update User</button>
                            <a href="Users.php" class="btn btn-danger">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
