// Include session configuration
include 'session_config.php';

// Process Profile Update
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

// Include database connection
include 'Database/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $contact_number = trim($_POST['contact_number']);
    $gender = $_POST['gender'];

    // Validate inputs
    if (empty($first_name) || empty($last_name) || empty($contact_number) || empty($gender)) {
        $_SESSION['profile_error'] = "All fields are required.";
        header("Location: UserProfile.php");
        exit();
    }

    // Update user profile
    $sql = "UPDATE users SET first_name = ?, last_name = ?, contact_number = ?, gender = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $first_name, $last_name, $contact_number, $gender, $user_id);

    if ($stmt->execute()) {
        // Update session variables
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['user_name'] = $first_name . ' ' . $last_name;

        $_SESSION['profile_success'] = "Profile updated successfully!";
    } else {
        $_SESSION['profile_error'] = "Failed to update profile. Please try again.";
    }

    $stmt->close();
    $conn->close();

    header("Location: UserProfile.php");
    exit();
} else {
    header("Location: UserProfile.php");
    exit();
}
?>
