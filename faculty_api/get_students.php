<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$db = new Database();
$dbConn = $db->connect();

try {
    $stmt = mysqli_prepare($dbConn, "SELECT id, name, email FROM users WHERE role = 'student' AND status = 'active' ORDER BY name");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $students = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
    echo json_encode($students);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch students', 'details' => $e->getMessage()]);
}
