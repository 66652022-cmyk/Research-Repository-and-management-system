<?php
include '../config/database.php';

$id = intval($_GET['id'] ?? 0);

$stmt = $dbConn->prepare("
    SELECT dv.id as version_id, dv.content, d.title, d.type, d.id as document_id
    FROM documents d
    LEFT JOIN document_versions dv ON d.id = dv.document_id
    WHERE d.id = ?
    ORDER BY dv.created_at DESC
    LIMIT 1
");
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
