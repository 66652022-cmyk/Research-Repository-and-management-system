<?php
// File: save_profile_teacher.php
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

    $required = ['gender','educational_attainment','course'];
    foreach($required as $field){
        if (empty($data[$field])) throw new Exception("Field '$field' is required");
    }

    require_once '../config/database.php';
    $database = new Database();
    $pdo = $database->connect();

    $stmt = $pdo->prepare("UPDATE users SET gender = ?, educational_attainment = ?, course = ? WHERE id = ?");
    $stmt->execute([
        $data['gender'],
        $data['educational_attainment'],
        $data['course'],
        $_SESSION['user_id']
    ]);

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
