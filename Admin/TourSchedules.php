<?php
// Admin Tour Schedules Page - Access restricted to admin users only
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

// Get all tour schedules with tour names
$schedules = [];
$sql = "SELECT ts.id, ts.tour_id, t.title AS tour_title, ts.start_date, ts.end_date, ts.availability
        FROM tour_schedules ts
        JOIN tours t ON ts.tour_id = t.id
        ORDER BY ts.start_date DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }
}

// Get tours for dropdown
$tours = [];
$sql_tours = "SELECT id, title FROM tours ORDER BY title";
$result_tours = $conn->query($sql_tours);
if ($result_tours && $result_tours->num_rows > 0) {
    while ($row = $result_tours->fetch_assoc()) {
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
    <title>Manage Tour Schedules - Admin Panel</title>
    <link rel="stylesheet" href="css/Admin.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/tours.css">
    <script src="js/sidebar.js"></script>
    <script src="js/tours.js"></script>
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
                    <h1>Manage Tour Schedules</h1>
                    <p>Admin Panel - <?php echo date('F j, Y'); ?></p>
                </div>
                <button id="add-schedule-btn" class="btn btn-primary">Add New Schedule</button>
            </div>

            <!-- Add Schedule Form (Initially Hidden) -->
            <div id="add-schedule-form" class="add-tour-form" style="display: none;">
                <h3>Add New Tour Schedule</h3>
                <form id="schedule-form" action="process_add_schedule.php" method="POST">
                    <div class="form-group">
                        <label for="tour-select">Tour:</label>
                        <select id="tour-select" name="tour_id" required>
                            <option value="">Select a Tour</option>
                            <?php foreach ($tours as $tour): ?>
                                <option value="<?php echo htmlspecialchars($tour['id']); ?>">
                                    <?php echo htmlspecialchars($tour['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start-date">Start Date:</label>
                        <input type="date" id="start-date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="end-date">End Date:</label>
                        <input type="date" id="end-date" name="end_date" required>
                    </div>
                    <div class="form-group">
                        <label for="availability">Availability:</label>
                        <input type="number" id="availability" name="availability" min="0" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Schedule</button>
                        <button type="button" class="btn btn-danger" id="cancel-schedule-btn">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- Schedules Table -->
            <div class="dashboard-section">
                <?php
                if (isset($_SESSION['add_schedule_errors'])) {
                    echo '<div class="error-message">';
                    foreach ($_SESSION['add_schedule_errors'] as $error) {
                        echo '<p>' . htmlspecialchars($error) . '</p>';
                    }
                    echo '</div>';
                    unset($_SESSION['add_schedule_errors']);
                }
                if (isset($_SESSION['add_schedule_success'])) {
                    echo '<div class="success-message" style="color: green; margin-bottom: 20px;">';
                    echo htmlspecialchars($_SESSION['add_schedule_success']);
                    echo '</div>';
                    unset($_SESSION['add_schedule_success']);
                }
                ?>

                <?php if (!empty($schedules)): ?>
                    <table class="tours-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tour Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Availability</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($schedule['id']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['tour_title']); ?></td>
                                    <td><?php echo htmlspecialchars(date('M j, Y', strtotime($schedule['start_date']))); ?></td>
                                    <td><?php echo htmlspecialchars(date('M j, Y', strtotime($schedule['end_date']))); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['availability']); ?></td>
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
                        <i>📅</i>
                        <h3>No Tour Schedules Found</h3>
                        <p>There are no tour schedules available in the system.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // Toggle add schedule form
        document.getElementById('add-schedule-btn').addEventListener('click', function() {
            const form = document.getElementById('add-schedule-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });

        // Cancel button
        document.getElementById('cancel-schedule-btn').addEventListener('click', function() {
            document.getElementById('add-schedule-form').style.display = 'none';
        });

        // Form validation
        document.getElementById('schedule-form').addEventListener('submit', function(e) {
            const startDate = new Date(document.getElementById('start-date').value);
            const endDate = new Date(document.getElementById('end-date').value);

            if (startDate >= endDate) {
                alert('End date must be after start date.');
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>
