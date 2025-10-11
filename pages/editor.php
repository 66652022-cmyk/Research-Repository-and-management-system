<?php
require '../vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;

$docId = $_GET['id'] ?? null;
$html = '';

if ($docId) {
    require_once '../config/database.php';
    $db = new Database();
    $conn = $db->connect();

    // Kunin file_path mula sa DB
    $stmt = $conn->prepare("SELECT file_path FROM documents WHERE id = ?");
    $stmt->bind_param("i", $docId);
    $stmt->execute();
    $stmt->bind_result($filePath);
    $stmt->fetch();
    $stmt->close();

    if ($filePath) {

         // Linisin ang path: alisin ang leading ../ kung sobra
        $cleanPath = preg_replace('#^(\.\./)+#', '../', $filePath);

        // Gawing absolute path
        $fullPath = realpath(__DIR__ . '/../' . ltrim($cleanPath, './'));

        // I-check kung existing yung file
        if ($fullPath && file_exists($fullPath)) {
            $phpWord = IOFactory::load($fullPath);
            $writer = IOFactory::createWriter($phpWord, 'HTML');
            ob_start();
            $writer->save('php://output');
            $html = ob_get_clean();
        } else {
            $html = "<p>File not found at: $fullPath</p>";
        }
    } else {
        $html = "<p>No file path found for this document ID.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Text Editor</title>
  <script src="https://cdn.tiny.cloud/1/eco46efqe3zl91r7iear2s2f1d47pyayrjoc94ptccpin6q2/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    #editor-container { max-width: 900px; margin: 0 auto; }
    #loading {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.9);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    .spinner {
      width: 50px;
      height: 50px;
      border: 5px solid #f3f3f3;
      border-top: 5px solid #3498db;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    #save-btn {
      margin-top: 10px;
      padding: 10px 20px;
      background: #28a745;
      color: #fff;
      border: none;
      cursor: pointer;
      border-radius: 5px;
    }
    #save-btn:hover { background: #218838; }
  </style>
</head>
<body>
  <div id="loading">
    <div class="spinner"></div>
  </div>
  
  <div id="editor-container">
    <h2>Text Editor</h2>
    <textarea id="myEditor"><?= $html ?></textarea>
    <button id="save-btn" onclick="saveDocument()">💾 Save Changes</button>
  </div>

  <script>
    tinymce.init({
      selector: '#myEditor',
      height: 600,
      menubar: true,
      plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
      toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
      setup: function(editor) {
        editor.on('init', function() {
          document.getElementById('loading').style.display = 'none';
        });
      }
    });

    window.addEventListener('message', (event) => {
        if (event.data.type === 'loadDocument') {
            document.getElementById('loading').style.display = 'flex';
            const htmlContent = event.data.document;
            tinymce.get('myEditor').setContent(htmlContent || '');
            setTimeout(() => {
                document.getElementById('loading').style.display = 'none';
            }, 500);
        }
    });


    function saveDocument() {
      const content = tinymce.get('myEditor').getContent();

      fetch('../queries/upload_handler/update_document.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id: <?= json_encode($docId) ?>,
          content: content
        })
      })
      .then(res => res.json())
      .then(data => {
        console.log('Response:', data);
        if (data.success) {
          alert('Document saved successfully!');
        } else {
          alert('Error saving document: ' + data.message);
        }
      })
      .catch(err => alert('❌ ' + err.message));
    }
  </script>
</body>
</html>
