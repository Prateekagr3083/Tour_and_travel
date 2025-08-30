<?php
// Admin Tours Page - Access restricted to admin users only
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

// Get all tours from database with destination and package names
$tours = [];
$sql = "SELECT t.id, t.title, d.name AS destination_name, t.price, t.duration, p.name AS package_name
        FROM tours t
        LEFT JOIN destinations d ON t.destination_id = d.id
        LEFT JOIN tour_packages p ON t.package_id = p.id
        ORDER BY t.id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tours[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tours - Admin Panel</title>
    <link rel="stylesheet" href="css/Admin.css">
    <link rel="stylesheet" href="css/tours.css">
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
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i>👥</i> Users
                    </a>
                </li>
                <li class="nav-item active">
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
                    <h1>Manage Tours</h1>
                    <p>Admin Panel - <?php echo date('F j, Y'); ?></p>
                </div>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>

            <!-- Tours Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>All Tours</h2>
                    <a href="#" class="add-btn">Add New Tour</a>
                </div>

                <?php if (!empty($tours)): ?>
                    <table class="tours-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tour Name</th>
                                <th>Destination</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tours as $tour): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($tour['id']); ?></td>
                                    <td><?php echo htmlspecialchars($tour['title']); ?></td>
                                    <td><?php echo htmlspecialchars($tour['destination_name']); ?></td>
                                    <td>$<?php echo number_format($tour['price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($tour['duration']); ?> days</td>
                                    <td>
                                        <span>
                                            <?php echo htmlspecialchars($tour['package_name']); ?>
                                        </span>
                                    </td>
                                    <td> <!-- No created_at column in tours table -->
                                        N/A
                                    </td>
                                    <td>
                                        <a href="#" class="action-btn edit-btn">Edit</a>
                                        <a href="#" class="action-btn delete-btn">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-tours">
                        <i>🏨</i>
                        <h3>No Tours Found</h3>
                        <p>There are no tours available in the system.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
