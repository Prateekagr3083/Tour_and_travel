<?php
// Admin Destinations Page - Access restricted to admin users only
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

// Get all destinations from database
$destinations = [];
$sql = "SELECT * FROM destinations ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $destinations[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Destinations - Admin Panel</title>
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
                    <h1>Manage Destinations</h1>
                    <p>Admin Panel - <?php echo date('F j, Y'); ?></p>
                    <?php
                    if (isset($_SESSION['destination_errors'])) {
                        echo '<div class="error-message">';
                        foreach ($_SESSION['destination_errors'] as $error) {
                            echo '<p>' . htmlspecialchars($error) . '</p>';
                        }
                        echo '</div>';
                        unset($_SESSION['destination_errors']);
                    }
                    if (isset($_SESSION['destination_success'])) {
                        echo '<div class="success-message" style="color: green; margin-top: 10px;">' . htmlspecialchars($_SESSION['destination_success']) . '</div>';
                        unset($_SESSION['destination_success']);
                    }
                    ?>
                </div>
                <a href="Tours.php" class="logout-btn">Back to Tours</a>
            </div>

            <!-- Destinations Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>All Destinations</h2>
                    <button type="button" class="add-btn" id="add-destination-btn">Add New Destination</button>
                </div>

                <!-- Add Destination Form (Initially Hidden) -->
                <div id="add-destination-form" class="add-tour-form" style="display: none;">
                    <h3>Add New Destination</h3>
                    <form id="destination-form" action="process_add_destination.php" method="POST">
                        <div class="form-group">
                            <label for="destination-name">Destination Name:</label>
                            <input type="text" id="destination-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="destination-description">Description:</label>
                            <textarea id="destination-description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="destination-country">Country:</label>
                            <input type="text" id="destination-country" name="country" required>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Save Destination</button>
                            <button type="button" class="btn btn-danger" id="cancel-destination-btn">Cancel</button>
                        </div>
                    </form>
                </div>

                <?php if (!empty($destinations)): ?>
                    <table class="tours-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Country</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($destinations as $destination): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($destination['id']); ?></td>
                                    <td><?php echo htmlspecialchars($destination['name']); ?></td>
                                    <td><?php echo htmlspecialchars($destination['description'] ?? 'No description'); ?></td>
                                    <td><?php echo htmlspecialchars($destination['country']); ?></td>
                                    <td>
                                        <a href="edit_destination.php?id=<?php echo $destination['id']; ?>" class="action-btn edit-btn">Edit</a>
                                        <a href="process_delete_destination.php?id=<?php echo $destination['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this destination?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-tours">
                        <i>🌍</i>
                        <h3>No Destinations Found</h3>
                        <p>There are no destinations available in the system.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // Toggle add destination form
        document.getElementById('add-destination-btn').addEventListener('click', function() {
            const form = document.getElementById('add-destination-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });

        // Cancel button
        document.getElementById('cancel-destination-btn').addEventListener('click', function() {
            document.getElementById('add-destination-form').style.display = 'none';
        });
    </script>
</body>
</html>
