<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->connect();

    $groupId         = $data['groupId'] ?? null;
    $englishCritique = $data['englishCritique'] ?? null;
    $statistician    = $data['statistician'] ?? null;
    $financialAnalyst = $data['financialAnalyst'] ?? null;

    if (!$groupId || !$englishCritique || !$statistician || !$financialAnalyst) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE groups 
         SET english_critique_id = ?, 
             statistician_id = ?, 
             financial_analyst_id = ?, 
             updated_at = NOW()
         WHERE id = ?"
    );

    if ($stmt === false) {
        throw new Exception("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'iiii', $englishCritique, $statistician, $financialAnalyst, $groupId);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Assignments saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save assignments: ' . mysqli_stmt_error($stmt)]);
    }

    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
