<?php
function getUserGroupsAndStats($dbConn, $role, $userId) {
    // Base query with joins (para makuha rin names ng critiques at adviser)
    $sql = "SELECT g.*, 
                   adv.name  AS adviser_name, 
                   eng.name  AS english_critique_name, 
                   stat.name AS statistician_name, 
                   fin.name  AS financial_analyst_name
            FROM groups g
            LEFT JOIN users adv  ON g.adviser_id = adv.id
            LEFT JOIN users eng  ON g.english_critique_id = eng.id
            LEFT JOIN users stat ON g.statistician_id = stat.id
            LEFT JOIN users fin  ON g.financial_analyst_id = fin.id";

    // Default (no filter for super_admin)
    $where  = "";
    $params = [];
    $types  = "";

    // Role-based filtering
    if ($role === 'adviser') {
        $where = " WHERE g.adviser_id = ?";
        $params[] = $userId;
        $types   .= "i";
    } elseif ($role === 'critique_english') {
        $where = " WHERE g.english_critique_id = ?";
        $params[] = $userId;
        $types   .= "i";
    } elseif ($role === 'critique_statistician') {
        $where = " WHERE g.statistician_id = ?";
        $params[] = $userId;
        $types   .= "i";
    } elseif ($role === 'financial_critique') {
        $where = " WHERE g.financial_analyst_id = ?";
        $params[] = $userId;
        $types   .= "i";
    } elseif ($role === 'super_admin') {
        // walang filter
    }

    $sql .= $where . " ORDER BY g.created_at DESC";

    // Prepare & execute
    $stmt = mysqli_prepare($dbConn, $sql);
    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $groups = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    // Default stats (para walang "undefined index")
    $stats = [
        'assigned_students'   => 0,
        'pending_reviews'     => 0,
        'pending_submissions' => 0
    ];

    // Fill stats
    $stats['assigned_students'] = count($groups);

    foreach ($groups as $g) {
        if ($g['status'] === 'on_hold') {
            $stats['pending_reviews']++;
        } elseif ($g['status'] === 'active') {
            $stats['pending_submissions']++;
        }
    }

    return ['groups' => $groups, 'stats' => $stats];
}
