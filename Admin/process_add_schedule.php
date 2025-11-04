<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

include '../Database/db_connect.php';

// Validate and sanitize inputs
$tour_id = isset($_POST['tour_id']) ? intval($_POST['tour_id']) : 0;
$start_date = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
$end_date = isset($_POST['end_date']) ? trim($_POST['end_date']) : '';
$availability = isset($_POST['availability']) ? intval($_POST['availability']) : 0;

// Basic validation
$errors = [];

if ($tour_id <= 0) {
    $errors[] = "Please select a valid tour.";
}
if ($start_date === '') {
    $errors[] = "Start date is required.";
}
if ($end_date === '') {
    $errors[] = "End date is required.";
}
if ($availability < 0) {
    $errors[] = "Availability must be 0 or greater.";
}

// Validate date format and logic
$start_timestamp = strtotime($start_date);
$end_timestamp = strtotime($end_date);

if ($start_timestamp === false) {
    $errors[] = "Invalid start date format.";
}
if ($end_timestamp === false) {
    $errors[] = "Invalid end date format.";
}
if ($start_timestamp >= $end_timestamp) {
    $errors[] = "End date must be after start date.";
}

// Check if tour exists
if ($tour_id > 0) {
    $stmt_check = $conn->prepare("SELECT id FROM tours WHERE id = ?");
    $stmt_check->bind_param("i", $tour_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows === 0) {
        $errors[] = "Selected tour does not exist.";
    }
    $stmt_check->close();
}

if (!empty($errors)) {
    $_SESSION['add_schedule_errors'] = $errors;
    header("Location: TourSchedules.php");
    exit();
}

// Insert schedule into database
$stmt = $conn->prepare("INSERT INTO tour_schedules (tour_id, start_date, end_date, availability) VALUES (?, ?, ?, ?)");
$stmt->bind_param("issi", $tour_id, $start_date, $end_date, $availability);

if (!$stmt->execute()) {
    $_SESSION['add_schedule_errors'] = ["Database error: " . $stmt->error];
    $stmt->close();
    $conn->close();
    header("Location: TourSchedules.php");
    exit();
}

$stmt->close();
$conn->close();

// Redirect back to schedules page with success message
$_SESSION['add_schedule_success'] = "Tour schedule added successfully!";
header("Location: TourSchedules.php");
exit();
?>
