<?php
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

// Fetch user's bookings with participant details
$bookings = [];
$sql = "SELECT b.id, b.booking_date, b.status, b.total_price, b.num_people, t.title, t.price as tour_price, t.location, t.duration
        FROM bookings b
        JOIN tours t ON b.tour_id = t.id
        WHERE b.user_id = ?
        ORDER BY b.booking_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // Fetch participant details for this booking
    $participants_sql = "SELECT person_name, age, gender, health_conditions FROM booking_details WHERE booking_id = ?";
    $stmt_participants = $conn->prepare($participants_sql);
    $stmt_participants->bind_param("i", $row['id']);
    $stmt_participants->execute();
    $participants_result = $stmt_participants->get_result();

    $participants = [];
    while ($participant = $participants_result->fetch_assoc()) {
        $participants[] = $participant;
    }

    $row['participants'] = $participants;
    $bookings[] = $row;
    $stmt_participants->close();
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

            <?php if (isset($_SESSION['booking_success'])): ?>
                <div class="message success"><?php echo $_SESSION['booking_success']; unset($_SESSION['booking_success']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['booking_error'])): ?>
                <div class="message error"><?php echo $_SESSION['booking_error']; unset($_SESSION['booking_error']); ?></div>
            <?php endif; ?>

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
                                    <span class="detail-value"><?php echo htmlspecialchars($booking['location'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Duration</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($booking['duration']); ?> days</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Number of People</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($booking['num_people']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Total Price</span>
                                    <span class="detail-value">₹<?php echo number_format($booking['total_price'], 2); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Booking Date</span>
                                    <span class="detail-value"><?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></span>
                                </div>
                            </div>

                            <?php if (!empty($booking['participants'])): ?>
                                <div class="participants-section">
                                    <h4>Participants:</h4>
                                    <ul class="participants-list">
                                        <?php foreach ($booking['participants'] as $participant): ?>
                                            <li class="participant-item">
                                                <strong><?php echo htmlspecialchars($participant['person_name']); ?></strong>
                                                (<?php echo htmlspecialchars($participant['age']); ?> years old,
                                                <?php echo htmlspecialchars(ucfirst($participant['gender'])); ?>)
                                                <?php if (!empty($participant['health_conditions'])): ?>
                                                    <br><small><em>Health: <?php echo htmlspecialchars($participant['health_conditions']); ?></em></small>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

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
