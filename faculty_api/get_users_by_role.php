<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$role = $_GET['role'] ?? '';

if (!$role) {
    http_response_code(400);
    echo json_encode(['error' => 'Role parameter is required']);
    exit;
}

$db = new Database();
$pdo = $db->connect();

$roles = [$role];
if ($role === 'adviser') {
    // Only include advisers
    $roles = ['adviser'];
} elseif ($role === 'english_critique') {
    // Map to database role critique_english
    $roles = ['critique_english'];
} elseif ($role === 'statistician') {
    $roles = ['critique_statistician'];
} elseif ($role === 'financial_analyst') {
    $roles = ['financial_critique'];
}

try {
    $placeholders = str_repeat('?,', count($roles) - 1) . '?';
    $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE role IN ($placeholders) AND status = 'active' ORDER BY name");
    $stmt->execute($roles);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch users', 'details' => $e->getMessage()]);
}
?>
