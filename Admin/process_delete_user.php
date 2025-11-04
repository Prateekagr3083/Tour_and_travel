<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id <= 0) {
    $_SESSION['user_error'] = "Invalid user ID.";
    header("Location: Users.php");
    exit();
}

include '../Database/db_connect.php';

// Check if user exists and is not an admin
$check_sql = "SELECT id FROM users WHERE id = ? AND role != 'admin'";
$stmt_check = $conn->prepare($check_sql);
$stmt_check->bind_param("i", $user_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    $_SESSION['user_error'] = "User not found or cannot be deleted.";
    $stmt_check->close();
    $conn->close();
    header("Location: Users.php");
    exit();
}
$stmt_check->close();

// Delete user interactions first (foreign key constraint)
$delete_interactions = "DELETE FROM user_interactions WHERE user_id = ?";
$stmt_interactions = $conn->prepare($delete_interactions);
$stmt_interactions->bind_param("i", $user_id);
$stmt_interactions->execute();
$stmt_interactions->close();

// Delete bookings
$delete_bookings = "DELETE FROM bookings WHERE user_id = ?";
$stmt_bookings = $conn->prepare($delete_bookings);
$stmt_bookings->bind_param("i", $user_id);
$stmt_bookings->execute();
$stmt_bookings->close();

// Delete user
$delete_sql = "DELETE FROM users WHERE id = ? AND role != 'admin'";
$stmt = $conn->prepare($delete_sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $_SESSION['user_success'] = "User deleted successfully!";
} else {
    $_SESSION['user_error'] = "Failed to delete user: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: Users.php");
exit();
?>
