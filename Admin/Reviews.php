<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit();
}

include '../Database/db_connect.php';

// Fetch reviews with user and tour details
$sql = "SELECT r.id, u.first_name, u.last_name, t.title, r.rating, r.comment, r.review_date
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        JOIN tours t ON r.tour_id = t.id
        ORDER BY r.review_date DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Reviews - Tour & Travel</title>
    <link rel="stylesheet" href="css/Admin.css" />
    <link rel="stylesheet" href="css/reviews.css" />
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <header class="header">
            <div class="welcome-message">
                <h1>Reviews Management</h1>
                <p>Manage all tour reviews</p>
            </div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>
        <section class="content-section">
            <?php if ($result && $result->num_rows > 0): ?>
            <table class="reviews-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Tour</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Review Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['rating']); ?></td>
                        <td><?php echo htmlspecialchars($row['comment']); ?></td>
                        <td><?php echo htmlspecialchars($row['review_date']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="no-reviews">No reviews found.</p>
            <?php endif; ?>
        </section>
    </div>
    <script src="js/reviews.js"></script>
</body>
</html>
<?php $conn->close(); ?>
