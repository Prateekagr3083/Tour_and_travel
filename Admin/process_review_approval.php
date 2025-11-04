<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

include '../Database/db_connect.php';

$review_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

if ($review_id <= 0 || !in_array($status, ['approved', 'rejected'])) {
    $_SESSION['review_error'] = "Invalid review ID or status.";
    header("Location: Reviews.php");
    exit();
}

// Update review status
$sql = "UPDATE user_interactions SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $review_id);

if ($stmt->execute()) {
    $_SESSION['review_success'] = "Review " . $status . " successfully!";
} else {
    $_SESSION['review_error'] = "Failed to update review status: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: Reviews.php");
exit();
?>
