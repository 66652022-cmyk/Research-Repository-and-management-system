<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$db = new Database();
$pdo = $db->connect();

try {
    $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE role = 'student' AND status = 'active' ORDER BY name");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($students);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch students', 'details' => $e->getMessage()]);
}
