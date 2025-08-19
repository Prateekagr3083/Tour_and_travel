<?php
// logout.php - Server-side logout handler
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle logout request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get request data
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Perform logout
    session_unset();
    session_destroy();
    
    // Clear session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Additional cleanup
    setcookie('PHPSESSID', '', time() - 3600, '/');
    
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
