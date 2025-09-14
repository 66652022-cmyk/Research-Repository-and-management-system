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
    <title>Student Dashboard</title>
    </head>

    <body>
        <h1>English Critique Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['user_name']; ?>!</p>
        <p>Role: English Critique</p>
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
