<?php
// Admin Logout Script
session_start();

// Destroy admin session
session_unset();
session_destroy();

// Clear admin session cookies
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Clear any admin-specific cookies
setcookie('admin_session', '', time() - 3600, '/');

// Redirect to admin login page
header("Location: Login.php");
exit();
?>
