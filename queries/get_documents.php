<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require '../vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;

$db = new Database();
$conn = $db->connect();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid document id"]);
    exit;
}

$sql = "SELECT id AS document_id, title, type, file_path, file_size, mime_type
        FROM documents
        WHERE id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $htmlContent = '';
    $filePath = $row['file_path'] ?? '';

    if ($filePath) {
       
        $cleanPath = preg_replace('#^(\.\./)+#', '../', $filePath);

        $fullPath = realpath(__DIR__ . '/../' . ltrim($cleanPath, './'));

        if ($fullPath && file_exists($fullPath)) {
            try {
                $phpWord = IOFactory::load($fullPath);
                $writer = IOFactory::createWriter($phpWord, 'HTML');
                ob_start();
                $writer->save('php://output');
                $htmlContent = ob_get_clean();
            } catch (Exception $e) {
                $htmlContent = "<p>Failed to load document: {$e->getMessage()}</p>";
            }
        } else {
            $htmlContent = "<p>File not found at: $fullPath</p>";
        }
    } else {
        $htmlContent = "<p>No file path found for this document ID.</p>";
    }

    echo json_encode([
        "success" => true,
        "document" => [
            "id" => $row['document_id'],
            "title" => $row['title'],
            "type" => $row['type'],
            "html" => $htmlContent
        ]
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Document not found"
    ]);
}

$stmt->close();
$conn->close();
?>
