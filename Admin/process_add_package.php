<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

include '../Database/db_connect.php';

// Validate and sanitize inputs
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
$duration = isset($_POST['duration']) ? intval($_POST['duration']) : 0;

// Basic validation
$errors = [];

if ($name === '') {
    $errors[] = "Package name is required.";
}
if ($price <= 0) {
    $errors[] = "Price must be greater than zero.";
}
if ($duration <= 0) {
    $errors[] = "Duration must be at least 1 day.";
}

if (!empty($errors)) {
    $_SESSION['package_errors'] = $errors;
    header("Location: Packages.php");
    exit();
}

// Insert package into database
$stmt = $conn->prepare("INSERT INTO tour_packages (name, description, price, duration) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssdi", $name, $description, $price, $duration);

if (!$stmt->execute()) {
    $_SESSION['package_errors'] = ["Database error: " . $stmt->error];
    $stmt->close();
    $conn->close();
    header("Location: Packages.php");
    exit();
}

$stmt->close();
$conn->close();

// Redirect back to packages page with success message
$_SESSION['package_success'] = "Tour package added successfully!";
header("Location: Packages.php");
exit();
?>
