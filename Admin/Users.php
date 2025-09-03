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

// Get all users from database
$users = [];
$sql = "SELECT id, email FROM users ORDER BY id DESC";
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
    <link rel="stylesheet" href="css/users.css" />
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p>Tour & Travel Management</p>
            </div>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="Dashboard.php" class="nav-link">
                        <i>📊</i> Dashboard
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="Users.php" class="nav-link">
                        <i>👥</i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a href="Tours.php" class="nav-link">
                        <i>🏨</i> Tours
                    </a>
                </li>
                <li class="nav-item">
                    <a href="Bookings.php" class="nav-link">
                        <i>📋</i> Bookings
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i>⚙️</i> Settings
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="welcome-message">
                    <h1>Manage Users</h1>
                    <p>Admin Panel - <?php echo date('F j, Y'); ?></p>
                </div>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>

            <!-- Users Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>All Users</h2>
                    <a href="#" class="action-btn edit-btn">Add New User</a>
                </div>

                <?php if (!empty($users)): ?>
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <a href="#" class="action-btn edit-btn">Edit</a>
                                        <a href="#" class="action-btn delete-btn">Delete</a>
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
</body>
</html>
