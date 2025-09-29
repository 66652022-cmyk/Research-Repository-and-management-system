<?php
header('Content-Type: application/json');
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$document_id = intval($data['document_id']);
$user_id = $_SESSION['user_id'];
$comment = trim($data['comment']);

if ($document_id <= 0 || empty($comment)) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$db = new Database();
$conn = $db->connect();

$parent_id = isset($data['parent_id']) ? intval($data['parent_id']) : null;

$sql = "INSERT INTO comments (document_id, user_id, comment, parent_id) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisi", $document_id, $user_id, $comment, $parent_id);
if ($stmt->execute()) {
    echo json_encode(["success" => true, "comment_id" => $stmt->insert_id]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to save comment"]);
}
