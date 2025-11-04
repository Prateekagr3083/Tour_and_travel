<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

include '../Database/db_connect.php';

// Get image ID from POST
$image_id = isset($_POST['image_id']) ? intval($_POST['image_id']) : 0;

if ($image_id <= 0) {
    $_SESSION['delete_image_errors'] = ["Invalid image ID."];
    header("Location: Tours.php");
    exit();
}

// Get image details before deletion
$select_sql = "SELECT image_url FROM tour_images WHERE id = ?";
$select_stmt = $conn->prepare($select_sql);
$select_stmt->bind_param("i", $image_id);
$select_stmt->execute();
$result = $select_stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['delete_image_errors'] = ["Image not found."];
    $select_stmt->close();
    $conn->close();
    header("Location: Tours.php");
    exit();
}

$row = $result->fetch_assoc();
$image_url = $row['image_url'];
$select_stmt->close();

// Delete image record from database
$delete_sql = "DELETE FROM tour_images WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $image_id);

if ($delete_stmt->execute()) {
    // Try to delete the physical file
    $file_path = '../' . $image_url;
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    $_SESSION['delete_image_success'] = "Image deleted successfully!";
} else {
    $_SESSION['delete_image_errors'] = ["Failed to delete image: " . $delete_stmt->error];
}

$delete_stmt->close();
$conn->close();

// Redirect back to edit tour page (we need to get tour_id)
$tour_id = isset($_POST['tour_id']) ? intval($_POST['tour_id']) : 0;
if ($tour_id > 0) {
    header("Location: edit_tour.php?id=" . $tour_id);
} else {
    header("Location: Tours.php");
}
exit();
?>
