<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$name = $_POST['studentName'] ?? '';
$email = $_POST['studentEmail'] ?? '';

if (!$name || !$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name and email are required']);
    exit;
}

// Check if email already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Email already exists']);
    exit;
}

$defaultPassword = password_hash('password123', PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'student', 'active')");
    $stmt->execute([$name, $email, $defaultPassword]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create student']);
}