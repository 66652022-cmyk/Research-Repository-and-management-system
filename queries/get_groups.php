<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

$db = new Database();
$dbConn = $db->connect(); // mysqli connection

$adviser_id = $_SESSION['user_id'] ?? 0;

if (!$adviser_id) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$sql = "SELECT g.id, g.name, COUNT(m.id) as members
        FROM groups g
        LEFT JOIN group_members m ON g.id = m.group_id
        WHERE g.adviser_id = ?
        GROUP BY g.id";

$stmt = $dbConn->prepare($sql);   // <-- gumamit ng $dbConn
$stmt->bind_param("i", $adviser_id);
$stmt->execute();
$result = $stmt->get_result();

$groups = [];
while ($row = $result->fetch_assoc()) {
    $groups[] = [
        "id" => $row['id'],
        "name" => $row['name'],
        "members" => (int)$row['members']
    ];
}

echo json_encode(["success" => true, "groups" => $groups]);
