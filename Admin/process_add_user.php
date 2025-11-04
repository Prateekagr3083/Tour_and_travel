<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

include '../Database/db_connect.php';

// Validate and sanitize inputs
$first_name = trim($_POST['first_name']);
$last_name = trim($_POST['last_name']);
$email = trim($_POST['email']);
$contact_number = trim($_POST['contact_number']);
$password = $_POST['password'];
$gender = $_POST['gender'];

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
if (empty($password) || strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters long.";
}
if (empty($gender)) {
    $errors[] = "Gender is required.";
}

// Check if email already exists
$check_email = "SELECT id FROM users WHERE email = ?";
$stmt_check = $conn->prepare($check_email);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $errors[] = "Email already exists.";
}
$stmt_check->close();

if (!empty($errors)) {
    $_SESSION['user_error'] = implode('<br>', $errors);
    header("Location: Users.php");
    exit();
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$sql = "INSERT INTO users (first_name, last_name, email, contact_number, password_hash, gender) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $first_name, $last_name, $email, $contact_number, $hashed_password, $gender);

if ($stmt->execute()) {
    $_SESSION['user_success'] = "User added successfully!";
} else {
    $_SESSION['user_error'] = "Failed to add user: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: Users.php");
exit();
?>
