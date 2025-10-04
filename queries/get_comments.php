<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

$db = new Database();
$conn = $db->connect();

if (!isset($_SESSION['user_id'], $_SESSION['user_role'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$user_id  = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

$document_id = isset($_GET['document_id']) ? intval($_GET['document_id']) : 0;
if ($document_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid document_id"]);
    exit;
}

// 🔹 Kunin group_id ng document
$sqlDoc = "SELECT group_id FROM documents WHERE id = ?";
$stmtDoc = $conn->prepare($sqlDoc);
$stmtDoc->bind_param("i", $document_id);
$stmtDoc->execute();
$resDoc = $stmtDoc->get_result();
if (!$rowDoc = $resDoc->fetch_assoc()) {
    echo json_encode(["success" => false, "message" => "Document not found"]);
    exit;
}
$group_id = $rowDoc['group_id'];

// 🔹 Base query
$sql = "SELECT c.id, c.comment, c.type, c.page_number, c.line_number, 
               c.status, c.created_at, c.parent_id,
               u.name AS user_name, u.role AS user_role
        FROM comments c
        LEFT JOIN users u ON c.user_id = u.id
        WHERE c.document_id = ? AND c.status = 'active'";

$params = [$document_id];
$types  = "i";
$allowed = false;

// 🔹 Access control per role
if ($user_role === 'super_admin' || $user_role === 'research_director') {
    $allowed = true; // full access

} elseif ($user_role === 'adviser') {
    $sql .= " AND u.role IN ('student','adviser')";
    $allowed = true;

} elseif ($user_role === 'critique_english') {
    $sql .= " AND u.role IN ('student','critique_english')";
    $allowed = true;

} elseif ($user_role === 'critique_statistician') {
    $sql .= " AND u.role IN ('student','critique_statistician')";
    $allowed = true;

} elseif ($user_role === 'financial_critique') {
    $sql .= " AND u.role IN ('student','financial_critique')";
    $allowed = true;

} elseif ($user_role === 'research_faculty') {
    // Faculty dapat assigned sa group
    $sqlCheck = "SELECT 1 FROM groups WHERE id = ? AND faculty_id = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("ii", $group_id, $user_id);
    $stmtCheck->execute();
    $isAssigned = $stmtCheck->get_result()->num_rows > 0;

    if ($isAssigned) {
        $sql .= " AND u.role IN ('student','adviser','critique_english','critique_statistician','financial_critique','research_faculty')";
        $allowed = true;
    }

} elseif ($user_role === 'student') {
    // Student dapat member ng group
    $sqlCheck = "SELECT 1 FROM group_members WHERE group_id = ? AND student_id = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("ii", $group_id, $user_id);
    $stmtCheck->execute();
    $isMember = $stmtCheck->get_result()->num_rows > 0;

    if ($isMember) {
        $sql .= " AND u.role IN ('student','adviser','critique_english','critique_statistician','financial_critique','research_faculty')";
        $allowed = true;
    }
}

if (!$allowed) {
    echo json_encode(["success" => false, "message" => "Not authorized"]);
    exit;
}

$sql .= " ORDER BY c.created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

echo json_encode(["success" => true, "comments" => $comments]);
$stmtDoc->close();
$stmt->close();
$conn->close();
?>