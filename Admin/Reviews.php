<?php
// Admin Reviews Page - Access restricted to admin users only
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

// Fetch reviews with user and tour details from user_interactions table
$reviews = [];
$sql = "SELECT ui.id, ui.review_rating, ui.review_comment, ui.review_date, ui.status,
        u.first_name, u.last_name, u.email,
        t.title as tour_name, t.location as destination_name
        FROM user_interactions ui
        JOIN users u ON ui.user_id = u.id
        JOIN tours t ON ui.tour_id = t.id
        LEFT JOIN destinations d ON t.destination_id = d.id
        WHERE ui.review_rating IS NOT NULL AND ui.review_comment IS NOT NULL
        ORDER BY ui.review_date DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Reviews - Admin Panel</title>
    <link rel="stylesheet" href="css/Admin.css" />
    <link rel="stylesheet" href="css/sidebar.css" />
    <link rel="stylesheet" href="css/reviews.css" />
    <script src="js/sidebar.js"></script>
    <script src="js/reviews.js"></script>
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
                    <h1>Manage Reviews</h1>
                    <p>Admin Panel - <?php echo date('F j, Y'); ?></p>
                    <?php
                    if (isset($_SESSION['review_success'])) {
                        echo '<div class="success-message" style="color: green; margin-top: 10px;">' . htmlspecialchars($_SESSION['review_success']) . '</div>';
                        unset($_SESSION['review_success']);
                    }
                    if (isset($_SESSION['review_error'])) {
                        echo '<div class="error-message" style="color: red; margin-top: 10px;">' . htmlspecialchars($_SESSION['review_error']) . '</div>';
                        unset($_SESSION['review_error']);
                    }
                    ?>
                </div>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>

            <!-- Reviews Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>All Reviews</h2>
                </div>

                <?php if (!empty($reviews)): ?>
                    <table class="reviews-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Tour</th>
                                <th>Destination</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Review Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($review['id']); ?></td>
                                    <td>
                                        <div><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></div>
                                        <small><?php echo htmlspecialchars($review['email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($review['tour_name']); ?></td>
                                    <td><?php echo htmlspecialchars($review['destination_name']); ?></td>
                                    <td>
                                        <div class="rating-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="star <?php echo $i <= $review['review_rating'] ? 'filled' : ''; ?>">★</span>
                                            <?php endfor; ?>
                                            <span class="rating-number">(<?php echo htmlspecialchars($review['review_rating']); ?>/5)</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="comment-preview">
                                            <?php echo htmlspecialchars(substr($review['review_comment'], 0, 100)); ?>
                                            <?php if (strlen($review['review_comment']) > 100): ?>...<?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($review['review_date'])); ?></td>
                                    <td>
                                        <span class="status-<?php echo $review['status']; ?>">
                                            <?php echo ucfirst($review['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view_review.php?id=<?php echo $review['id']; ?>" class="action-btn view-btn">View</a>
                                        <?php if ($review['status'] === 'pending'): ?>
                                            <a href="#" class="action-btn approve-btn" onclick="updateReviewStatus(<?php echo $review['id']; ?>, 'approved')">Approve</a>
                                            <a href="#" class="action-btn reject-btn" onclick="updateReviewStatus(<?php echo $review['id']; ?>, 'rejected')">Reject</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-reviews">
                        <i>⭐</i>
                        <h3>No Reviews Found</h3>
                        <p>There are no reviews in the system yet.</p>
                    </div>
                <?php endif; ?>
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
