<?php
// File: save_profile_student.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

try {
    $input = file_get_contents('php://input');
    if (!$input) throw new Exception('No input data received');

    $data = json_decode($input, true);
    if (!$data) throw new Exception('Invalid input data');

    $required = ['gender','year','course'];
    foreach($required as $field){
        if (empty($data[$field])) throw new Exception("Field '$field' is required");
    }

    require_once '../config/database.php';
    require_once '../classes/UnifiedAuth.php';

    $database = new Database();
    $db = $database->connect();
    $auth = new UnifiedAuth();

    // Update user profile
    $stmt = mysqli_prepare($db, "UPDATE users SET gender = ?, year = ?, course = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'sisi', $data['gender'], $data['year'], $data['course'], $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);

    $stmt = mysqli_prepare($db, "SELECT role FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    $role = $user['role'] ?? 'student';

    // Get dashboard URL
    $dashboard = $auth->getDashboardUrl($role);

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully',
        'dashboard' => $dashboard
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
