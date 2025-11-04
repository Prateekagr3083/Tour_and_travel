<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

include '../Database/db_connect.php';

$user_id = intval($_POST['user_id']);
$first_name = trim($_POST['first_name']);
$last_name = trim($_POST['last_name']);
$email = trim($_POST['email']);
$contact_number = trim($_POST['contact_number']);
$gender = $_POST['gender'];
$new_password = trim($_POST['new_password']);

// Basic validation
$errors = [];

if (empty($first_name)) {
    $errors[] = "First name is required.";
}
if (empty($last_name)) {
    $errors[] = "Last name is required.";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Valid email is required.";
}
if (empty($contact_number)) {
    $errors[] = "Contact number is required.";
}
if (empty($gender)) {
    $errors[] = "Gender is required.";
}

// Check if email already exists (excluding current user)
$check_email = "SELECT id FROM users WHERE email = ? AND id != ?";
$stmt_check = $conn->prepare($check_email);
$stmt_check->bind_param("si", $email, $user_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $errors[] = "Email already exists.";
}
$stmt_check->close();

if (!empty($errors)) {
    $_SESSION['user_error'] = implode('<br>', $errors);
    header("Location: edit_user.php?id=" . $user_id);
    exit();
}

// Update user
if (!empty($new_password)) {
    // Update with new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, contact_number = ?, gender = ?, password_hash = ? WHERE id = ? AND role != 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $contact_number, $gender, $hashed_password, $user_id);
} else {
    // Update without changing password
    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, contact_number = ?, gender = ? WHERE id = ? AND role != 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $first_name, $last_name, $email, $contact_number, $gender, $user_id);
}

if ($stmt->execute()) {
    $_SESSION['user_success'] = "User updated successfully!";
} else {
    $_SESSION['user_error'] = "Failed to update user: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: Users.php");
exit();
?>
