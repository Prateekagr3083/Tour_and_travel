<?php
// Include session configuration
include 'session_config.php';

// User-side Tours Page

// Include database connection
include 'Database/db_connect.php';

$tours = [];
$sql = "SELECT t.id, t.title, t.location, t.price, t.duration
        FROM tours t
        ORDER BY t.id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Fetch all images for the tour from tour_images table
        $image_sql = "SELECT image_url FROM tour_images WHERE tour_id = " . intval($row['id']);
        $image_result = $conn->query($image_sql);
        $tour_images = [];
        if ($image_result && $image_result->num_rows > 0) {
            while ($image_row = $image_result->fetch_assoc()) {
                $tour_images[] = $image_row['image_url'];
            }
        } else {
            $tour_images[] = 'project image/default-tour.jpg';
        }
        $row['tour_images'] = $tour_images;
        $tours[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tours - Tour and Travel</title>
    <link rel="stylesheet" href="CSS/Home.css">
    <link rel="stylesheet" href="CSS/Nave.css">
    <link rel="stylesheet" href="CSS/Tours.css">
    <script src="Scripts/Tours.js"></script>
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'Navbar/Nave.php'; ?>

    <main>
        <section class="tours-section">
            <h1>Available Tours</h1>
            <div class="tours-grid">
                <?php if (!empty($tours)): ?>
                    <?php foreach ($tours as $tour): ?>
                        <div class="card" data-tour-id="<?php echo htmlspecialchars($tour['id']); ?>">
                            <div class="image_container">
                                <div class="image-slider">
                                    <?php foreach ($tour['tour_images'] as $index => $img_url): ?>
                                        <img src="<?php echo htmlspecialchars($img_url); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>" class="tour-image <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="title">
                                <span><?php echo htmlspecialchars($tour['title']); ?></span>
                            </div>
                            <div class="size">
                                <span><?php echo htmlspecialchars($tour['location']); ?></span>
                            </div>
                            <div class="action">
                                <div class="price">
                                    <span>₹<?php echo number_format($tour['price'], 2); ?></span>
                                </div>
                                <a href="TourDetails.php?id=<?php echo htmlspecialchars($tour['id']); ?>" class="cart-button">
                                    <span>View Details</span>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-tours">
                        <h3>No Tours Available</h3>
                        <p>Please check back later for new tours.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
