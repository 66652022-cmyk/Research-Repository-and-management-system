<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$db = new Database();
$dbConn = $db->connect();

// Check if user is logged in and is research director or super admin
session_start();
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_role = $_SESSION['user_role'] ?? '';
if (!in_array($user_role, ['research_director', 'super_admin'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
    exit;
}

try {
    // Get faculty members by role
    $faculty = [];

    // Get English Critiques
    $stmt = mysqli_prepare($dbConn, "SELECT id, name, email FROM users WHERE role = 'english_critique' AND status = 'active' ORDER BY name");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $faculty['english_critiques'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $faculty['english_critiques'][] = $row;
    }

    // Get Statisticians
    $stmt = mysqli_prepare($dbConn, "SELECT id, name, email FROM users WHERE role = 'statistician' AND status = 'active' ORDER BY name");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $faculty['statisticians'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $faculty['statisticians'][] = $row;
    }

    // Get Financial Analysts
    $stmt = mysqli_prepare($dbConn, "SELECT id, name, email FROM users WHERE role = 'financial_analyst' AND status = 'active' ORDER BY name");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $faculty['financial_analysts'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $faculty['financial_analysts'][] = $row;
    }

    echo json_encode(['success' => true, 'faculty' => $faculty]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to fetch faculty: ' . $e->getMessage()]);
}
?>
