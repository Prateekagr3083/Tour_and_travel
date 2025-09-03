<?php
// Admin Dashboard - Access restricted to admin users only
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

// Get statistics for dashboard
$users_count = 0;
$bookings_count = 0;
$tours_count = 0;

// Count total users
$sql_users = "SELECT COUNT(*) as total_users FROM users WHERE role != 'admin'";
$result_users = $conn->query($sql_users);
if ($result_users) {
    $users_count = $result_users->fetch_assoc()['total_users'];
}

// Count total bookings (assuming bookings table exists)
$sql_bookings = "SELECT COUNT(*) as total_bookings FROM bookings";
$result_bookings = $conn->query($sql_bookings);
if ($result_bookings) {
    $bookings_count = $result_bookings->fetch_assoc()['total_bookings'];
}

// Count total tours (assuming tours table exists)
$sql_tours = "SELECT COUNT(*) as total_tours FROM tours";
$result_tours = $conn->query($sql_tours);
if ($result_tours) {
    $tours_count = $result_tours->fetch_assoc()['total_tours'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tour & Travel</title>
    <link rel="stylesheet" href="css/Admin.css">
    <link rel="stylesheet" href="css/sidebar.css">
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
                    <h1>Welcome, <?php echo htmlspecialchars($admin_name); ?>!</h1>
                    <p>Admin Dashboard - <?php echo date('F j, Y'); ?></p>
                </div>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $users_count; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $bookings_count; ?></div>
                    <div class="stat-label">Total Bookings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $tours_count; ?></div>
                    <div class="stat-label">Total Tours</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Revenue</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2>Quick Actions</h2>
                </div>
                <div class="quick-actions">
                    <a href="Tours.php" class="action-btn">Add New Tour</a>
                    <a href="Bookings.php" class="action-btn">View Bookings</a>
                    <a href="Users.php" class="action-btn">Manage Users</a>
                    <a href="Payments.php" class="action-btn">Payments</a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2>Recent Activity</h2>
                </div>
                <div class="coming-soon">
                    <i>📈</i>
                    <h3>Activity Tracking</h3>
                    <p>This feature will be available soon</p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
