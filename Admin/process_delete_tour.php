<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

include '../Database/db_connect.php';

// Get tour ID from URL
$tour_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($tour_id <= 0) {
    $_SESSION['delete_tour_errors'] = ["Invalid tour ID."];
    header("Location: Tours.php");
    exit();
}

// Check if tour exists
$check_sql = "SELECT id FROM tours WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $tour_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $_SESSION['delete_tour_errors'] = ["Tour not found."];
    $check_stmt->close();
    $conn->close();
    header("Location: Tours.php");
    exit();
}
$check_stmt->close();

// Delete tour images first (due to foreign key constraint)
$delete_images_sql = "DELETE FROM tour_images WHERE tour_id = ?";
$delete_images_stmt = $conn->prepare($delete_images_sql);
$delete_images_stmt->bind_param("i", $tour_id);
$delete_images_stmt->execute();
$delete_images_stmt->close();

// Delete user interactions (reviews/bookings) for this tour
$delete_interactions_sql = "DELETE FROM user_interactions WHERE tour_id = ?";
$delete_interactions_stmt = $conn->prepare($delete_interactions_sql);
$delete_interactions_stmt->bind_param("i", $tour_id);
$delete_interactions_stmt->execute();
$delete_interactions_stmt->close();

// Delete bookings for this tour
$delete_bookings_sql = "DELETE FROM bookings WHERE tour_id = ?";
$delete_bookings_stmt = $conn->prepare($delete_bookings_sql);
$delete_bookings_stmt->bind_param("i", $tour_id);
$delete_bookings_stmt->execute();
$delete_bookings_stmt->close();

// Delete the tour
$delete_tour_sql = "DELETE FROM tours WHERE id = ?";
$delete_tour_stmt = $conn->prepare($delete_tour_sql);
$delete_tour_stmt->bind_param("i", $tour_id);

if ($delete_tour_stmt->execute()) {
    $_SESSION['delete_tour_success'] = "Tour deleted successfully!";
} else {
    $_SESSION['delete_tour_errors'] = ["Failed to delete tour: " . $delete_tour_stmt->error];
}

$delete_tour_stmt->close();
$conn->close();

header("Location: Tours.php");
exit();
?>
