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

    $groupId = $data['groupId'] ?? null;
    $adviser = $data['adviser'] ?? null;
    $englishCritique = $data['englishCritique'] ?? null;
    $statistician = $data['statistician'] ?? null;
    $financialAnalyst = $data['financialAnalyst'] ?? null;

    if (!$groupId) {
        echo json_encode(['success' => false, 'message' => 'Missing group ID']);
        exit;
    }

    //I-update ang group assignment
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE groups 
         SET adviser_id = ?, 
             english_critique_id = ?, 
             statistician_id = ?, 
             financial_analyst_id = ?, 
             updated_at = NOW()
         WHERE id = ?"
    );

    if ($stmt === false) {
        throw new Exception("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'iiiii', $adviser, $englishCritique, $statistician, $financialAnalyst, $groupId);

    if (!mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => false, 'message' => 'Failed to save assignments: ' . mysqli_stmt_error($stmt)]);
        exit;
    }
    mysqli_stmt_close($stmt);

    //Function para i-update status ng user
    function updateStatus($conn, $userId, $status) {
        if ($userId) {
            $stmt = mysqli_prepare($conn, "UPDATE users SET status = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'si', $status, $userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    //I-set sa "active" lahat ng may bagong assignment
    updateStatus($conn, $adviser, 'active');
    updateStatus($conn, $englishCritique, 'active');
    updateStatus($conn, $statistician, 'active');
    updateStatus($conn, $financialAnalyst, 'active');

    //Hanapin lahat ng users na advisers/critics na wala ng group assignment → gawin silang not_assigned
    $roles = ["adviser", "critique_english", "critique_statistician", "financial_critique"];
    $roleList = "'" . implode("','", $roles) . "'";

    $query = "
        SELECT u.id 
        FROM users u
        WHERE u.role IN ($roleList)
        AND u.id NOT IN (
            SELECT adviser_id FROM groups WHERE adviser_id IS NOT NULL
            UNION
            SELECT english_critique_id FROM groups WHERE english_critique_id IS NOT NULL
            UNION
            SELECT statistician_id FROM groups WHERE statistician_id IS NOT NULL
            UNION
            SELECT financial_analyst_id FROM groups WHERE financial_analyst_id IS NOT NULL
        )
    ";

    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        updateStatus($conn, $row['id'], 'not_assigned');
    }

    echo json_encode(['success' => true, 'message' => 'Assignments and status updated successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
