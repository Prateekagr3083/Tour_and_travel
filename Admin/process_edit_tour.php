<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

include '../Database/db_connect.php';

// Validate and sanitize inputs
$tour_id = isset($_POST['tour_id']) ? intval($_POST['tour_id']) : 0;
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
$duration = isset($_POST['duration']) ? intval($_POST['duration']) : 0;

// Basic validation
$errors = [];

if ($tour_id <= 0) {
    $errors[] = "Invalid tour ID.";
}
if ($title === '') {
    $errors[] = "Tour name is required.";
}
if ($description === '') {
    $errors[] = "Description is required.";
}
if ($location === '') {
    $errors[] = "Location is required.";
}
if ($price <= 0) {
    $errors[] = "Price must be greater than zero.";
}
if ($duration <= 0) {
    $errors[] = "Duration must be at least 1 day.";
}

if (!empty($errors)) {
    $_SESSION['edit_tour_errors'] = $errors;
    header("Location: edit_tour.php?id=" . $tour_id);
    exit();
}

// Update tour in database
$stmt = $conn->prepare("UPDATE tours SET title = ?, description = ?, price = ?, location = ?, duration = ? WHERE id = ?");
$stmt->bind_param("ssdssi", $title, $description, $price, $location, $duration, $tour_id);

if (!$stmt->execute()) {
    $_SESSION['edit_tour_errors'] = ["Database error: " . $stmt->error];
    $stmt->close();
    $conn->close();
    header("Location: edit_tour.php?id=" . $tour_id);
    exit();
}

$stmt->close();

// Handle new image upload if provided
if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../project image/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $image_name = basename($_FILES['new_image']['name']);
    $target_file = $upload_dir . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate image file type
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($imageFileType, $allowed_types)) {
        if (move_uploaded_file($_FILES['new_image']['tmp_name'], $target_file)) {
            // Insert new image record
            $stmt_img = $conn->prepare("INSERT INTO tour_images (tour_id, image_url, description) VALUES (?, ?, ?)");
            $image_url = 'project image/' . $image_name;
            $image_description = $title . " image";
            $stmt_img->bind_param("iss", $tour_id, $image_url, $image_description);
            $stmt_img->execute();
            $stmt_img->close();
        }
    }
}

$conn->close();

// Redirect back to tours page with success message
$_SESSION['edit_tour_success'] = "Tour updated successfully!";
header("Location: Tours.php");
exit();
?>
