<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$db = new Database();
$dbConn = $db->connect();

$name = $_POST['groupName'] ?? '';
$thesisTitle = $_POST['thesisTitle'] ?? '';
$members = $_POST['groupMembers'] ?? [];
$adviser_id = !empty($_POST['adviser_id']) ? $_POST['adviser_id'] : null;
$english_critique_id = !empty($_POST['english_critique_id']) ? $_POST['english_critique_id'] : null;
$statistician_id = !empty($_POST['statistician_id']) ? $_POST['statistician_id'] : null;
$financial_analyst_id = !empty($_POST['financial_analyst_id']) ? $_POST['financial_analyst_id'] : null;

if (!$name || !$thesisTitle || empty($members) || !$adviser_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Group name, thesis title, members, and adviser are required']);
    exit;
}

// Check if user is logged in
session_start();
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    mysqli_autocommit($dbConn, false); // Start transaction

    // Insert group
    $stmt = mysqli_prepare($dbConn, "INSERT INTO groups (name, description, adviser_id, english_critique_id, statistician_id, financial_analyst_id, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . mysqli_error($dbConn));
    }
    mysqli_stmt_bind_param($stmt, 'ssssss', $name, $thesisTitle, $adviser_id, $english_critique_id, $statistician_id, $financial_analyst_id);
    mysqli_stmt_execute($stmt);
    $group_id = mysqli_insert_id($dbConn);

    // Insert group members
    $stmtMember = mysqli_prepare($dbConn, "INSERT INTO group_members (group_id, student_id, role) VALUES (?, ?, ?)");
    if ($stmtMember === false) {
        throw new Exception("Prepare failed: " . mysqli_error($dbConn));
    }
    $first = true;
    foreach ($members as $student_id) {
        $role = $first ? 'leader' : 'member';
        mysqli_stmt_bind_param($stmtMember, 'iss', $group_id, $student_id, $role);
        mysqli_stmt_execute($stmtMember);
        $first = false;
    }

    mysqli_commit($dbConn);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    mysqli_rollback($dbConn);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create group: ' . $e->getMessage()]);
}
