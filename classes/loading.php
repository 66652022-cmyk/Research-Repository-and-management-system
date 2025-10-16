<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Loading Dashboard...</title>
<link rel="stylesheet" href="/THESIS/src/output.css">
<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
.spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #e5e7eb;
    border-top-color: #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
</style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="flex flex-col items-center space-y-4">
        <div class="spinner"></div>
        <p class="text-gray-600 font-medium">Loading your dashboard...</p>
    </div>

    <script>
        // Kunin ang URL sa localStorage
        const redirectUrl = localStorage.getItem('redirect_url');
        if (redirectUrl) {
            // optional: small delay for smoother effect
            setTimeout(() => {
                window.location.href = redirectUrl;
                localStorage.removeItem('redirect_url');
            }, 1200); // 1.2 seconds
        }
    </script>
</body>
</html>
