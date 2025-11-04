<?php
// Tour Details Page
session_start();

// Include database connection
include 'Database/db_connect.php';

// Get tour ID from URL
$tour_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($tour_id <= 0) {
    header("Location: Tours.php");
    exit();
}

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

// Fetch reviews for this tour from user_interactions table
$reviews = [];
$sql_reviews = "SELECT ui.review_rating, ui.review_comment, ui.review_date, u.first_name, u.last_name
                FROM user_interactions ui
                JOIN users u ON ui.user_id = u.id
                WHERE ui.tour_id = ? AND ui.status = 'approved'
                ORDER BY ui.review_date DESC";
$stmt_reviews = $conn->prepare($sql_reviews);
$stmt_reviews->bind_param("i", $tour_id);
$stmt_reviews->execute();
$result_reviews = $stmt_reviews->get_result();
while ($row = $result_reviews->fetch_assoc()) {
    $reviews[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tour['title']); ?> - Tour Details</title>
    <link rel="stylesheet" href="CSS/Home.css">
    <link rel="stylesheet" href="CSS/Nave.css">
    <link rel="stylesheet" href="CSS/TourDetails.css">
    <script src="Scripts/TourDetails.js"></script>
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'Navbar/Nave.php'; ?>

    <main>
        <section class="tour-details">
            <div class="tour-header">
                <h1><?php echo htmlspecialchars($tour['title']); ?></h1>
                <p class="destination"><?php echo htmlspecialchars($tour['destination_name']); ?></p>
            </div>

            <div class="tour-content">
            <div class="tour-image">
                <?php if (!empty($tour_images)): ?>
                    <div class="tour-image-gallery">
                        <?php foreach ($tour_images as $img): ?>
                            <img src="<?php echo htmlspecialchars($img['image_url']); ?>" alt="<?php echo htmlspecialchars($img['description'] ?? $tour['title']); ?>" class="gallery-image">
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <img src="project image/default-tour.jpg" alt="<?php echo htmlspecialchars($tour['title']); ?>">
                <?php endif; ?>
            </div>

                <div class="tour-info">
                    <div class="info-item">
                        <strong>Price:</strong> ₹<?php echo number_format($tour['price'], 2); ?>
                    </div>
                    <div class="info-item">
                        <strong>Duration:</strong> <?php echo htmlspecialchars($tour['duration']); ?> days
                    </div>
                    <div class="info-item">
                        <strong>Package:</strong> <?php echo htmlspecialchars($tour['package_name']); ?>
                    </div>
                    <div class="info-item">
                        <strong>Description:</strong>
                        <p><?php echo htmlspecialchars($tour['description'] ?? 'No description available.'); ?></p>
                    </div>
                </div>
            </div>

            <div class="reviews-section">
                <h2>Customer Reviews</h2>
                <?php if (!empty($reviews)): ?>
                    <div class="reviews-list">
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-card">
                                <div class="review-header">
                                    <span class="reviewer-name"><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></span>
                                    <span class="review-rating">Rating: <?php echo htmlspecialchars($review['rating']); ?>/5</span>
                                </div>
                                <p class="review-comment"><?php echo htmlspecialchars($review['comment']); ?></p>
                                <span class="review-date"><?php echo htmlspecialchars(date('F j, Y', strtotime($review['review_date']))); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No reviews yet for this tour.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>