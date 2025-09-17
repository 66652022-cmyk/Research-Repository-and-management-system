<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$db = new Database();
$dbConn = $db->connect();

$name = $_POST['studentName'] ?? '';
$email = $_POST['studentEmail'] ?? '';

if (!$name || !$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name and email are required']);
    exit;
}

// Check if email already exists
$stmt = mysqli_prepare($dbConn, "SELECT id FROM users WHERE email = ?");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_fetch_assoc($result)) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Email already exists']);
    exit;
}

$defaultPassword = password_hash('password123', PASSWORD_DEFAULT);

try {
    $stmt = mysqli_prepare($dbConn, "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'student', 'active')");
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . mysqli_error($dbConn));
    }
    mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $defaultPassword);
    mysqli_stmt_execute($stmt);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create student']);
}
