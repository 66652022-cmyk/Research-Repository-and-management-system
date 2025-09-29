<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

$db = new Database();
$dbConn = $db->connect();

// Kunin ang user id at role mula sa session
$userId = $_SESSION['user_id'] ?? 0;
$role   = $_SESSION['user_role'] ?? '';

if (!$userId || !$role) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

// Base query
$sql = "SELECT g.id, g.name, COUNT(m.id) AS members
        FROM groups g
        LEFT JOIN group_members m ON g.id = m.group_id ";

// Role-based filtering
$params = [];
$types = "";
if ($role === 'adviser') {
    $sql .= "WHERE g.adviser_id = ?";
    $params[] = $userId;
    $types .= "i";
} elseif ($role === 'critique_english') {
    $sql .= "WHERE g.english_critique_id = ?";
    $params[] = $userId;
    $types .= "i";
} elseif ($role === 'critique_statistician') {
    $sql .= "WHERE g.statistician_id = ?";
    $params[] = $userId;
    $types .= "i";
} elseif ($role === 'financial_critique') {
    $sql .= "WHERE g.financial_analyst_id = ?";
    $params[] = $userId;
    $types .= "i";
} elseif ($role === 'super_admin') {
    // walang filter = makikita lahat
}

$sql .= " GROUP BY g.id ORDER BY g.created_at DESC";

$stmt = $dbConn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
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
