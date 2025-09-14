<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /THESIS/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <title>Research Director</title>
    </head>

    <body>
        <h1>Research Director Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['user_name']; ?>!</p>
        <p>Role: Research Director</p>
        <a href="#" onclick="confirmLogout()">Logout</a>
    </body>
    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = '../classes/LogoutHandling.php';
            }
        }
    </script>
</html>