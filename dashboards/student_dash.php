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
        <h1>Student Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['user_name']; ?>!</p>
        <p>Role: Student</p>
        <a href="/THESIS/logout.php">Logout</a>
    </body>
</html>
