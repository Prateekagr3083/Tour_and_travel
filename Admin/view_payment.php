<?php
// View Payment Details Page - Access restricted to admin users only
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

// Get payment/booking ID from URL
$payment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($payment_id <= 0) {
    header("Location: Payments.php");
    exit();
}

// Include database connection
include '../Database/db_connect.php';

// Get payment/booking details with user and tour information
$sql = "SELECT b.id, b.booking_date, b.status, b.number_of_guests, b.total_price,
        u.first_name, u.last_name, u.email, u.contact_number,
        t.title as tour_name, t.price as tour_price, t.duration, t.description,
        t.location as destination_name, p.name as package_name
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN tours t ON b.tour_id = t.id
        LEFT JOIN destinations d ON t.destination_id = d.id
        LEFT JOIN tour_packages p ON t.package_id = p.id
        WHERE b.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: Payments.php");
    exit();
}

$payment = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Determine payment status
$payment_status = 'pending';
if ($payment['status'] === 'confirmed') {
    $payment_status = 'completed';
} elseif ($payment['status'] === 'cancelled') {
    $payment_status = 'failed';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View Payment - Admin Panel</title>
    <link rel="stylesheet" href="css/Admin.css" />
    <link rel="stylesheet" href="css/sidebar.css" />
    <link rel="stylesheet" href="css/payments.css" />
    <script src="js/sidebar.js"></script>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="welcome-message">
                    <h1>Payment Details</h1>
                    <p>Admin Panel - <?php echo date('F j, Y'); ?></p>
                </div>
                <a href="Payments.php" class="logout-btn">Back to Payments</a>
            </div>

            <!-- Payment Details -->
            <div class="content-section">
                <div class="payment-details">
                    <div class="details-header">
                        <h2>Transaction #<?php echo htmlspecialchars($payment['id']); ?></h2>
                        <span class="status-<?php echo $payment_status; ?> status-badge">
                            <?php echo ucfirst($payment_status); ?>
                        </span>
                    </div>

                    <div class="details-grid">
                        <div class="details-section">
                            <h3>Customer Information</h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Name:</label>
                                    <span><?php echo htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Email:</label>
                                    <span><?php echo htmlspecialchars($payment['email']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Contact:</label>
                                    <span><?php echo htmlspecialchars($payment['contact_number']); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="details-section">
                            <h3>Tour Information</h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Tour Name:</label>
                                    <span><?php echo htmlspecialchars($payment['tour_name']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Destination:</label>
                                    <span><?php echo htmlspecialchars($payment['destination_name']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Package:</label>
                                    <span><?php echo htmlspecialchars($payment['package_name']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Duration:</label>
                                    <span><?php echo htmlspecialchars($payment['duration']); ?> days</span>
                                </div>
                                <div class="info-item">
                                    <label>Description:</label>
                                    <span><?php echo htmlspecialchars($payment['description']); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="details-section">
                            <h3>Payment Information</h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Transaction ID:</label>
                                    <span>TXN<?php echo str_pad($payment['id'], 6, '0', STR_PAD_LEFT); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Payment Date:</label>
                                    <span><?php echo date('F j, Y H:i', strtotime($payment['booking_date'])); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Number of Guests:</label>
                                    <span><?php echo htmlspecialchars($payment['number_of_guests']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Tour Price:</label>
                                    <span>₹<?php echo number_format($payment['tour_price'], 2); ?> per person</span>
                                </div>
                                <div class="info-item">
                                    <label>Total Amount:</label>
                                    <span class="amount-highlight">₹<?php echo number_format($payment['total_price'], 2); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Payment Method:</label>
                                    <span>Online Payment</span>
                                </div>
                                <div class="info-item">
                                    <label>Status:</label>
                                    <span><?php echo ucfirst($payment_status); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="payment-actions">
                        <?php if ($payment_status === 'completed'): ?>
                            <button type="button" class="action-btn refund-btn" onclick="processRefund(<?php echo $payment['id']; ?>)">Process Refund</button>
                        <?php endif; ?>
                        <a href="Payments.php" class="action-btn back-btn">Back to Payments</a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    function processRefund(paymentId) {
        if (confirm('Are you sure you want to process a refund for this payment? This action cannot be undone.')) {
            alert('Refund functionality will be implemented soon.');
        }
    }
    </script>
</body>
</html>
