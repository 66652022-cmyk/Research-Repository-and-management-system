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
$user_role = $_SESSION['user_role']; // galing sa login

if ($user_role === 'super_admin') {
    // SUPER ADMIN: kita lahat ng members at documents
    $sql = "SELECT u.id, u.name, gm.group_id
            FROM users u
            JOIN group_members gm ON gm.student_id = u.id
            ORDER BY gm.group_id, u.id";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $membersResult = $stmt->get_result();

    $members = [];
    while ($member = $membersResult->fetch_assoc()) {
        $member['submissions'] = [];

        $sql2 = "SELECT d.id, d.title, d.type, d.chapter, d.part,
                        d.mime_type, d.file_size, d.file_path, 
                        d.submitted_by, d.submitted_at
                 FROM documents d
                 WHERE d.submitted_by = ?
                 ORDER BY d.submitted_at DESC";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $member['id']);
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
    // NORMAL STUDENT / MEMBER: yung dati lang
    $sqlGroup = "SELECT group_id FROM group_members WHERE student_id = ? LIMIT 1";
    $stmtGroup = $conn->prepare($sqlGroup);
    $stmtGroup->bind_param("i", $user_id);
    $stmtGroup->execute();
    $resGroup = $stmtGroup->get_result();

    if ($rowGroup = $resGroup->fetch_assoc()) {
        $group_id = $rowGroup['group_id'];

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

            $sql2 = "SELECT d.id, d.title, d.type, d.chapter, d.part,
                            d.mime_type, d.file_size, d.file_path, 
                            d.submitted_by, d.submitted_at
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
}

echo json_encode($response);
?>