<?php
// Include session configuration
include 'session_config.php';

// Book Tour Page
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

// Include database connection
include 'Database/db_connect.php';

// Get tour ID from URL
$tour_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($tour_id <= 0) {
    header("Location: Tours.php");
    exit();
}

$tour = null;
$sql_tour = "SELECT t.id, t.title, t.location, t.price, t.duration, t.description
             FROM tours t
             WHERE t.id = ?";
$stmt = $conn->prepare($sql_tour);
$stmt->bind_param("i", $tour_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $tour = $result->fetch_assoc();
} else {
    header("Location: Tours.php");
    exit();
}

// Fetch tour images
$tour_images = [];
$image_sql = "SELECT image_url, description FROM tour_images WHERE tour_id = ?";
$stmt_images = $conn->prepare($image_sql);
$stmt_images->bind_param("i", $tour_id);
$stmt_images->execute();
$result_images = $stmt_images->get_result();
while ($row = $result_images->fetch_assoc()) {
    $tour_images[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tour - <?php echo htmlspecialchars($tour['title']); ?></title>
    <link rel="stylesheet" href="CSS/Home.css">
    <link rel="stylesheet" href="CSS/Nave.css">
    <link rel="stylesheet" href="CSS/BookTour.css">
    <script src="Scripts/BookTour.js"></script>
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'Navbar/Nave.php'; ?>

    <main>
        <!-- Tour Details Section (Text Only) -->
        <section class="tour-details-section">
            <div class="tour-header">
                <h1><?php echo htmlspecialchars($tour['title']); ?></h1>
                <div class="tour-info-only">
                    <div class="tour-info">
                        <div class="info-item">
                            <strong>Price:</strong> ₹<?php echo number_format($tour['price'], 2); ?> per person
                        </div>
                        <div class="info-item">
                            <strong>Duration:</strong> <?php echo htmlspecialchars($tour['duration']); ?> days
                        </div>
                        <div class="info-item">
                            <strong>Location:</strong> <?php echo htmlspecialchars($tour['location'] ?? 'Location not specified'); ?>
                        </div>
                        <div class="info-item">
                            <strong>Description:</strong>
                            <p><?php echo htmlspecialchars($tour['description'] ?? 'No description available.'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Booking Form Section -->
        <section class="booking-section">
            <div class="booking-form">
                <h2>Book This Tour</h2>

                <?php if (isset($_SESSION['booking_success'])): ?>
                    <div class="message success"><?php echo $_SESSION['booking_success']; unset($_SESSION['booking_success']); ?></div>
                <?php endif; ?>

                <?php if (isset($_SESSION['booking_error'])): ?>
                    <div class="message error"><?php echo $_SESSION['booking_error']; unset($_SESSION['booking_error']); ?></div>
                <?php endif; ?>

                <form method="POST" action="process_booking.php" id="booking-form">
                    <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">

                    <!-- Number of People -->
                    <div class="form-group">
                        <label for="num_people">Number of People:</label>
                        <input type="number" name="num_people" id="num_people" required min="1" max="20" placeholder="Enter number of people">
                    </div>

                    <!-- Dynamic Person Fields Container -->
                    <div id="person-fields" class="person-fields">
                        <!-- Fields will be dynamically added here -->
                    </div>

                    <!-- Booking Summary -->
                    <div class="booking-summary">
                        <h3>Booking Summary</h3>
                        <div class="summary-item">
                            <span>Tour:</span>
                            <span><?php echo htmlspecialchars($tour['title']); ?></span>
                        </div>
                        <div class="summary-item">
                            <span>Price per person:</span>
                            <span id="price-per-person">₹<?php echo number_format($tour['price'], 2); ?></span>
                        </div>
                        <div class="summary-item total-price">
                            <span>Total Price:</span>
                            <span id="total-price">₹0.00</span>
                        </div>
                    </div>

                    <button type="submit" class="book-btn">Confirm Booking</button>
                </form>

                <div style="text-align: center; margin-top: 1rem;">
                    <a href="TourDetails.php?id=<?php echo $tour_id; ?>" style="color: #04543a;">← Back to Tour Details</a>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
