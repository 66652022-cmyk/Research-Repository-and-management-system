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
$dbConn = $db->connect();

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
    $stmt = mysqli_prepare($dbConn, "SELECT id, name, email FROM users WHERE role IN ($placeholders) AND status = 'active' ORDER BY name");
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . mysqli_error($dbConn));
    }
    $types = str_repeat('s', count($roles));
    mysqli_stmt_bind_param($stmt, $types, ...$roles);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    echo json_encode($users);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch users', 'details' => $e->getMessage()]);
}
?>
