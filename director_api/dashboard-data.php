<?php
require_once '../config/database.php';

$db = new Database();
$dbConn = $db->connect();

$response = [];

/* Active Groups */
$stmt = mysqli_prepare($dbConn, "SELECT COUNT(*) FROM groups WHERE status = 'active'");
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $activeGroups);
mysqli_stmt_fetch($stmt);
$response['active_groups'] = $activeGroups ?? 0;
mysqli_stmt_close($stmt);

/* Active Advisory Panels */
$stmt = mysqli_prepare($dbConn, "
    SELECT COUNT(*) 
    FROM users 
    WHERE status = 'active' 
    AND role IN ('adviser', 'critique_english', 'critique_statistician', 'financial_critique')
");

mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $totalActivePanels);
mysqli_stmt_fetch($stmt);
$response['active_panels'] = $totalActivePanels ?? 0;
mysqli_stmt_close($stmt);


/* Active English Critiques */
$stmt = mysqli_prepare($dbConn, "
    SELECT COUNT(*) 
    FROM users 
    WHERE status = 'active' AND role = 'critique_english'
");
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $englishCount);
mysqli_stmt_fetch($stmt);
$response['english_critiques'] = $englishCount ?? 0;
mysqli_stmt_close($stmt);

/* Active Statisticians */
$stmt = mysqli_prepare($dbConn, "
    SELECT COUNT(*) 
    FROM users 
    WHERE status = 'active' AND role = 'critique_statistician'
");
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $statisticianCount);
mysqli_stmt_fetch($stmt);
$response['statisticians'] = $statisticianCount ?? 0;
mysqli_stmt_close($stmt);

/* Active Financial Analysts */
$stmt = mysqli_prepare($dbConn, "
    SELECT COUNT(*) 
    FROM users 
    WHERE status = 'active' AND role = 'financial_critique'
");
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $financialCount);
mysqli_stmt_fetch($stmt);
$response['financial_analysts'] = $financialCount ?? 0;
mysqli_stmt_close($stmt);

/* Pending Publications */
$stmt = mysqli_prepare($dbConn, "
    SELECT COUNT(*) 
    FROM groups 
    WHERE status IN ('completed')
");
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $pending);
mysqli_stmt_fetch($stmt);
$response['pending'] = $pending ?? 0;
mysqli_stmt_close($stmt);

/* Total Groups by Course */
$courseCounts = [];
$stmt = mysqli_prepare($dbConn, "
    SELECT course, COUNT(*) as total 
    FROM groups 
    GROUP BY course
");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $courseCounts[] = $row;
}
$response['course_counts'] = $courseCounts;
mysqli_stmt_close($stmt);

/* Groups by Year Level */
$yearLevels = ['1','2','3','4'];
$yearCounts = [];
foreach ($yearLevels as $year) {
    $stmt = mysqli_prepare($dbConn, "SELECT COUNT(*) FROM groups WHERE year_level = ?");
    mysqli_stmt_bind_param($stmt, 's', $year);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    $yearCounts[$year] = $count ?? 0;
    mysqli_stmt_close($stmt);
}
$response['year_counts'] = $yearCounts;

/* Completed Groups */
$stmt = mysqli_prepare($dbConn, "SELECT COUNT(*) FROM groups WHERE status = 'completed'");
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $completed);
mysqli_stmt_fetch($stmt);
$response['completed_groups'] = $completed ?? 0;
mysqli_stmt_close($stmt);

/* Recent Activity */
$stmt = mysqli_prepare($dbConn, "
    SELECT * FROM (
        -- Completed Groups
        SELECT 
            CONCAT('Group ', g.name, ' has completed their work') AS title,
            g.updated_at AS activity_time,
            g.name AS group_name
        FROM groups g
        WHERE g.status = 'completed'

        UNION ALL

        -- New Groups Submitted
        SELECT 
            CONCAT('New group ', g.name, ' is submitted') AS title,
            g.created_at AS activity_time,
            g.name AS group_name
        FROM groups g

        UNION ALL

        -- New English Critique Assigned
        SELECT 
            CONCAT('New English Critique assigned to ', g.name) AS title,
            g.updated_at AS activity_time,
            g.name AS group_name
        FROM groups g
        WHERE g.english_critique_id IS NOT NULL

        UNION ALL

        -- New Statistician Assigned
        SELECT 
            CONCAT('New Statistician assigned to ', g.name) AS title,
            g.updated_at AS activity_time,
            g.name AS group_name
        FROM groups g
        WHERE g.statistician_id IS NOT NULL

        UNION ALL

        -- New Financial Analyst Assigned
        SELECT 
            CONCAT('New Financial Analyst assigned to ', g.name) AS title,
            g.updated_at AS activity_time,
            g.name AS group_name
        FROM groups g
        WHERE g.financial_analyst_id IS NOT NULL

    ) AS activities
    ORDER BY activity_time DESC
    LIMIT 5
");

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$recent = [];
while ($row = mysqli_fetch_assoc($result)) {
    $recent[] = $row;
}
$response['recent_activity'] = $recent;
mysqli_stmt_close($stmt);

// Return JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
