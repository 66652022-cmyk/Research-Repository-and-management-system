<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || $_SESSION['user_role'] !== 'super_admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}
require_once '../config/database.php';
$db = new Database();
$dbConn = $db->connect();

$stmt = mysqli_prepare($dbConn, "SELECT id, name, email, role AS type, created_at AS created FROM users WHERE status='active' ORDER BY created_at DESC");
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

echo json_encode(['success' => true, 'users' => $users]);
?>
