<?php
// View Review Details Page - Access restricted to admin users only
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

// Get review ID from URL
$review_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($review_id <= 0) {
    header("Location: Reviews.php");
    exit();
}

// Include database connection
include '../Database/db_connect.php';

// Get review details with user and tour information
$sql = "SELECT ui.id, ui.review_rating, ui.review_comment, ui.review_date, ui.status,
        u.first_name, u.last_name, u.email,
        t.title as tour_name, t.description as tour_description,
        d.name as destination_name, p.name as package_name
        FROM user_interactions ui
        JOIN users u ON ui.user_id = u.id
        JOIN tours t ON ui.tour_id = t.id
        LEFT JOIN destinations d ON t.destination_id = d.id
        LEFT JOIN tour_packages p ON t.package_id = p.id
        WHERE ui.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $review_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: Reviews.php");
    exit();
}

$review = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View Review - Admin Panel</title>
    <link rel="stylesheet" href="css/Admin.css" />
    <link rel="stylesheet" href="css/sidebar.css" />
    <link rel="stylesheet" href="css/reviews.css" />
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
                    <h1>Review Details</h1>
                    <p>Admin Panel - <?php echo date('F j, Y'); ?></p>
                </div>
                <a href="Reviews.php" class="logout-btn">Back to Reviews</a>
            </div>

            <!-- Review Details -->
            <div class="content-section">
                <div class="review-details">
                    <div class="details-header">
                        <h2>Review #<?php echo htmlspecialchars($review['id']); ?></h2>
                        <span class="status-<?php echo $review['status']; ?> status-badge">
                            <?php echo ucfirst($review['status']); ?>
                        </span>
                    </div>

                    <div class="details-grid">
                        <div class="details-section">
                            <h3>Customer Information</h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Name:</label>
                                    <span><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Email:</label>
                                    <span><?php echo htmlspecialchars($review['email']); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="details-section">
                            <h3>Tour Information</h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Tour Name:</label>
                                    <span><?php echo htmlspecialchars($review['tour_name']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Destination:</label>
                                    <span><?php echo htmlspecialchars($review['destination_name']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Package:</label>
                                    <span><?php echo htmlspecialchars($review['package_name']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Description:</label>
                                    <span><?php echo htmlspecialchars($review['tour_description']); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="details-section">
                            <h3>Review Information</h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Review Date:</label>
                                    <span><?php echo date('F j, Y', strtotime($review['review_date'])); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Rating:</label>
                                    <div class="rating-display">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star <?php echo $i <= $review['review_rating'] ? 'filled' : ''; ?>">★</span>
                                        <?php endfor; ?>
                                        <span class="rating-number">(<?php echo htmlspecialchars($review['review_rating']); ?>/5)</span>
                                    </div>
                                </div>
                                <div class="info-item full-width">
                                    <label>Comment:</label>
                                    <div class="review-comment">
                                        <?php echo nl2br(htmlspecialchars($review['review_comment'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="review-actions">
                        <?php if ($review['status'] === 'pending'): ?>
                            <a href="#" class="action-btn approve-btn" onclick="updateReviewStatus(<?php echo $review['id']; ?>, 'approved')">Approve Review</a>
                            <a href="#" class="action-btn reject-btn" onclick="updateReviewStatus(<?php echo $review['id']; ?>, 'rejected')">Reject Review</a>
                        <?php endif; ?>
                        <a href="Reviews.php" class="action-btn back-btn">Back to Reviews</a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    function updateReviewStatus(reviewId, status) {
        if (confirm('Are you sure you want to ' + status + ' this review?')) {
            window.location.href = 'process_review_approval.php?id=' + reviewId + '&status=' + status;
        }
    }
    </script>
</body>
</html>
