<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../vendor/autoload.php';
require_once '../../config/database.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\IOFactory;

$db = new Database();
$conn = $db->connect();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$docId = intval($data['id'] ?? 0);
$content = trim($data['content'] ?? '');

if ($docId <= 0 || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$stmt = $conn->prepare("SELECT file_path FROM documents WHERE id = ?");
$stmt->bind_param("i", $docId);
$stmt->execute();
$stmt->bind_result($filePath);
$stmt->fetch();
$stmt->close();

if (!$filePath) {
    echo json_encode(['success' => false, 'message' => 'No file path found']);
    exit;
}

// 🔹 Linisin ang path
$cleanPath = preg_replace('#^(\.\./)+#', '../', $filePath);
$absolutePath = realpath(__DIR__ . '/../../' . ltrim($cleanPath, './'));

if (!$absolutePath || !file_exists($absolutePath)) {
    echo json_encode(['success' => false, 'message' => "File not found at $absolutePath"]);
    exit;
}

$tmpFile = $absolutePath . '.tmp';
$backupFile = $absolutePath . '.bak';

try {
    if (file_exists($absolutePath)) {
        copy($absolutePath, $backupFile);
    }

    $phpWord = new PhpWord();
    $section = $phpWord->addSection();
    Html::addHtml($section, $content, false, false);

    $writer = IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($tmpFile);

    if (file_exists($tmpFile) && filesize($tmpFile) > 0) {
        rename($tmpFile, $absolutePath);

        // 🧹 Burahin ang backup file kapag successful na
        if (file_exists($backupFile)) {
            unlink($backupFile);
        }

        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Temporary file was not created properly.');
    }

} catch (Exception $e) {

    if (file_exists($tmpFile)) unlink($tmpFile);

    if (file_exists($backupFile)) {
        copy($backupFile, $absolutePath);
    }

    error_log('❌ Document update error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to save document: ' . $e->getMessage()]);
}

$conn->close();
?>
