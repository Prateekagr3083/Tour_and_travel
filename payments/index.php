<?php
// Payment Gateway Integration Page
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

$booking_data = $_SESSION['pending_booking'];
$tour_id = $booking_data['tour_id'];
$num_people = $booking_data['num_people'];
$total_price = $booking_data['total_price'];
$people_data = $booking_data['people_data'];

// Include database connection
include '../Database/db_connect.php';

// Get tour details
$sql_tour = "SELECT title, location, price, duration FROM tours WHERE id = ?";
$stmt = $conn->prepare($sql_tour);
$stmt->bind_param("i", $tour_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../Tours.php");
    exit();
}

$tour = $result->fetch_assoc();
$conn->close();

// Generate unique transaction ID
$transaction_id = 'TXN' . time() . rand(1000, 9999);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway - Tour and Travel</title>
    <link rel="stylesheet" href="../CSS/Home.css">
    <link rel="stylesheet" href="../CSS/Nave.css">
    <link rel="stylesheet" href="../CSS/Payment.css">
</head>
<body>
    <!-- Include Navbar -->
    <?php include '../Navbar/Nave.php'; ?>

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

            <!-- Payment Gateway Integration -->
            <div class="payment-gateway">
                <h3>Payment Gateway</h3>
                <p>Please complete your payment using one of the available payment methods below.</p>

                <!-- Payment Options -->
                <div class="payment-options">
                    <div class="payment-option">
                        <input type="radio" id="razorpay" name="payment_gateway" value="razorpay" checked>
                        <label for="razorpay">
                            <img src="https://cdn.razorpay.com/static/assets/logo/payment.svg" alt="Razorpay" style="height: 30px;">
                            Razorpay (Credit/Debit Card, UPI, Net Banking)
                        </label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" id="payu" name="payment_gateway" value="payu">
                        <label for="payu">
                            PayU Money (Multiple Payment Options)
                        </label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" id="stripe" name="payment_gateway" value="stripe">
                        <label for="stripe">
                            Stripe (International Cards)
                        </label>
                    </div>
                </div>

                <!-- Terms Agreement -->
                <div class="form-group" style="margin-top: 1rem;">
                    <label>
                        <input type="checkbox" name="terms" id="terms" required>
                        I agree to the <a href="../Terms.php" target="_blank">Terms and Conditions</a>
                    </label>
                </div>

                <!-- Pay Now Button -->
                <button type="button" class="book-btn" id="pay-now-btn" onclick="initiatePayment()">
                    Pay ₹<?php echo number_format($total_price, 2); ?>
                </button>
            </div>

            <div style="text-align: center; margin-top: 1rem;">
                <a href="../BookTour.php?id=<?php echo $tour_id; ?>" style="color: #04543a;">← Back to Booking</a>
            </div>
        </div>
    </main>

    <!-- Payment Gateway Scripts -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        // Payment configuration
        const paymentConfig = {
            amount: <?php echo $total_price * 100; ?>, // Amount in paisa (multiply by 100)
            currency: 'INR',
            transactionId: '<?php echo $transaction_id; ?>',
            tourId: <?php echo $tour_id; ?>,
            numPeople: <?php echo $num_people; ?>,
            totalPrice: <?php echo $total_price; ?>
        };

        function initiatePayment() {
            const selectedGateway = document.querySelector('input[name="payment_gateway"]:checked').value;
            const termsAccepted = document.getElementById('terms').checked;

            if (!termsAccepted) {
                alert('Please accept the terms and conditions to proceed with payment.');
                return;
            }

            switch(selectedGateway) {
                case 'razorpay':
                    initiateRazorpayPayment();
                    break;
                case 'payu':
                    initiatePayUPayment();
                    break;
                case 'stripe':
                    initiateStripePayment();
                    break;
                default:
                    alert('Please select a payment method.');
            }
        }

        function initiateRazorpayPayment() {
            const options = {
                key: 'YOUR_RAZORPAY_KEY_ID', // Replace with your Razorpay Key ID
                amount: paymentConfig.amount,
                currency: paymentConfig.currency,
                name: 'Tour and Travel',
                description: 'Tour Booking Payment',
                order_id: paymentConfig.transactionId,
                handler: function (response) {
                    // Payment successful
                    processPaymentSuccess(response, 'razorpay');
                },
                prefill: {
                    name: '<?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>',
                    email: '<?php echo htmlspecialchars($_SESSION['user_email']); ?>',
                    contact: '<?php echo htmlspecialchars($_SESSION['user_contact'] ?? ''); ?>'
                },
                theme: {
                    color: '#04543a'
                }
            };

            const rzp = new Razorpay(options);
            rzp.open();
        }

        function initiatePayUPayment() {
            // PayU integration would go here
            alert('PayU integration coming soon. Please use Razorpay for now.');
        }

        function initiateStripePayment() {
            // Stripe integration would go here
            alert('Stripe integration coming soon. Please use Razorpay for now.');
        }

        function processPaymentSuccess(response, gateway) {
            // Create form to submit payment details
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'process_payment.php';

            // Add payment data
            const fields = {
                transaction_id: response.razorpay_payment_id || response.transaction_id,
                payment_method: gateway,
                amount: paymentConfig.totalPrice,
                gateway_response: JSON.stringify(response)
            };

            for (const [key, value] of Object.entries(fields)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>
