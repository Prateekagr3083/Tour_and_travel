<?php
// Admin Bookings Page - Access restricted to admin users only
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

// Get all bookings from database with user and tour information
$bookings = [];
$sql = "SELECT b.*, u.name as user_name, u.email as user_email, t.name as tour_name, t.destination 
        FROM bookings b 
        JOIN users u ON b.user_id = u.id 
        JOIN tours t ON b.tour_id = t.id 
        ORDER BY b.booking_date DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Admin Panel</title>
    <link rel="stylesheet" href="css/Admin.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h2 {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        
        .sidebar-header p {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .nav-menu {
            list-style: none;
            padding: 20px 0;
        }
        
        .nav-item {
            padding: 12px 20px;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }
        
        .nav-item:hover {
            background: rgba(255,255,255,0.1);
            border-left-color: #fff;
        }
        
        .nav-item.active {
            background: rgba(255,255,255,0.2);
            border-left-color: #fff;
        }
        
        .nav-link {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .nav-link i {
            font-size: 1.1rem;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .welcome-message h1 {
            color: #333;
            margin-bottom: 5px;
        }
        
        .welcome-message p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .logout-btn:hover {
            background: #c82333;
        }
        
        .content-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .section-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .section-header h2 {
            color: #333;
            font-size: 1.3rem;
        }
        
        .bookings-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .bookings-table th,
        .bookings-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .bookings-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .bookings-table tr:hover {
            background: #f8f9fa;
        }
        
        .status-confirmed {
            color: #28a745;
            font-weight: 600;
        }
        
        .status-pending {
            color: #ffc107;
            font-weight: 600;
        }
        
        .status-cancelled {
            color: #dc3545;
            font-weight: 600;
        }
        
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
            text-decoration: none;
            display: inline-block;
            font-size: 0.8rem;
        }
        
        .view-btn {
            background: #007bff;
            color: white;
        }
        
        .view-btn:hover {
            background: #0056b3;
        }
        
        .confirm-btn {
            background: #28a745;
            color: white;
        }
        
        .confirm-btn:hover {
            background: #218838;
        }
        
        .cancel-btn {
            background: #dc3545;
            color: white;
        }
        
        .cancel-btn:hover {
            background: #c82333;
        }
        
        .no-bookings {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .no-bookings i {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
        }
        
        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .filter-form {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .filter-group label {
            font-size: 0.9rem;
            color: #666;
        }
        
        .filter-group select,
        .filter-group input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        
        .filter-btn {
            background: #007bff;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .filter-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p>Tour & Travel Management</p>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="Dashboard.php" class="nav-link">
                        <i>📊</i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i>👥</i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a href="Tours.php" class="nav-link">
                        <i>🏨</i> Tours
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="Bookings.php" class="nav-link">
                        <i>📋</i> Bookings
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i>⚙️</i> Settings
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="welcome-message">
                    <h1>Manage Bookings</h1>
                    <p>Admin Panel - <?php echo date('F j, Y'); ?></p>
                </div>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form class="filter-form">
                    <div class="filter-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="">All Status</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="date">Booking Date</label>
                        <input type="date" id="date" name="date">
                    </div>
                    <button type="submit" class="filter-btn">Apply Filters</button>
                </form>
            </div>

            <!-- Bookings Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>All Bookings</h2>
                </div>

                <?php if (!empty($bookings)): ?>
                    <table class="bookings-table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>User</th>
                                <th>Tour</th>
                                <th>Destination</th>
                                <th>Guests</th>
                                <th>Total Price</th>
                                <th>Booking Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                    <td>
                                        <div><?php echo htmlspecialchars($booking['user_name']); ?></div>
                                        <small><?php echo htmlspecialchars($booking['user_email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($booking['tour_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['destination']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['guests']); ?></td>
                                    <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></td>
                                    <td>
                                        <span class="status-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="action-btn view-btn">View</a>
                                        <?php if ($booking['status'] === 'pending'): ?>
                                            <a href="#" class="action-btn confirm-btn">Confirm</a>
                                            <a href="#" class="action-btn cancel-btn">Cancel</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-bookings">
                        <i>📋</i>
                        <h3>No Bookings Found</h3>
                        <p>There are no bookings in the system yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
