<?php
// Admin Payments Page - Access restricted to admin users only
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

// Include database connection
include '../Database/db_connect.php';

// Get admin information
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];
$admin_email = $_SESSION['admin_email'];

$payments = [];
$total_payments = 0;
$completed_payments = 0;
$pending_payments = 0;
$failed_payments = 0;

// Since no payments table exists, we will derive payments from bookings table as a proxy
// Assuming bookings table has: id, user_id, tour_id, booking_date, status, number_of_guests, total_price
// We'll treat bookings with status 'confirmed' as completed payments, others as pending or failed

$sql = "SELECT b.id, b.user_id, b.tour_id, b.booking_date, b.status, b.number_of_guests, b.total_price,
        u.first_name, u.last_name, u.email,
        t.title as tour_name
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.id
        LEFT JOIN tours t ON b.tour_id = t.id
        ORDER BY b.booking_date DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $payment_status = 'pending';
        $amount = $row['total_price'];

        if ($row['status'] === 'confirmed') {
            $payment_status = 'completed';
            $completed_payments += $amount;
        } elseif ($row['status'] === 'cancelled') {
            $payment_status = 'failed';
            $failed_payments += $amount;
        } else {
            $pending_payments += $amount;
        }

        $payments[] = [
            'id' => $row['id'],
            'user_id' => $row['user_id'],
            'user_name' => $row['first_name'] . ' ' . $row['last_name'],
            'user_email' => $row['email'],
            'amount' => $amount,
            'currency' => 'INR',
            'status' => $payment_status,
            'payment_method' => 'Online Payment',
            'transaction_id' => 'TXN' . str_pad($row['id'], 6, '0', STR_PAD_LEFT),
            'created_at' => $row['booking_date'],
            'tour_name' => $row['tour_name'],
            'guests' => $row['number_of_guests']
        ];

        $total_payments += $amount;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments - Admin Panel</title>
    <link rel="stylesheet" href="css/Admin.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/payments.css">
    <script src="js/sidebar.js"></script>
    <script src="js/payments.js"></script>
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
                    <h1>Manage Payments</h1>
                    <p>Admin Panel - <?php echo date('F j, Y'); ?></p>
                    <?php
                    if (isset($_SESSION['payment_success'])) {
                        echo '<div class="success-message" style="color: green; margin-top: 10px;">' . htmlspecialchars($_SESSION['payment_success']) . '</div>';
                        unset($_SESSION['payment_success']);
                    }
                    if (isset($_SESSION['payment_error'])) {
                        echo '<div class="error-message" style="color: red; margin-top: 10px;">' . htmlspecialchars($_SESSION['payment_error']) . '</div>';
                        unset($_SESSION['payment_error']);
                    }
                    ?>
                </div>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>

            <!-- Payment Summary -->
            <div class="payment-summary">
                <div class="summary-card">
                    <div class="summary-amount">₹<?php echo number_format($total_payments, 2); ?></div>
                    <div class="summary-label">Total Revenue</div>
                </div>
                <div class="summary-card">
                    <div class="summary-amount amount-positive">₹<?php echo number_format($completed_payments, 2); ?></div>
                    <div class="summary-label">Completed</div>
                </div>
                <div class="summary-card">
                    <div class="summary-amount">₹<?php echo number_format($pending_payments, 2); ?></div>
                    <div class="summary-label">Pending</div>
                </div>
                <div class="summary-card">
                    <div class="summary-amount amount-negative">₹<?php echo number_format($failed_payments, 2); ?></div>
                    <div class="summary-label">Failed</div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form class="filter-form">
                    <div class="filter-group">
                        <label for="status-filter">Status</label>
                        <select id="status-filter" name="status">
                            <option value="">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="date-from">From Date</label>
                        <input type="date" id="date-from" name="date_from">
                    </div>
                    <div class="filter-group">
                        <label for="date-to">To Date</label>
                        <input type="date" id="date-to" name="date_to">
                    </div>
                    <button type="submit" class="filter-btn">Filter</button>
                </form>
            </div>

            <!-- Payments Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>All Transactions</h2>
                    <button type="button" class="action-btn edit-btn" onclick="exportReport()">Export Report</button>
                </div>

                <?php if (!empty($payments)): ?>
                    <table class="payments-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Tour</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment Method</th>
                                <th>Transaction ID</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($payment['id']); ?></td>
                                    <td>
                                        <div><?php echo htmlspecialchars($payment['user_name']); ?></div>
                                        <small><?php echo htmlspecialchars($payment['user_email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($payment['tour_name']); ?></td>
                                    <td>₹<?php echo number_format($payment['amount'], 2); ?></td>
                                    <td>
                                        <span class="status-<?php echo htmlspecialchars($payment['status']); ?>">
                                            <?php echo ucfirst(htmlspecialchars($payment['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['transaction_id']); ?></td>
                                    <td><?php echo date('M j, Y H:i', strtotime($payment['created_at'])); ?></td>
                                    <td>
                                        <a href="view_payment.php?id=<?php echo $payment['id']; ?>" class="payment-action-btn view-btn">View</a>
                                        <?php if ($payment['status'] === 'completed'): ?>
                                            <a href="#" class="payment-action-btn refund-btn" onclick="processRefund(<?php echo $payment['id']; ?>)">Refund</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-payments">
                        <i>💳</i>
                        <h3>No Payments Found</h3>
                        <p>There are no payment transactions in the system yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
    function exportReport() {
        alert('Export functionality will be implemented soon.');
    }

    function processRefund(paymentId) {
        if (confirm('Are you sure you want to process a refund for this payment?')) {
            alert('Refund functionality will be implemented soon.');
        }
    }
    </script>
</body>
</html>
