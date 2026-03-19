<?php
// Payment Success Page
include '../session_config.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login.php");
    exit();
}

// Get booking ID from URL
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

if ($booking_id <= 0) {
    header("Location: ../MyBookings.php");
    exit();
}

// Include database connection
include '../Database/db_connect.php';

// Get booking details
$sql = "SELECT b.id, b.booking_date, b.status, b.total_price, b.num_people,
               t.title, t.location, t.duration, t.price,
               p.transaction_id, p.payment_method, p.payment_date
        FROM bookings b
        JOIN tours t ON b.tour_id = t.id
        LEFT JOIN payments p ON b.id = p.booking_id
        WHERE b.id = ? AND b.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../MyBookings.php");
    exit();
}

$booking = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Tour and Travel</title>
    <link rel="stylesheet" href="../CSS/Home.css">
    <link rel="stylesheet" href="../CSS/Nave.css">
    <link rel="stylesheet" href="../CSS/Payment.css">
</head>
<body>
    <!-- Include Navbar -->
    <?php include '../Navbar/Nave.php'; ?>

    <main>
        <div class="booking-form" style="max-width: 600px; margin: 2rem auto;">
            <div class="success-message" style="text-align: center; margin-bottom: 2rem;">
                <div class="success-icon">✅</div>
                <h2>Payment Successful!</h2>
                <p>Your booking has been confirmed and payment processed successfully.</p>
            </div>

            <!-- Booking Details -->
            <div class="booking-summary">
                <h3>Booking Details</h3>
                <div class="summary-item">
                    <span>Booking ID:</span>
                    <span>#<?php echo htmlspecialchars($booking['id']); ?></span>
                </div>
                <div class="summary-item">
                    <span>Tour:</span>
                    <span><?php echo htmlspecialchars($booking['title']); ?></span>
                </div>
                <div class="summary-item">
                    <span>Location:</span>
                    <span><?php echo htmlspecialchars($booking['location']); ?></span>
                </div>
                <div class="summary-item">
                    <span>Duration:</span>
                    <span><?php echo htmlspecialchars($booking['duration']); ?> days</span>
                </div>
                <div class="summary-item">
                    <span>Number of People:</span>
                    <span><?php echo htmlspecialchars($booking['num_people']); ?></span>
                </div>
                <div class="summary-item">
                    <span>Total Amount:</span>
                    <span>₹<?php echo number_format($booking['total_price'], 2); ?></span>
                </div>
                <div class="summary-item">
                    <span>Booking Date:</span>
                    <span><?php echo date('F j, Y \a\t g:i A', strtotime($booking['booking_date'])); ?></span>
                </div>
                <div class="summary-item">
                    <span>Status:</span>
                    <span class="status-confirmed"><?php echo htmlspecialchars(ucfirst($booking['status'])); ?></span>
                </div>
            </div>

            <!-- Payment Details -->
            <?php if (!empty($booking['transaction_id'])): ?>
            <div class="payment-details">
                <h3>Payment Information</h3>
                <div class="summary-item">
                    <span>Transaction ID:</span>
                    <span><?php echo htmlspecialchars($booking['transaction_id']); ?></span>
                </div>
                <div class="summary-item">
                    <span>Payment Method:</span>
                    <span><?php echo htmlspecialchars($booking['payment_method']); ?></span>
                </div>
                <div class="summary-item">
                    <span>Payment Date:</span>
                    <span><?php echo date('F j, Y \a\t g:i A', strtotime($booking['payment_date'])); ?></span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="action-buttons" style="text-align: center; margin-top: 2rem;">
                <a href="../MyBookings.php" class="book-btn" style="margin-right: 1rem;">View My Bookings</a>
                <a href="../Tours.php" class="book-btn" style="background: #6c757d;">Book Another Tour</a>
            </div>

            <!-- Additional Information -->
            <div class="info-section" style="margin-top: 2rem; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                <h4>What happens next?</h4>
                <ul style="text-align: left; margin: 1rem 0;">
                    <li>You will receive a confirmation email with your booking details.</li>
                    <li>Our team will contact you within 24 hours to confirm your travel arrangements.</li>
                    <li>Please keep your booking ID handy for any future reference.</li>
                    <li>For any queries, contact our support team.</li>
                </ul>
            </div>
        </div>
    </main>
</body>
</html>
