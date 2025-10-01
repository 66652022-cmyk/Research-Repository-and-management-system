<?php
require_once '../vendor/autoload.php'; // siguraduhing tama ang path

use Firebase\JWT\JWT;

// JWT secret — dapat tugma sa OnlyOffice container mo
$secret = "6p2UldWlFjBTbHAw6jzSj1dNLN3qOAwQ";

// File to open
$filename = "test.docx";
$filePath = __DIR__ . "/../uploads/" . $filename; // relative path from pages/

// Siguraduhin na existing yung file
if (!file_exists($filePath)) {
    die("File not found: " . htmlspecialchars($filePath));
}

// JWT payload
$payload = [
    "document" => [
        "fileType" => "docx",
        "key" => md5($filename . filemtime($filePath)),
        "title" => $filename,
        "url" => "http://localhost/THESIS/uploads/" . $filename // localhost para sa XAMPP
    ],
    "editorConfig" => [
        "callbackUrl" => "http://localhost/THESIS/callback.php" // localhost din
    ]
];

// Generate token
$token = JWT::encode($payload, $secret, 'HS256');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OnlyOffice Editor</title>
    <!-- API script mula sa Docker OnlyOffice -->
    <script src="http://localhost:8080/web-apps/apps/api/documents/api.js"></script>
</head>
<body>
    <div id="placeholder" style="width:100%; height:90vh;"></div>

    <script>
        var docEditor = new DocsAPI.DocEditor("placeholder", {
            "document": {
                "fileType": "docx",
                "key": "<?php echo md5($filename . filemtime($filePath)); ?>",
                "title": "<?php echo $filename; ?>",
                "url": "http://localhost/THESIS/uploads/<?php echo $filename; ?>"
            },
            "editorConfig": {
                "callbackUrl": "http://localhost/THESIS/callback.php"
            },
            "token": "<?php echo $token; ?>"
        });
    </script>
</body>
</html>
