<?php
// User-side Tours Page
session_start();

// Include database connection
include 'Database/db_connect.php';

$tours = [];
$sql = "SELECT t.id, t.title, d.name AS destination_name, t.price, t.duration, p.name AS package_name
        FROM tours t
        LEFT JOIN destinations d ON t.destination_id = d.id
        LEFT JOIN tour_packages p ON t.package_id = p.id
        ORDER BY t.id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Fetch first image for the tour from tour_images table
        $image_sql = "SELECT image_url FROM tour_images WHERE tour_id = " . intval($row['id']) . " LIMIT 1";
        $image_result = $conn->query($image_sql);
        $image_url = 'project image/default-tour.jpg';
        if ($image_result && $image_result->num_rows > 0) {
            $image_row = $image_result->fetch_assoc();
            $image_url = $image_row['image_url'];
        }
        $row['image_url'] = $image_url;
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
                                <img src="<?php echo htmlspecialchars($tour['image_path'] ?? 'project image/default-tour.jpg'); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>" class="tour-image">
                            </div>
                            <div class="title">
                                <span><?php echo htmlspecialchars($tour['title']); ?></span>
                            </div>
                            <div class="size">
                                <span><?php echo htmlspecialchars($tour['destination_name']); ?></span>
                            </div>
                            <div class="action">
                                <div class="price">
                                    <span>$<?php echo number_format($tour['price'], 2); ?></span>
                                </div>
                                <button class="cart-button">
                                    <span>View Details</span>
                                </button>
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
