<?php
// Admin Dashboard - Access restricted to admin users only
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

// Include database connection
include '../Database/db_connect.php';

// Get admin information
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];
$admin_email = $_SESSION['admin_email'];

// Get statistics for dashboard
$users_count = 0;
$bookings_count = 0;
$tours_count = 0;

// Count total users
$sql_users = "SELECT COUNT(*) as total_users FROM users WHERE role != 'admin'";
$result_users = $conn->query($sql_users);
if ($result_users) {
    $users_count = $result_users->fetch_assoc()['total_users'];
}

// Count total bookings (assuming bookings table exists)
$sql_bookings = "SELECT COUNT(*) as total_bookings FROM bookings";
$result_bookings = $conn->query($sql_bookings);
if ($result_bookings) {
    $bookings_count = $result_bookings->fetch_assoc()['total_bookings'];
}

// Count total tours (assuming tours table exists)
$sql_tours = "SELECT COUNT(*) as total_tours FROM tours";
$result_tours = $conn->query($sql_tours);
if ($result_tours) {
    $tours_count = $result_tours->fetch_assoc()['total_tours'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
