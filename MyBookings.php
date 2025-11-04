// Include session configuration
include 'session_config.php';

session_start();

// My Bookings Page

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

// Include database connection
include 'Database/db_connect.php';

$user_id = $_SESSION['user_id'];

// Fetch user's bookings
$bookings = [];
$sql = "SELECT b.id, b.booking_date, b.status, t.title, t.price, d.name AS destination_name, t.duration
        FROM bookings b
        JOIN tours t ON b.tour_id = t.id
        LEFT JOIN destinations d ON t.destination_id = d.id
        WHERE b.user_id = ?
        ORDER BY b.booking_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Tour and Travel</title>
    <link rel="stylesheet" href="CSS/Home.css">
    <link rel="stylesheet" href="CSS/Nave.css">
    <link rel="stylesheet" href="CSS/MyBookings.css">
    <script src="Scripts/MyBookings.js"></script>
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'Navbar/Nave.php'; ?>

    <main>
        <section class="bookings-section">
            <h1>My Bookings</h1>

            <?php if (!empty($bookings)): ?>
                <div class="bookings-grid">
                    <?php foreach ($bookings as $booking): ?>
                        <div class="booking-card">
                            <div class="booking-header">
                                <h2 class="booking-title"><?php echo htmlspecialchars($booking['title']); ?></h2>
                                <span class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                                    <?php echo htmlspecialchars($booking['status']); ?>
                                </span>
                            </div>

                            <div class="booking-details">
                                <div class="detail-item">
                                    <span class="detail-label">Destination</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($booking['destination_name'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Duration</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($booking['duration']); ?> days</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Price</span>
                                    <span class="detail-value">₹<?php echo number_format($booking['price'], 2); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Booking Date</span>
                                    <span class="detail-value"><?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></span>
                                </div>
                            </div>

                            <div class="booking-date">
                                Booked on: <?php echo date('F j, Y \a\t g:i A', strtotime($booking['booking_date'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-bookings">
                    <i>📋</i>
                    <h3>No Bookings Yet</h3>
                    <p>You haven't booked any tours yet. Start exploring amazing destinations!</p>
                    <a href="Tours.php" class="browse-btn">Browse Tours</a>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
