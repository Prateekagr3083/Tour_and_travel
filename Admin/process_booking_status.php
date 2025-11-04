<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

if ($booking_id <= 0 || !in_array($status, ['confirmed', 'cancelled'])) {
    $_SESSION['booking_error'] = "Invalid booking ID or status.";
    header("Location: Bookings.php");
    exit();
}

include '../Database/db_connect.php';

// Update booking status
$sql = "UPDATE bookings SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $booking_id);

if ($stmt->execute()) {
    $_SESSION['booking_success'] = "Booking " . $status . " successfully!";
} else {
    $_SESSION['booking_error'] = "Failed to update booking status: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: Bookings.php");
exit();
?>
