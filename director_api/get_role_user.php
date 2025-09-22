<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$db = new Database();
$dbConn = $db->connect();

try {
    $roles = ['critique_english', 'critique_statistician', 'financial_critique', 'adviser'];
    $placeholders = implode(',', array_fill(0, count($roles), '?'));

    $stmt = mysqli_prepare($dbConn, "SELECT id, name, role FROM users WHERE role IN ($placeholders) AND status = 'active' ORDER BY name");
    $types = str_repeat('s', count($roles));
    mysqli_stmt_bind_param($stmt, $types, ...$roles);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $users
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
