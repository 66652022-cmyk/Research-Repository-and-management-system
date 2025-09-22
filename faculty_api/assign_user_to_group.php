<?php
header('Content-Type: application/json');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$groupId = $_POST['group_id'] ?? '';
$assignmentType = $_POST['assignment_type'] ?? '';
$userId = $_POST['user_id'] ?? '';

if (!$groupId || !$assignmentType || !$userId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters: group_id, assignment_type, user_id']);
    exit;
}

$db = new Database();
$dbConn = $db->connect();

// Validate assignment type
$validTypes = ['english_critique', 'statistician', 'financial_analyst'];
if (!in_array($assignmentType, $validTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid assignment type']);
    exit;
}

// Map assignment type to database column
$columnMap = [
    'english_critique' => 'english_critique_id',
    'statistician' => 'statistician_id',
    'financial_analyst' => 'financial_analyst_id'
];

$column = $columnMap[$assignmentType];

// Check if user has the correct role
$roleMap = [
    'english_critique' => 'critique_english',
    'statistician' => 'critique_statistician',
    'financial_analyst' => 'financial_critique'
];

try {
    // Verify user has correct role
    $stmt = mysqli_prepare($dbConn, "SELECT role FROM users WHERE id = ? AND status = 'active'");
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . mysqli_error($dbConn));
    }
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user || $user['role'] !== $roleMap[$assignmentType]) {
        http_response_code(400);
        echo json_encode(['error' => 'User does not have the required role for this assignment type']);
        exit;
    }

    // Check if group exists
    $stmt = mysqli_prepare($dbConn, "SELECT id FROM groups WHERE id = ?");
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . mysqli_error($dbConn));
    }
    mysqli_stmt_bind_param($stmt, 'i', $groupId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Group not found']);
        exit;
    }

    // Assign user to group
    $stmt = mysqli_prepare($dbConn, "UPDATE groups SET {$column} = ? WHERE id = ?");
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . mysqli_error($dbConn));
    }
    mysqli_stmt_bind_param($stmt, 'ii', $userId, $groupId);
    mysqli_stmt_execute($stmt);

    if (mysqli_affected_rows($dbConn) > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'User assigned successfully',
            'assignment_type' => $assignmentType,
            'user_id' => $userId
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to assign user']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
}
?>
