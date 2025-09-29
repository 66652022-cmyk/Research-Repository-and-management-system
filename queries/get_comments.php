<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$db = new Database();
$conn = $db->connect();

$document_id = isset($_GET['document_id']) ? intval($_GET['document_id']) : 0;

if ($document_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid document_id"]);
    exit;
}

$sql = "SELECT c.id, c.comment, c.type, c.page_number, c.line_number, c.status, c.created_at, u.name AS user_name
        FROM comments c
        LEFT JOIN users u ON c.user_id = u.id
        WHERE c.document_id = ? AND c.status = 'active'
        ORDER BY c.created_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $document_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

echo json_encode(["success" => true, "comments" => $comments]);
