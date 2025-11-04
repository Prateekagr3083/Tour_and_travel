// Include session configuration
include 'session_config.php';

// Process Review Submission
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
    $tour_id = isset($_POST['tour_id']) ? intval($_POST['tour_id']) : 0;
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $review_comment = trim($_POST['review_comment']);
    $review_date = date('Y-m-d H:i:s');

    // Validate inputs
    if ($tour_id <= 0 || $rating < 1 || $rating > 5 || empty($review_comment)) {
        $_SESSION['review_error'] = "Invalid input data. Please try again.";
        header("Location: TourDetails.php?id=" . $tour_id);
        exit();
    }

    // Check if user has already reviewed this tour
    $check_sql = "SELECT id FROM user_interactions WHERE user_id = ? AND tour_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $tour_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Update existing review
        $update_sql = "UPDATE user_interactions SET review_rating = ?, review_comment = ?, review_date = ?, status = 'pending' WHERE user_id = ? AND tour_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("issii", $rating, $review_comment, $review_date, $user_id, $tour_id);

        if ($update_stmt->execute()) {
            $_SESSION['review_success'] = "Your review has been updated and is pending approval.";
        } else {
            $_SESSION['review_error'] = "Failed to update review. Please try again.";
        }
        $update_stmt->close();
    } else {
        // Insert new review
        $insert_sql = "INSERT INTO user_interactions (user_id, tour_id, review_rating, review_comment, review_date, status) VALUES (?, ?, ?, ?, ?, 'pending')";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iiiss", $user_id, $tour_id, $rating, $review_comment, $review_date);

        if ($insert_stmt->execute()) {
            $_SESSION['review_success'] = "Your review has been submitted and is pending approval.";
        } else {
            $_SESSION['review_error'] = "Failed to submit review. Please try again.";
        }
        $insert_stmt->close();
    }

    $check_stmt->close();
    $conn->close();

    // Redirect back to tour details
    header("Location: TourDetails.php?id=" . $tour_id);
    exit();
} else {
    // Invalid request method
    header("Location: Tours.php");
    exit();
}
?>
