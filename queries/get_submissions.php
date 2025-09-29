<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$db = new Database();
$conn = $db->connect();

$groupId = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;

if ($groupId <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid group_id"]);
    exit;
}

$sql = "SELECT d.id, d.title, d.type, d.file_path, d.file_size, d.mime_type,
               d.status, d.submitted_at, u.name AS submitted_by
        FROM documents d
        LEFT JOIN users u ON d.submitted_by = u.id
        WHERE d.group_id = ?
        ORDER BY d.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $groupId);
$stmt->execute();
$result = $stmt->get_result();

$documents = [];
while ($row = $result->fetch_assoc()) {
    $documents[] = [
        "id"           => $row["id"],
        "title"        => $row["title"],
        "type"         => $row["type"],
        "file_path"    => $row["file_path"],
        "file_size"    => $row["file_size"],
        "mime_type"    => $row["mime_type"],
        "status"       => $row["status"],
        "submitted_by" => $row["submitted_by"],
        "submitted_at" => $row["submitted_at"],
        "comments"     => []
    ];
}

echo json_encode(["success" => true, "documents" => $documents]);
