<?php
// Include database connection
include 'Database/db_connect.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    
    // Validate passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='Register.php';</script>";
        exit();
    }
    
    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($check_email);
    
    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location.href='Register.php';</script>";
        exit();
    }
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $sql = "INSERT INTO users (firstname, lastname, contact, email, password, gender) 
            VALUES ('$firstname', '$lastname', '$contact', '$email', '$hashed_password', '$gender')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registration successful!'); window.location.href='Login.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.location.href='Register.php';</script>";
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
    <title>Register</title>
    <link rel="stylesheet" href="CSS/Login.css">
    <link rel="stylesheet" href="CSS/Nave.css">
</head>
<body>
    <?php include 'Navbar/Nave.php'; ?>
    
    <main>
        <form class="form" method="POST" action="Register.php" onsubmit="return validatePasswords()">
            <p class="title">Register</p>
            
            <label>
                <input required="" placeholder=" " type="text" name="firstname" class="input">
                <span>Firstname</span>
            </label>
            <label>
                <input required="" placeholder=" " type="text" name="lastname" class="input">
                <span>Lastname</span>
            </label>
            <label>
              <input 
                type="tel" 
                name="contact"
                class="input" 
                required 
                placeholder=" " 
                pattern="[0-9]*" 
                inputmode="numeric" 
                oninput="this.value=this.value.replace(/[^0-9]/g,'')"
              >
              <span>Contact Number</span>
            </label>
            <label>
                <input required="" placeholder=" " type="email" name="email" class="input">
                <span>Email</span>
            </label>
            <label>
                <input required="" placeholder=" " type="password" name="password" class="input" id="password">
                <span>Password</span>
            </label>
            <label>
                <input required="" placeholder=" " type="password" name="confirm_password" class="input" id="confirmPassword">
                <span>Confirm Password</span>
            </label>
            <div class="gender-selection">
                <span class="gender-title">Gender</span>
                <label>
                    <input type="radio" name="gender" value="male" required=""> Male
                </label>
                <label>
                    <input type="radio" name="gender" value="female"> Female
                </label>
                <label>
                    <input type="radio" name="gender" value="other"> Others
                </label>
            </div>
            <div class="terms-conditions">
                <label>
                    <input type="checkbox" name="terms" required=""> I accept the <a href="#">Terms & Conditions</a>
                </label>
            </div>
            <button type="submit" class="submit">Register</button>
            <p class="signin">Already have an account? <a href="Login.php">Login</a></p>
        </form>
    </main>
    <script>
        function validatePasswords() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
</body>
</html>
