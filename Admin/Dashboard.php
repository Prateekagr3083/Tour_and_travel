<?php
// Admin Dashboard - Access restricted to admin users only
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

// Get statistics for dashboard
$users_count = 0;
$bookings_count = 0;
$tours_count = 0;

// Count total users
$sql_users = "SELECT COUNT(*) as total_users FROM users WHERE role != 'admin'";
$result_users = $conn->query($sql_users);
if ($result_users) {
    $users_count = $result_users->fetch_assoc()['total_users'];
}

// Count total bookings (assuming bookings table exists)
$sql_bookings = "SELECT COUNT(*) as total_bookings FROM bookings";
$result_bookings = $conn->query($sql_bookings);
if ($result_bookings) {
    $bookings_count = $result_bookings->fetch_assoc()['total_bookings'];
}

// Count total tours (assuming tours table exists)
$sql_tours = "SELECT COUNT(*) as total_tours FROM tours";
$result_tours = $conn->query($sql_tours);
if ($result_tours) {
    $tours_count = $result_tours->fetch_assoc()['total_tours'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tour & Travel</title>
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
