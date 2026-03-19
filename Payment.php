<?php
// Include session configuration
include 'session_config.php';

session_start();

// Payment Page

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

// Check if we have booking data from the booking process
if (!isset($_SESSION['pending_booking'])) {
    header("Location: Tours.php");
    exit();
}

$booking_data = $_SESSION['pending_booking'];
$tour_id = $booking_data['tour_id'];
$num_people = $booking_data['num_people'];
$total_price = $booking_data['total_price'];
$people_data = $booking_data['people_data'];

// Include database connection
include 'Database/db_connect.php';

// Get tour details
$sql_tour = "SELECT title, location, price, duration FROM tours WHERE id = ?";
$stmt = $conn->prepare($sql_tour);
$stmt->bind_param("i", $tour_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: Tours.php");
    exit();
}

$tour = $result->fetch_assoc();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Tour and Travel</title>
    <link rel="stylesheet" href="CSS/Home.css">
    <link rel="stylesheet" href="CSS/Nave.css">
    <link rel="stylesheet" href="CSS/Payment.css">
    <script src="Scripts/BookTour.js"></script>
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'Navbar/Nave.php'; ?>

    <main>
        <div class="booking-form" style="max-width: 600px; margin: 2rem auto;">
            <h2>Complete Your Payment</h2>

            <!-- Booking Summary -->
            <div class="booking-summary">
                <h3>Booking Summary</h3>
                <div class="summary-item">
                    <span>Tour:</span>
                    <span><?php echo htmlspecialchars($tour['title']); ?></span>
                </div>
                <div class="summary-item">
                    <span>Location:</span>
                    <span><?php echo htmlspecialchars($tour['location']); ?></span>
                </div>
                <div class="summary-item">
                    <span>Duration:</span>
                    <span><?php echo htmlspecialchars($tour['duration']); ?> days</span>
                </div>
                <div class="summary-item">
                    <span>Number of People:</span>
                    <span><?php echo htmlspecialchars($num_people); ?></span>
                </div>
                <div class="summary-item">
                    <span>Price per person:</span>
                    <span>₹<?php echo number_format($tour['price'], 2); ?></span>
                </div>
                <div class="summary-item total-price">
                    <span>Total Amount:</span>
                    <span>₹<?php echo number_format($total_price, 2); ?></span>
                </div>
            </div>

            <!-- Payment Form -->
            <form method="POST" action="process_payment.php" id="payment-form">
                <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
                <input type="hidden" name="num_people" value="<?php echo $num_people; ?>">
                <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">

                <div class="form-group">
                    <label for="payment_method">Payment Method:</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="">Select Payment Method</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="net_banking">Net Banking</option>
                        <option value="upi">UPI</option>
                        <option value="wallet">Digital Wallet</option>
                    </select>
                </div>

                <!-- Card Details (shown when card payment is selected) -->
                <div id="card-details" style="display: none;">
                    <div class="form-group">
                        <label for="card_number">Card Number:</label>
                        <input type="text" name="card_number" id="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                    </div>
                    <div class="form-group">
                        <label for="expiry_date">Expiry Date (MM/YY):</label>
                        <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YY" maxlength="5">
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV:</label>
                        <input type="text" name="cvv" id="cvv" placeholder="123" maxlength="4">
                    </div>
                    <div class="form-group">
                        <label for="card_holder">Card Holder Name:</label>
                        <input type="text" name="card_holder" id="card_holder" placeholder="John Doe">
                    </div>
                </div>

                <!-- UPI Details (shown when UPI is selected) -->
                <div id="upi-details" style="display: none;">
                    <div class="form-group">
                        <label for="upi_id">UPI ID:</label>
                        <input type="text" name="upi_id" id="upi_id" placeholder="yourname@upi">
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="terms" id="terms" required>
                        I agree to the <a href="Terms.php" target="_blank">Terms and Conditions</a>
                    </label>
                </div>

                <button type="submit" class="book-btn">Pay ₹<?php echo number_format($total_price, 2); ?></button>
            </form>

            <div style="text-align: center; margin-top: 1rem;">
                <a href="BookTour.php?id=<?php echo $tour_id; ?>" style="color: #04543a;">← Back to Booking</a>
            </div>
        </div>
    </main>

    <script>
        // Show/hide payment method specific fields
        document.getElementById('payment_method').addEventListener('change', function() {
            const method = this.value;
            const cardDetails = document.getElementById('card-details');
            const upiDetails = document.getElementById('upi-details');

            cardDetails.style.display = (method === 'credit_card' || method === 'debit_card') ? 'block' : 'none';
            upiDetails.style.display = (method === 'upi') ? 'block' : 'none';
        });

        // Format card number
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || '';
            e.target.value = formattedValue;
        });

        // Format expiry date
        document.getElementById('expiry_date').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
