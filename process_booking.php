<?php
// Include session configuration
include 'session_config.php';

// Process Booking Submission
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
    $num_people = isset($_POST['num_people']) ? intval($_POST['num_people']) : 0;

    // Validate basic inputs
    if ($tour_id <= 0 || $num_people <= 0 || $num_people > 6) {
        $_SESSION['booking_error'] = "Invalid booking data. Please try again.";
        header("Location: BookTour.php?id=" . $tour_id);
        exit();
    }

    // Validate that we have data for all people
    $people_data = [];
    for ($i = 1; $i <= $num_people; $i++) {
        $name = trim($_POST["name_$i"] ?? '');
        $age = intval($_POST["age_$i"] ?? 0);
        $gender = $_POST["gender_$i"] ?? '';
        $health = trim($_POST["health_$i"] ?? '');

        if (empty($name) || $age <= 0 || $age > 120 || empty($gender)) {
            $_SESSION['booking_error'] = "Invalid data for Person $i. Please check all fields.";
            header("Location: BookTour.php?id=" . $tour_id);
            exit();
        }

        $people_data[] = [
            'name' => $name,
            'age' => $age,
            'gender' => $gender,
            'health_conditions' => $health
        ];
    }

    // Get tour price for calculation
    $tour_sql = "SELECT price FROM tours WHERE id = ?";
    $stmt_tour = $conn->prepare($tour_sql);
    $stmt_tour->bind_param("i", $tour_id);
    $stmt_tour->execute();
    $tour_result = $stmt_tour->get_result();

    if ($tour_result->num_rows === 0) {
        $_SESSION['booking_error'] = "Tour not found.";
        header("Location: Tours.php");
        exit();
    }

    $tour = $tour_result->fetch_assoc();
    $total_price = $tour['price'] * $num_people;
    $booking_date = date('Y-m-d H:i:s');

    // Store booking data in session for payment processing
    $_SESSION['pending_booking'] = [
        'user_id' => $user_id,
        'tour_id' => $tour_id,
        'num_people' => $num_people,
        'total_price' => $total_price,
        'people_data' => $people_data,
        'booking_date' => $booking_date
    ];

    $conn->close();

    // Redirect to payment page
    header("Location: Payment.php");
    exit();
} else {
    // Invalid request method
    header("Location: Tours.php");
    exit();
}
?>
