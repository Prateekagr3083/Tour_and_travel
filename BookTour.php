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
        <!-- Tour Details Section -->
        <section class="tour-details-section">
            <div class="tour-header">
                <h1><?php echo htmlspecialchars($tour['title']); ?></h1>
                <div class="tour-content">
                    <div class="tour-gallery">
                        <div class="image-slider">
                            <?php if (!empty($tour_images)): ?>
                                <?php foreach ($tour_images as $index => $img): ?>
                                    <img src="<?php echo htmlspecialchars($img['image_url']); ?>" alt="<?php echo htmlspecialchars($img['description'] ?? $tour['title']); ?>" class="gallery-image <?php echo $index === 0 ? 'active' : ''; ?>">
                                <?php endforeach; ?>
                            <?php else: ?>
                                <img src="project image/default-tour.jpg" alt="<?php echo htmlspecialchars($tour['title']); ?>" class="gallery-image active">
                            <?php endif; ?>
                        </div>
                        <?php if (count($tour_images) > 1): ?>
                            <div class="slider-dots">
                                <?php for ($i = 0; $i < count($tour_images); $i++): ?>
                                    <span class="dot <?php echo $i === 0 ? 'active' : ''; ?>" data-slide="<?php echo $i; ?>"></span>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>
                    </div>

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
                        <select name="num_people" id="num_people" required>
                            <option value="">Select number of people</option>
                            <option value="1">1 Person</option>
                            <option value="2">2 People</option>
                            <option value="3">3 People</option>
                            <option value="4">4 People</option>
                            <option value="5">5 People</option>
                            <option value="6">6 People</option>
                        </select>
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
                            <span>₹<?php echo number_format($tour['price'], 2); ?></span>
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
