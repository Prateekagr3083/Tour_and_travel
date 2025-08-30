<?php
// Admin Tours Page - Access restricted to admin users only
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

// Get all tours from database
$tours = [];
$sql = "SELECT * FROM tours ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tours[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tours - Admin Panel</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .section-header h2 {
            color: #333;
            font-size: 1.3rem;
        }
        
        .add-btn {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .add-btn:hover {
            background: #218838;
        }
        
        .tours-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .tours-table th,
        .tours-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .tours-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .tours-table tr:hover {
            background: #f8f9fa;
        }
        
        .status-active {
            color: #28a745;
            font-weight: 600;
        }
        
        .status-inactive {
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
        
        .edit-btn {
            background: #ffc107;
            color: #333;
        }
        
        .edit-btn:hover {
            background: #e0a800;
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
        }
        
        .delete-btn:hover {
            background: #c82333;
        }
        
        .no-tours {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .no-tours i {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
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
                <li class="nav-item active">
                    <a href="Tours.php" class="nav-link">
                        <i>🏨</i> Tours
                    </a>
                </li>
                <li class="nav-item">
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
                    <h1>Manage Tours</h1>
                    <p>Admin Panel - <?php echo date('F j, Y'); ?></p>
                </div>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>

            <!-- Tours Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>All Tours</h2>
                    <a href="#" class="add-btn">Add New Tour</a>
                </div>

                <?php if (!empty($tours)): ?>
                    <table class="tours-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tour Name</th>
                                <th>Destination</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tours as $tour): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($tour['id']); ?></td>
                                    <td><?php echo htmlspecialchars($tour['name']); ?></td>
                                    <td><?php echo htmlspecialchars($tour['destination']); ?></td>
                                    <td>$<?php echo number_format($tour['price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($tour['duration']); ?> days</td>
                                    <td>
                                        <span class="<?php echo $tour['status'] === 'active' ? 'status-active' : 'status-inactive'; ?>">
                                            <?php echo ucfirst($tour['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($tour['created_at'])); ?></td>
                                    <td>
                                        <a href="#" class="action-btn edit-btn">Edit</a>
                                        <a href="#" class="action-btn delete-btn">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-tours">
                        <i>🏨</i>
                        <h3>No Tours Found</h3>
                        <p>There are no tours available in the system.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
