<?php
// heartbeat.php - Server-side heartbeat handler for session management
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle heartbeat request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['action']) && $input['action'] === 'heartbeat') {
        // Update session last activity
        if (isset($_SESSION['user_id'])) {
            $_SESSION['last_activity'] = time();
            echo json_encode(['success' => true, 'message' => 'Session updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No active session']);
        }
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
