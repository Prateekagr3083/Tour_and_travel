<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

include '../Database/db_connect.php';

// Validate and sanitize inputs
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
$duration = isset($_POST['duration']) ? intval($_POST['duration']) : 0;

// Basic validation
$errors = [];

if (empty($title)) {
    $errors[] = "Tour name is required.";
}
if (empty($description)) {
    $errors[] = "Description is required.";
}
if (empty($location)) {
    $errors[] = "Location is required.";
}
if ($price <= 0) {
    $errors[] = "Price must be greater than zero.";
}
if ($duration < 1) {
    $errors[] = "Duration must be at least 1 day.";
}
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = "Image upload failed or missing.";
}

if (!empty($errors)) {
    $_SESSION['add_tour_errors'] = $errors;
    header("Location: Tours.php");
    exit();
}

// Insert tour into database
$stmt = $conn->prepare("INSERT INTO tours (title, description, location, price, duration) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssdsi", $title, $description, $location, $price, $duration);

if (!$stmt->execute()) {
    $_SESSION['add_tour_errors'] = ["Database error: " . $stmt->error];
    $stmt->close();
    $conn->close();
    header("Location: Tours.php");
    exit();
}

$tour_id = $stmt->insert_id;
$stmt->close();

// Handle image upload
$upload_dir = '../project image/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$image_name = basename($_FILES['image']['name']);
$target_file = $upload_dir . $image_name;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Validate image file type
$allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($imageFileType, $allowed_types)) {
    $_SESSION['add_tour_errors'] = ["Only JPG, JPEG, PNG & GIF files are allowed."];
    header("Location: Tours.php");
    exit();
}

// Move uploaded file
if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
    $_SESSION['add_tour_errors'] = ["Failed to upload image."];
    header("Location: Tours.php");
    exit();
}

// Insert image record
$stmt_img = $conn->prepare("INSERT INTO tour_images (tour_id, image_url, description) VALUES (?, ?, ?)");
$image_url = 'project image/' . $image_name;
$image_description = $title . " image";
$stmt_img->bind_param("iss", $tour_id, $image_url, $image_description);
$stmt_img->execute();
$stmt_img->close();

$conn->close();

// Redirect back to tours page with success message
$_SESSION['add_tour_success'] = "Tour added successfully!";
header("Location: Tours.php");
exit();
?>
