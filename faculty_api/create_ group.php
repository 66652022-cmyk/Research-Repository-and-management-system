<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$db = new Database();
$dbConn = $db->connect();

$name = $_POST['groupName'] ?? '';
$thesisTitle = $_POST['thesisTitle'] ?? '';
$researchTopic = $_POST['researchTopic'] ?? '';
$members = $_POST['groupMembers'] ?? [];

// Validate required fields
if (!$name || !$thesisTitle || !$researchTopic || empty($members)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Group name, thesis title, research topic, and members are required']);
    exit;
}


// Check if user is logged in
session_start();
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['user_role'] ?? null;

if (!$user_id || $user_role !== 'research_faculty') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized - only faculty can create groups']);
    exit;
}

try {
    mysqli_autocommit($dbConn, false); // Start transaction

    // Insert group (automatic faculty_id)
    $stmt = mysqli_prepare(
    $dbConn,
    "INSERT INTO groups (name, description, faculty_id, status, research_topic) 
        VALUES (?, ?, ?, 'active', ?)"
    );
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . mysqli_error($dbConn));
    }
    mysqli_stmt_bind_param($stmt, 'ssss', $name, $thesisTitle, $user_id, $researchTopic);
    mysqli_stmt_execute($stmt);
    $group_id = mysqli_insert_id($dbConn);


    // Insert group members (first = leader, rest = members)
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
    echo json_encode(['success' => true, 'message' => 'Group created successfully']);
} catch (Exception $e) {
    mysqli_rollback($dbConn);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create group: ' . $e->getMessage()]);
}
