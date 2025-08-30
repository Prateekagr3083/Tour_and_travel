<?php
// Admin Login Page
include '../Database/db_connect.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Check if user exists and is admin
    $sql = "SELECT * FROM users WHERE email = '$email' AND role = 'admin'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $row['password_hash'])) {
            // Start admin session
            session_start();
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['first_name'] . ' ' . $row['last_name'];
            $_SESSION['admin_email'] = $row['email'];
            $_SESSION['admin_role'] = 'admin';
            
            header("Location: Dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No admin user found with that email!";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Tour & Travel</title>
    <link rel="stylesheet" href="css/Admin.css">
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-header">
            <h1>Admin Login</h1>
            <p>Access the tour and travel management system</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="Login.php">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="admin@example.com">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
            
            <button type="submit" class="btn-admin-login">Login to Admin Panel</button>
        </form>
        
        <div class="back-to-home">
            <a href="../Home.php">← Back to Home Page</a>
        </div>
    </div>
</body>
</html>
