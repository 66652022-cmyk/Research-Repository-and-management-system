<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');

// Check if user logged in
if(!isset($_SESSION['user_id'])) {
    exit(json_encode(["success"=>false,"message"=>"Not logged in"]));
}

// Check kung kumpleto yung data
if(!isset($_POST['group_id'], $_POST['chapter'], $_POST['part'], $_POST['title'], $_FILES['file'])) {
    exit(json_encode(["success"=>false,"message"=>"Incomplete data"]));
}

$db = new Database();
$conn = $db->connect();

// Sanitize inputs
$group_id = intval($_POST['group_id']);
$chapter = intval($_POST['chapter']);
$part = htmlspecialchars(trim($_POST['part']));
$title = htmlspecialchars(trim($_POST['title']));
$user_id = $_SESSION['user_id'];
$file = $_FILES['file'];

// File validation
$allowedTypes = [
    'application/pdf',                  
    'image/jpeg',                       
    'image/png',                        
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
    'application/msword',               
    'application/vnd.ms-excel',         
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
];
$maxSize = 10*1024*1024; // 10MB

if(!in_array($file['type'], $allowedTypes)) {
    exit(json_encode(["success"=>false,"message"=>"Invalid file type"]));
}

if($file['size'] > $maxSize) {
    exit(json_encode(["success"=>false,"message"=>"File too large"]));
}

// Save file
$targetDir = "../../uploads/";
$fileName = bin2hex(random_bytes(16)) . "_" . basename($file['name']);
$filePath = $targetDir . $fileName;

if(!move_uploaded_file($file['tmp_name'], $filePath)) {
    exit(json_encode(["success"=>false,"message"=>"File upload failed"]));
}

// Kunin file details
$fileSize = $file['size']; // raw bytes
$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)); 
$researchPart = "chapter".$chapter;

// Insert sa DB
$stmt = $conn->prepare("INSERT INTO documents 
    (group_id, chapter, part, title, file_path, file_size, mime_type, type, status, submitted_by, submitted_at, created_at, updated_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'submitted', ?, NOW(), NOW(), NOW())");

$stmt->bind_param("iisssissi", 
    $group_id,      
    $chapter,       
    $part,          
    $title,         
    $filePath,      
    $fileSize,      
    $fileExtension, 
    $researchPart,  
    $user_id        
);

if($stmt->execute()){
    echo json_encode(["success"=>true]);
} else {
    echo json_encode(["success"=>false,"message"=>"Database insert failed: ".$stmt->error]);
}
?>
