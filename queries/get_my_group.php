<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

$db = new Database();
$conn = $db->connect();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Kunin group info ng current student
$stmt = $conn->prepare("
    SELECT g.id, g.name, g.description, g.research_topic, g.status
    FROM group_members gm
    JOIN groups g ON gm.group_id = g.id
    WHERE gm.student_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($group = $res->fetch_assoc()) {
    $group_id = $group['id'];

    // Kunin lahat ng members ng group
    $stmt2 = $conn->prepare("
        SELECT u.id, u.name, u.email, u.course, u.year, gm.role
        FROM group_members gm
        JOIN users u ON gm.student_id = u.id
        WHERE gm.group_id = ?
    ");
    $stmt2->bind_param("i", $group_id);
    $stmt2->execute();
    $res2 = $stmt2->get_result();

    $members = [];
    while ($row = $res2->fetch_assoc()) {
        $members[] = $row;
    }

    echo json_encode([
        "success" => true,
        "group" => $group,
        "members" => $members
    ]);
} else {
    echo json_encode(["success" => false, "message" => "No group assigned"]);
}
?>