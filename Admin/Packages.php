<?php
// Admin Tour Packages Page - Access restricted to admin users only
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

// Get all tour packages from database
$packages = [];
$sql = "SELECT * FROM tour_packages ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tour Packages - Admin Panel</title>
    <link rel="stylesheet" href="css/Admin.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/tours.css">
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
                    <h1>Manage Tour Packages</h1>
                    <p>Admin Panel - <?php echo date('F j, Y'); ?></p>
                    <?php
                    if (isset($_SESSION['package_errors'])) {
                        echo '<div class="error-message">';
                        foreach ($_SESSION['package_errors'] as $error) {
                            echo '<p>' . htmlspecialchars($error) . '</p>';
                        }
                        echo '</div>';
                        unset($_SESSION['package_errors']);
                    }
                    if (isset($_SESSION['package_success'])) {
                        echo '<div class="success-message" style="color: green; margin-top: 10px;">' . htmlspecialchars($_SESSION['package_success']) . '</div>';
                        unset($_SESSION['package_success']);
                    }
                    ?>
                </div>
                <a href="Tours.php" class="logout-btn">Back to Tours</a>
            </div>

            <!-- Packages Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>All Tour Packages</h2>
                    <button type="button" class="add-btn" id="add-package-btn">Add New Package</button>
                </div>

                <!-- Add Package Form (Initially Hidden) -->
                <div id="add-package-form" class="add-tour-form" style="display: none;">
                    <h3>Add New Tour Package</h3>
                    <form id="package-form" action="process_add_package.php" method="POST">
                        <div class="form-group">
                            <label for="package-name">Package Name:</label>
                            <input type="text" id="package-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="package-description">Description:</label>
                            <textarea id="package-description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="package-price">Price (₹):</label>
                            <input type="number" id="package-price" name="price" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="package-duration">Duration (days):</label>
                            <input type="number" id="package-duration" name="duration" min="1" required>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Save Package</button>
                            <button type="button" class="btn btn-danger" id="cancel-package-btn">Cancel</button>
                        </div>
                    </form>
                </div>

                <?php if (!empty($packages)): ?>
                    <table class="tours-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($packages as $package): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($package['id']); ?></td>
                                    <td><?php echo htmlspecialchars($package['name']); ?></td>
                                    <td><?php echo htmlspecialchars($package['description'] ?? 'No description'); ?></td>
                                    <td>₹<?php echo number_format($package['price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($package['duration']); ?> days</td>
                                    <td>
                                        <a href="edit_package.php?id=<?php echo $package['id']; ?>" class="action-btn edit-btn">Edit</a>
                                        <a href="process_delete_package.php?id=<?php echo $package['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this package?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-tours">
                        <i>📦</i>
                        <h3>No Packages Found</h3>
                        <p>There are no tour packages available in the system.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // Toggle add package form
        document.getElementById('add-package-btn').addEventListener('click', function() {
            const form = document.getElementById('add-package-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });

        // Cancel button
        document.getElementById('cancel-package-btn').addEventListener('click', function() {
            document.getElementById('add-package-form').style.display = 'none';
        });
    </script>
</body>
</html>
