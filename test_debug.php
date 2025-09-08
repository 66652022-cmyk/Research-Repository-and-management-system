<?php
// Create this as: ../classes/test_debug.php
// Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    echo json_encode([
        'success' => true,
        'message' => 'Basic PHP test successful',
        'php_version' => PHP_VERSION,
        'post_data' => $_POST,
        'input_data' => file_get_contents("php://input"),
        'request_method' => $_SERVER['REQUEST_METHOD']
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'line' => $e->getLine()
    ]);
}
?>