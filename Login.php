<?php
// Include database connection
include 'Database/db_connect.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Check if user exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $row['password_hash'])) {
            // Start session and redirect to dashboard or home
            session_start();
            $_SESSION['user_id'] = $row['id'];
            // Start session and set user_id
            session_start();
            $_SESSION['user_id'] = $row['id'];
            header("Location: Home.php");
            exit();
        } else {
            echo "<script>alert('Invalid password!'); window.location.href='Login.php';</script>";
        }
    } else {
        echo "<script>alert('No user found with that email!'); window.location.href='Login.php';</script>";
    }

    $conn->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="CSS/Login.css">
    <link rel="stylesheet" href="CSS/Nave.css">
</head>
<body>
    <?php include 'Navbar/Nave.php'; ?>
    
    <main>
        <form class="form" method="POST" action="Login.php">
            <p class="title">Login</p>
            
            <label>
                <input required="" placeholder=" " type="email" name="email" class="input">
                <span>Email</span>
            </label>
            <label>
                <input required="" placeholder=" " type="password" name="password" class="input" id="password">
                <span>Password</span>
            </label>
            <button class="submit">Login</button>
            <p class="signin">Don't have an account? <a href="Register.php">Register</a></p>
        </form>
    </main>
</body>
</html>
