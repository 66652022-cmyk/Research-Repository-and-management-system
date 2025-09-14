<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || $_SESSION['user_role'] !== 'super_admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}
require_once '../config/database.php';
$db = new Database();
$pdo = $db->connect();

$stmt = $pdo->prepare("SELECT id, name, email, role AS type, created_at AS created FROM users WHERE status='active' ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'users' => $users]);
?>