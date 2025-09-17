<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$db = new Database();
$pdo = $db->connect();

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
    $pdo->beginTransaction();

    // Insert group
    $stmt = $pdo->prepare("INSERT INTO groups (name, description, adviser_id, english_critique_id, statistician_id, financial_analyst_id, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
    $stmt->execute([$name, $thesisTitle, $adviser_id, $english_critique_id, $statistician_id, $financial_analyst_id]);
    $group_id = $pdo->lastInsertId();

    // Insert group members
    $stmtMember = $pdo->prepare("INSERT INTO group_members (group_id, student_id, role) VALUES (?, ?, ?)");
    $first = true;
    foreach ($members as $student_id) {
        $role = $first ? 'leader' : 'member';
        $stmtMember->execute([$group_id, $student_id, $role]);
        $first = false;
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create group: ' . $e->getMessage()]);
}
