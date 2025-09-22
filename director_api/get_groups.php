<?php

$stmt = mysqli_prepare($dbConn, "
    SELECT g.id, g.name, g.description, g.status, g.created_at,
           adv.name AS adviser_name,
           eng.name AS english_critique_name,
           stat.name AS statistician_name,
           fin.name AS financial_analyst_name
    FROM groups g
    LEFT JOIN users adv  ON g.adviser_id = adv.id
    LEFT JOIN users eng  ON g.english_critique_id = eng.id
    LEFT JOIN users stat ON g.statistician_id = stat.id
    LEFT JOIN users fin  ON g.financial_analyst_id = fin.id
    ORDER BY g.created_at DESC
");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$groups = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get members for each group
foreach ($groups as &$group) {
    $stmt2 = mysqli_prepare($dbConn, "
        SELECT u.name 
        FROM group_members gm 
        JOIN users u ON gm.student_id = u.id 
        WHERE gm.group_id = ?
    ");
    mysqli_stmt_bind_param($stmt2, 'i', $group['id']);
    mysqli_stmt_execute($stmt2);
    $result2 = mysqli_stmt_get_result($stmt2);

    $members = [];
    while ($row = mysqli_fetch_assoc($result2)) {
        $members[] = $row['name'];
    }
    $group['members'] = $members;
    mysqli_stmt_close($stmt2);
}

mysqli_stmt_close($stmt);
?>