<?php
// Payment Processing Script
include '../session_config.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login.php");
    exit();
}

// Check if we have booking data
if (!isset($_SESSION['pending_booking'])) {
    header("Location: ../Tours.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $booking_data = $_SESSION['pending_booking'];

    $tour_id = $booking_data['tour_id'];
    $num_people = $booking_data['num_people'];
    $total_price = $booking_data['total_price'];
    $people_data = $booking_data['people_data'];
    $booking_date = $booking_data['booking_date'];

    // Get payment details from form
    $transaction_id = $_POST['transaction_id'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);

    // Validate payment data
    if (empty($transaction_id) || empty($payment_method) || $amount <= 0) {
        $_SESSION['payment_error'] = "Invalid payment data.";
        header("Location: index.php");
        exit();
    }

    // Include database connection
    include '../Database/db_connect.php';

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert booking
        $booking_sql = "INSERT INTO bookings (user_id, tour_id, booking_date, status, total_price, num_people) VALUES (?, ?, ?, 'confirmed', ?, ?)";
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

        // Insert payment record
        $payment_sql = "INSERT INTO payments (booking_id, transaction_id, amount, payment_method, status, payment_date) VALUES (?, ?, ?, ?, 'completed', NOW())";
        $stmt_payment = $conn->prepare($payment_sql);
        $stmt_payment->bind_param("isdss", $booking_id, $transaction_id, $amount, $payment_method);
        $stmt_payment->execute();

        // Commit transaction
        $conn->commit();

        // Clear pending booking data
        unset($_SESSION['pending_booking']);

        // Set success message
        $_SESSION['booking_success'] = "Payment successful! Your booking has been confirmed.";

        // Close statements
        $stmt_booking->close();
        $stmt_details->close();
        $stmt_payment->close();

        // Redirect to success page
        header("Location: success.php?booking_id=" . $booking_id);
        exit();

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['payment_error'] = "Payment failed. Please try again.";
        header("Location: index.php");
        exit();
    }

    $conn->close();
} else {
    // Invalid request method
    header("Location: ../Tours.php");
    exit();
}
?>
