<?php
// Test file to debug session behavior
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>Session Debug Info:</h2>";
echo "<p>Session Status: " . session_status() . "</p>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session Variables: ";
print_r($_SESSION);
echo "</p>";

if (isset($_SESSION['user_id'])) {
    echo "<p>User ID is set: " . $_SESSION['user_id'] . "</p>";
} else {
    echo "<p>User ID is NOT set</p>";
}

echo '<p><a href="Login.php">Go to Login</a></p>';
echo '<p><a href="Home.php">Go to Home</a></p>';
?>
