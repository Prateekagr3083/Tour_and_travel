<?php
// Admin Bookings Page - Access restricted to admin users only
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

// Get all bookings from database with user and tour information
$bookings = [];
$sql = "SELECT b.id, b.booking_date, b.status, b.number_of_guests, b.total_price,
        u.first_name, u.last_name, u.email as user_email,
        t.title as tour_name, d.name as destination_name
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN tours t ON b.tour_id = t.id
        LEFT JOIN destinations d ON t.destination_id = d.id
        ORDER BY b.booking_date DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Bookings - Admin Panel</title>
    <link rel="stylesheet" href="css/Admin.css" />
    <link rel="stylesheet" href="css/sidebar.css" />
    <link rel="stylesheet" href="css/bookings.css" />
    <script src="js/sidebar.js"></script>
    <script src="js/bookings.js"></script>
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
                    <h1>Manage Bookings</h1>
                    <p>Admin Panel - <?php echo date('F j, Y'); ?></p>
                    <?php
                    if (isset($_SESSION['booking_success'])) {
                        echo '<div class="success-message" style="color: green; margin-top: 10px;">' . htmlspecialchars($_SESSION['booking_success']) . '</div>';
                        unset($_SESSION['booking_success']);
                    }
                    if (isset($_SESSION['booking_error'])) {
                        echo '<div class="error-message" style="color: red; margin-top: 10px;">' . htmlspecialchars($_SESSION['booking_error']) . '</div>';
                        unset($_SESSION['booking_error']);
                    }
                    ?>
                </div>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form class="filter-form">
                    <div class="filter-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="">All Status</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="date">Booking Date</label>
                        <input type="date" id="date" name="date" />
                    </div>
                    <button type="submit" class="filter-btn">Apply Filters</button>
                </form>
            </div>

            <!-- Bookings Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>All Bookings</h2>
                </div>

                <?php if (!empty($bookings)): ?>
                    <table class="bookings-table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>User</th>
                                <th>Tour</th>
                                <th>Destination</th>
                                <th>Guests</th>
                                <th>Total Price</th>
                                <th>Booking Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                    <td>
                                        <div><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></div>
                                        <small><?php echo htmlspecialchars($booking['user_email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($booking['tour_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['destination_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['number_of_guests']); ?></td>
                                    <td>₹<?php echo number_format($booking['total_price'], 2); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></td>
                                    <td>
                                        <span class="status-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view_booking.php?id=<?php echo $booking['id']; ?>" class="action-btn view-btn">View</a>
                                        <?php if ($booking['status'] === 'pending'): ?>
                                            <a href="#" class="action-btn confirm-btn" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'confirmed')">Confirm</a>
                                            <a href="#" class="action-btn cancel-btn" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'cancelled')">Cancel</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-bookings">
                        <i>📋</i>
                        <h3>No Bookings Found</h3>
                        <p>There are no bookings in the system yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
    function updateBookingStatus(bookingId, status) {
        if (confirm('Are you sure you want to ' + status + ' this booking?')) {
            window.location.href = 'process_booking_status.php?id=' + bookingId + '&status=' + status;
        }
    }
    </script>
</body>
</html>
