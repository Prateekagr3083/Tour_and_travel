
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tours - Admin Panel</title>
    <link rel="stylesheet" href="css/Admin.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/tours.css">
    <script src="js/sidebar.js"></script>
    <script src="js/tours.js"></script>
    <script src="js/add_tour.js"></script>
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
                    <h1>Manage Tours</h1>
                    <p>Admin Panel - <?php echo date('F j, Y'); ?></p>
                    <?php
                    if (isset($_SESSION['add_tour_errors'])) {
                        echo '<div class="error-message">';
                        foreach ($_SESSION['add_tour_errors'] as $error) {
                            echo '<p>' . htmlspecialchars($error) . '</p>';
                        }
                        echo '</div>';
                        unset($_SESSION['add_tour_errors']);
                    }
                    if (isset($_SESSION['add_tour_success'])) {
                        echo '<div class="success-message" style="color: green; margin-top: 10px;">' . htmlspecialchars($_SESSION['add_tour_success']) . '</div>';
                        unset($_SESSION['add_tour_success']);
                    }
                    ?>
                </div>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>

            <!-- Tours Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>All Tours</h2>
                <button type="button" class="add-btn" id="add-tour-btn">Add New Tour</button>
            </div>

            <?php include 'add_tour_form.php'; ?>

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
                                    <td>₹<?php echo number_format($tour['price'], 2); ?></td>
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
                                        <a href="edit_tour.php?id=<?php echo $tour['id']; ?>" class="action-btn edit-btn">Edit</a>
                                        <a href="#" class="action-btn delete-btn" onclick="deleteTour(<?php echo $tour['id']; ?>)">Delete</a>
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

    <script>
    function deleteTour(tourId) {
        if (confirm('Are you sure you want to delete this tour? This action cannot be undone.')) {
            window.location.href = 'process_delete_tour.php?id=' + tourId;
        }
    }
    </script>
</body>
</html>
