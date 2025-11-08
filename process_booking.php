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

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert booking
        $booking_sql = "INSERT INTO bookings (user_id, tour_id, booking_date, status, total_price, num_people) VALUES (?, ?, ?, 'pending', ?, ?)";
        $stmt_booking = $conn->prepare($booking_sql);
        $stmt_booking->bind_param("iisdii", $user_id, $tour_id, $booking_date, $total_price, $num_people);
        $stmt_booking->execute();
        $booking_id = $conn->insert_id;

        // Insert booking details for each person
        $details_sql = "INSERT INTO booking_details (booking_id, person_name, age, gender, health_conditions) VALUES (?, ?, ?, ?, ?)";
        $stmt_details = $conn->prepare($details_sql);

        foreach ($people_data as $person) {
            $stmt_details->bind_param("isiss", $booking_id, $person['name'], $person['age'], $person['gender'], $person['health_conditions']);
            $stmt_details->execute();
        }

        // Commit transaction
        $conn->commit();

        $_SESSION['booking_success'] = "Your booking has been submitted successfully! It will be confirmed after admin approval.";

        // Close statements
        $stmt_booking->close();
        $stmt_details->close();
        $stmt_tour->close();

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['booking_error'] = "Failed to process booking. Please try again.";
    }

    $conn->close();

    // Redirect back to tour details or bookings page
    header("Location: MyBookings.php");
    exit();
} else {
    // Invalid request method
    header("Location: Tours.php");
    exit();
}
?>
