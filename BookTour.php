// Include session configuration
include 'session_config.php';

session_start();

// Book Tour Page

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

// Fetch tour details
$tour = null;
$sql_tour = "SELECT t.id, t.title, d.name AS destination_name, t.price, t.duration, p.name AS package_name, t.description
             FROM tours t
             LEFT JOIN destinations d ON t.destination_id = d.id
             LEFT JOIN tour_packages p ON t.package_id = p.id
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $booking_date = date('Y-m-d H:i:s');
    $status = 'pending'; // Default status

    // Check if user already booked this tour
    $check_sql = "SELECT id FROM bookings WHERE user_id = ? AND tour_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $tour_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $error = "You have already booked this tour.";
    } else {
        // Insert booking
        $insert_sql = "INSERT INTO bookings (user_id, tour_id, booking_date, status) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iiss", $user_id, $tour_id, $booking_date, $status);

        if ($insert_stmt->execute()) {
            $success = "Tour booked successfully! Your booking is pending approval.";
        } else {
            $error = "Failed to book tour. Please try again.";
        }
        $insert_stmt->close();
    }
    $check_stmt->close();
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
        <div class="booking-form">
            <h2>Book Tour: <?php echo htmlspecialchars($tour['title']); ?></h2>

            <?php if (isset($success)): ?>
                <div class="message success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="tour-summary">
                <h3>Tour Summary</h3>
                <p><strong>Destination:</strong> <?php echo htmlspecialchars($tour['destination_name'] ?? $tour['location']); ?></p>
                <p><strong>Duration:</strong> <?php echo htmlspecialchars($tour['duration']); ?> days</p>
                <p><strong>Price:</strong> ₹<?php echo number_format($tour['price'], 2); ?></p>
                <p><strong>Package:</strong> <?php echo htmlspecialchars($tour['package_name'] ?? 'Standard'); ?></p>
            </div>

            <form method="POST" action="BookTour.php?id=<?php echo $tour_id; ?>">
                <p>Are you sure you want to book this tour? Your booking will be confirmed after admin approval.</p>

                <button type="submit" class="book-btn">Confirm Booking</button>
            </form>

            <div style="text-align: center; margin-top: 1rem;">
                <a href="TourDetails.php?id=<?php echo $tour_id; ?>" style="color: #04543a;">← Back to Tour Details</a>
            </div>
        </div>
    </main>
</body>
</html>
