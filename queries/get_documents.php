<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$db = new Database();
$conn = $db->connect();

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid document id"]);
    exit;
}

$sql = "SELECT dv.id AS version_id, dv.content, d.title, d.type, d.id AS document_id
        FROM documents d
        LEFT JOIN document_versions dv ON d.id = dv.document_id
        WHERE d.id = ?
        ORDER BY dv.created_at DESC
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "success" => true,
        "document" => $row
    ]);
} else {
    echo json_encode(["success" => false]);
}
