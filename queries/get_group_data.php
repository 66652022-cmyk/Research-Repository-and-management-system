<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

$db = new Database();
$conn = $db->connect();

$response = ["success" => false, "members" => []];

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Step 0: Kunin group_id ng current student
$sqlGroup = "SELECT group_id FROM group_members WHERE student_id = ? LIMIT 1";
$stmtGroup = $conn->prepare($sqlGroup);
$stmtGroup->bind_param("i", $user_id);
$stmtGroup->execute();
$resGroup = $stmtGroup->get_result();

if ($rowGroup = $resGroup->fetch_assoc()) {
    $group_id = $rowGroup['group_id'];

    // Step 1: Kunin lahat ng members ng group
    $sql = "SELECT u.id, u.name
            FROM group_members gm
            JOIN users u ON gm.student_id = u.id
            WHERE gm.group_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $membersResult = $stmt->get_result();

    $members = [];
    while ($member = $membersResult->fetch_assoc()) {
        $member['submissions'] = [];

        // Step 2: Kunin lahat ng documents na sinubmit ng member na ito
        $sql2 = "SELECT d.id, d.title, d.type, d.mime_type, d.file_size, 
                        d.file_path, d.submitted_by, d.submitted_at
                 FROM documents d
                 WHERE d.submitted_by = ? AND d.group_id = ?
                 ORDER BY d.submitted_at DESC";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ii", $member['id'], $group_id);
        $stmt2->execute();
        $docsResult = $stmt2->get_result();

        while ($doc = $docsResult->fetch_assoc()) {
            $member['submissions'][] = $doc;
        }

        $members[] = $member;
    }

    $response['success'] = true;
    $response['members'] = $members;
} else {
    $response['message'] = "No group assigned";
}

echo json_encode($response);
