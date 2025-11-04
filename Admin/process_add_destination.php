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
$country = isset($_POST['country']) ? trim($_POST['country']) : '';

// Basic validation
$errors = [];

if ($name === '') {
    $errors[] = "Destination name is required.";
}
if ($country === '') {
    $errors[] = "Country is required.";
}

if (!empty($errors)) {
    $_SESSION['destination_errors'] = $errors;
    header("Location: Destinations.php");
    exit();
}

// Insert destination into database
$stmt = $conn->prepare("INSERT INTO destinations (name, description, country) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $description, $country);

if (!$stmt->execute()) {
    $_SESSION['destination_errors'] = ["Database error: " . $stmt->error];
    $stmt->close();
    $conn->close();
    header("Location: Destinations.php");
    exit();
}

$stmt->close();
$conn->close();

// Redirect back to destinations page with success message
$_SESSION['destination_success'] = "Destination added successfully!";
header("Location: Destinations.php");
exit();
?>
