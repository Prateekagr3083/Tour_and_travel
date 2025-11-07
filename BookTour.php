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
