<?php
// Edit Tour Page - Access restricted to admin users only
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

// Include database connection
include '../Database/db_connect.php';

// Get tour ID from URL
$tour_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($tour_id <= 0) {
    header("Location: Tours.php");
    exit();
}

// Fetch tour details
$tour = null;
$sql_tour = "SELECT * FROM tours WHERE id = ?";
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
$image_sql = "SELECT * FROM tour_images WHERE tour_id = ?";
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
    <title>Edit Tour - Admin Panel</title>
    <link rel="stylesheet" href="css/Admin.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/tours.css">
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
                    <h1>Edit Tour</h1>
                    <p>Admin Panel - <?php echo date('F j, Y'); ?></p>
                </div>
                <a href="Tours.php" class="logout-btn">Back to Tours</a>
            </div>

            <!-- Edit Tour Form -->
            <div class="content-section">
                <form id="edit-tour-form" action="process_edit_tour.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tour_id" value="<?php echo $tour['id']; ?>">

                    <div class="form-group">
                        <label for="tour-name">Tour Name:</label>
                        <input type="text" id="tour-name" name="title" value="<?php echo htmlspecialchars($tour['title']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="3" required><?php echo htmlspecialchars($tour['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="destination">Destination:</label>
                        <select id="destination" name="destination_id" required>
                            <option value="">Select Destination</option>
                            <?php
                            $dest_conn = new mysqli("localhost", "root", "", "Tour_and_travel");
                            $dest_sql = "SELECT id, name FROM destinations ORDER BY name";
                            $dest_result = $dest_conn->query($dest_sql);
                            while ($dest = $dest_result->fetch_assoc()) {
                                $selected = ($dest['id'] == $tour['destination_id']) ? 'selected' : '';
                                echo '<option value="' . $dest['id'] . '" ' . $selected . '>' . htmlspecialchars($dest['name']) . '</option>';
                            }
                            $dest_conn->close();
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="package">Tour Package:</label>
                        <select id="package" name="package_id" required>
                            <option value="">Select Package</option>
                            <?php
                            $pkg_conn = new mysqli("localhost", "root", "", "Tour_and_travel");
                            $pkg_sql = "SELECT id, name FROM tour_packages ORDER BY name";
                            $pkg_result = $pkg_conn->query($pkg_sql);
                            while ($pkg = $pkg_result->fetch_assoc()) {
                                $selected = ($pkg['id'] == $tour['package_id']) ? 'selected' : '';
                                echo '<option value="' . $pkg['id'] . '" ' . $selected . '>' . htmlspecialchars($pkg['name']) . '</option>';
                            }
                            $pkg_conn->close();
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price">Price (₹):</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($tour['price']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="duration">Duration (days):</label>
                        <input type="number" id="duration" name="duration" min="1" value="<?php echo htmlspecialchars($tour['duration']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="new-image">Add New Image (optional):</label>
                        <input type="file" id="new-image" name="new_image" accept="image/*">
                    </div>

                    <!-- Current Images -->
                    <?php if (!empty($tour_images)): ?>
                        <div class="form-group">
                            <label>Current Images:</label>
                            <div class="current-images">
                                <?php foreach ($tour_images as $img): ?>
                                    <div class="image-item">
                                        <img src="<?php echo htmlspecialchars($img['image_url']); ?>" alt="Tour Image" style="max-width: 100px; max-height: 100px;">
                                        <button type="button" class="delete-image-btn" data-image-id="<?php echo $img['id']; ?>">Delete</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Tour</button>
                        <a href="Tours.php" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Handle image deletion
        document.querySelectorAll('.delete-image-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const imageId = this.getAttribute('data-image-id');
                if (confirm('Are you sure you want to delete this image?')) {
                    // Create form to submit deletion
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'process_delete_image.php';

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'image_id';
                    input.value = imageId;

                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>
