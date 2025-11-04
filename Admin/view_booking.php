<?php
// View Booking Details Page - Access restricted to admin users only
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

// Get booking ID from URL
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($booking_id <= 0) {
    header("Location: Bookings.php");
    exit();
}

// Include database connection
include '../Database/db_connect.php';

// Get booking details with user and tour information
$sql = "SELECT b.id, b.booking_date, b.status, b.number_of_guests, b.total_price,
        u.first_name, u.last_name, u.email, u.contact_number,
        t.title as tour_name, t.price as tour_price, t.duration, t.description,
        d.name as destination_name, p.name as package_name
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN tours t ON b.tour_id = t.id
        LEFT JOIN destinations d ON t.destination_id = d.id
        LEFT JOIN tour_packages p ON t.package_id = p.id
        WHERE b.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: Bookings.php");
    exit();
}

$booking = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View Booking - Admin Panel</title>
    <link rel="stylesheet" href="css/Admin.css" />
    <link rel="stylesheet" href="css/sidebar.css" />
    <link rel="stylesheet" href="css/bookings.css" />
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
                    <h1>Booking Details</h1>
                    <p>Admin Panel - <?php echo date('F j, Y'); ?></p>
                </div>
                <a href="Bookings.php" class="logout-btn">Back to Bookings</a>
            </div>

            <!-- Booking Details -->
            <div class="content-section">
                <div class="booking-details">
                    <div class="details-header">
                        <h2>Booking #<?php echo htmlspecialchars($booking['id']); ?></h2>
                        <span class="status-<?php echo $booking['status']; ?> status-badge">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </div>

                    <div class="details-grid">
                        <div class="details-section">
                            <h3>Customer Information</h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Name:</label>
                                    <span><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Email:</label>
                                    <span><?php echo htmlspecialchars($booking['email']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Contact:</label>
                                    <span><?php echo htmlspecialchars($booking['contact_number']); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="details-section">
                            <h3>Tour Information</h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Tour Name:</label>
                                    <span><?php echo htmlspecialchars($booking['tour_name']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Destination:</label>
                                    <span><?php echo htmlspecialchars($booking['destination_name']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Package:</label>
                                    <span><?php echo htmlspecialchars($booking['package_name']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Duration:</label>
                                    <span><?php echo htmlspecialchars($booking['duration']); ?> days</span>
                                </div>
                                <div class="info-item">
                                    <label>Description:</label>
                                    <span><?php echo htmlspecialchars($booking['description']); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="details-section">
                            <h3>Booking Information</h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Booking Date:</label>
                                    <span><?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Number of Guests:</label>
                                    <span><?php echo htmlspecialchars($booking['number_of_guests']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Tour Price:</label>
                                    <span>₹<?php echo number_format($booking['tour_price'], 2); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Total Price:</label>
                                    <span>₹<?php echo number_format($booking['total_price'], 2); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="booking-actions">
                        <?php if ($booking['status'] === 'pending'): ?>
                            <a href="#" class="action-btn confirm-btn" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'confirmed')">Confirm Booking</a>
                            <a href="#" class="action-btn cancel-btn" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'cancelled')">Cancel Booking</a>
                        <?php endif; ?>
                        <a href="Bookings.php" class="action-btn back-btn">Back to Bookings</a>
                    </div>
                </div>
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
