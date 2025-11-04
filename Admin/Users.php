<?php
// Admin Users Page - Access restricted to admin users only
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

// Include database connection
include '../Database/db_connect.php';

// Get admin information
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];
$admin_email = $_SESSION['admin_email'];

// Get all users from database (excluding admins)
$users = [];
$sql = "SELECT id, first_name, last_name, email, contact_number, gender, created_at FROM users WHERE role != 'admin' ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Users - Admin Panel</title>
    <link rel="stylesheet" href="css/Admin.css" />
    <link rel="stylesheet" href="css/sidebar.css" />
    <link rel="stylesheet" href="css/users.css" />
    <script src="js/sidebar.js"></script>
    <script src="js/users.js"></script>
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
                    <h1>Manage Users</h1>
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
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>

            <!-- Users Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>All Users</h2>
                    <button type="button" class="action-btn edit-btn" id="add-user-btn">Add New User</button>
                </div>

                <!-- Add User Form (Hidden by default) -->
                <div id="add-user-form" class="add-user-form" style="display: none;">
                    <h3>Add New User</h3>
                    <form method="POST" action="process_add_user.php">
                        <div class="form-group">
                            <label for="first_name">First Name:</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name:</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_number">Contact Number:</label>
                            <input type="tel" id="contact_number" name="contact_number" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender:</label>
                            <select id="gender" name="gender" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Add User</button>
                            <button type="button" class="btn btn-danger" id="cancel-add-btn">Cancel</button>
                        </div>
                    </form>
                </div>

                <?php if (!empty($users)): ?>
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Gender</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['contact_number']); ?></td>
                                    <td><?php echo htmlspecialchars($user['gender']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="action-btn edit-btn">Edit</a>
                                        <a href="#" class="action-btn delete-btn" onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-users">
                        <i>👥</i>
                        <h3>No Users Found</h3>
                        <p>There are no users in the system yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
    // Toggle add user form
    document.getElementById('add-user-btn').addEventListener('click', function() {
        const form = document.getElementById('add-user-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    });

    document.getElementById('cancel-add-btn').addEventListener('click', function() {
        document.getElementById('add-user-form').style.display = 'none';
    });

    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            window.location.href = 'process_delete_user.php?id=' + userId;
        }
    }
    </script>
</body>
</html>
