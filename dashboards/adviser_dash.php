<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: /THESIS/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <title>Research Adviser</title>
    </head>

    <body>
        <div id="adviser-dashboard" class="section">
        <h1>Research Adviser Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['user_name']; ?>!</p>
        <p>Role:Research Adviser</p>
        <a href="#" onclick="confirmLogout()">Logout</a>
        </div>
       
    </body>
    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = '../classes/LogoutHandling.php';
            }
        }
    </script>
</html>
