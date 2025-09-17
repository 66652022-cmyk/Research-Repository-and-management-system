<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();

// Set JSON header immediately
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || $_SESSION['user_role'] !== 'super_admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Include files with error checking
if (!file_exists('database.php')) {
    echo json_encode(['success' => false, 'message' => 'Database class not found']);
    exit();
}

if (!file_exists('UnifiedAuth.php')) {
    echo json_encode(['success' => false, 'message' => 'UnifiedAuth class not found']);
    exit();
}

require_once '../config/database.php';
require_once 'UnifiedAuth.php';

try {
    // Get and decode input
    $rawInput = file_get_contents('php://input');
    
    if (empty($rawInput)) {
        echo json_encode(['success' => false, 'message' => 'No data received']);
        exit();
    }
    
    $input = json_decode($rawInput, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit();
    }
    
    // Validate required fields
    if (empty($input['name']) || empty($input['email']) || empty($input['role'])) {
        echo json_encode(['success' => false, 'message' => 'Name, email, and role are required']);
        exit();
    }
    
    // Validate email
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit();
    }
    
    // Create UnifiedAuth instance
    $auth = new UnifiedAuth();
    
    if (!$auth) {
        echo json_encode(['success' => false, 'message' => 'Failed to initialize authentication']);
        exit();
    }
    
    // Create user
    $result = $auth->createUser($input);
    
    // Make sure we have a valid result
    if (!is_array($result)) {
        echo json_encode(['success' => false, 'message' => 'Invalid response from user creation']);
        exit();
    }
    
    // Return the result
    echo json_encode($result);
    
} catch (Exception $e) {
    // Log error but don't expose it to user
    error_log("Add user error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    echo json_encode(['success' => false, 'message' => 'Server error occurred: ' . $e->getMessage()]);
} catch (Error $e) {
    error_log("PHP Error in add user: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    echo json_encode(['success' => false, 'message' => 'PHP Error occurred: ' . $e->getMessage()]);
}
?>