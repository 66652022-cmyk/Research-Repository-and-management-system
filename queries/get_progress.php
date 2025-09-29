<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

$db = new Database();
$conn = $db->connect();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

if (!isset($_GET['group_id'])) {
    echo json_encode(["success" => false, "message" => "No group_id provided"]);
    exit;
}

$group_id = intval($_GET['group_id']);

// Template of chapters & parts
$chapters_template = [
    1 => [
        "title" => "The problem and its setting",
        "parts" => [
            "Introduction",
            "Review of Related Literature",
            "Theoretical Framework and/or Conceptual frameworks",
            "Statement of the Problem, Hypotheses (if applicable)",
            "Scope and Delimitation of the study",
            "Significance of the study",
            "Definition of terms"
        ]
    ],
    2 => [
        "title" => "Research Methodology",
        "parts" => [
            "Research Design",
            "Research Locale",
            "Sample and sampling Procedure",
            "Sample and Sampling Criteria",
            "Data Gathering Procedure",
            "Data Gathering Instrument",
            "Data Analysis Techniques",
            "Ethical Considerations"
        ]
    ],
    3 => [
        "title" => "Planning and Results",
        "parts" => [
            "Planning phase",
            "Simulation",
            "Presentation of Results",
            "Analysis and Interpretation of Results"
        ]
    ],
    4 => [
        "title" => "Summary and Conclusion",
        "parts" => [
            "Summary of Findings",
            "Conclusion",
            "Limitation of the Study",
            "Recommendations"
        ]
    ]
];

// Fetch actual submissions
$stmt = $conn->prepare("
    SELECT chapter, part, status
    FROM documents
    WHERE group_id = ?
");
$stmt->bind_param("i", $group_id);
$stmt->execute();
$res = $stmt->get_result();

$progress_data = [];
while ($row = $res->fetch_assoc()) {
    $progress_data[$row['chapter']][$row['part']] = $row['status'];
}

// Build final chapters array
$chapters = [];
foreach ($chapters_template as $chapNum => $chapInfo) {
    $total_parts = count($chapInfo['parts']);
    $completed_parts = 0;
    $parts_arr = [];
    foreach ($chapInfo['parts'] as $part) {
        $status = isset($progress_data[$chapNum][$part]) ? $progress_data[$chapNum][$part] : 'pending';
        if ($status === 'approved') $completed_parts++;
        $parts_arr[] = [
            "part" => $part,
            "status" => $status
        ];
    }

    $chapters[] = [
        "chapter" => $chapNum,
        "title" => $chapInfo['title'],
        "parts" => $parts_arr,
        "total_parts" => $total_parts,
        "completed_parts" => $completed_parts,
        "percentage" => $total_parts > 0 ? round(($completed_parts / $total_parts) * 100) : 0
    ];
}

// Overall progress
$total_parts = 0;
$completed_parts = 0;
foreach ($chapters as $ch) {
    $total_parts += $ch['total_parts'];
    $completed_parts += $ch['completed_parts'];
}
$overall_percentage = $total_parts > 0 ? round(($completed_parts / $total_parts) * 100) : 0;

echo json_encode([
    "success" => true,
    "chapters" => $chapters,
    "overall" => [
        "total_parts" => $total_parts,
        "completed_parts" => $completed_parts,
        "percentage" => $overall_percentage
    ]
]);
error_log(print_r($progress_data, true));

?>