<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SESSION['user_role'] !== 'super_admin') {
    echo json_encode(['success' => false, 'message' => 'Insufficient privileges']);
    exit();
}

if (!isset($_POST['admin_to_adviser_login']) || $_POST['admin_to_adviser_login'] !== 'true') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

try {
    require_once '../config/database.php';

    $admin_user_id = $_POST['admin_user_id'] ?? '';
    $admin_email = $_POST['admin_email'] ?? '';

    if ($admin_user_id != $_SESSION['user_id'] || $admin_email != $_SESSION['user_email']) {
        echo json_encode(['success' => false, 'message' => 'Credential mismatch']);
        exit();
    }
    
    $_SESSION['original_admin_session'] = [
        'user_id' => $_SESSION['user_id'],
        'user_name' => $_SESSION['user_name'],
        'user_email' => $_SESSION['user_email'],
        'user_role' => $_SESSION['user_role'],
        'login_time' => $_SESSION['login_time'] ?? time()
    ];

    //Use admin account as adviser directly
    $_SESSION['user_role'] = 'research adviser';
    $_SESSION['acting_as_adviser'] = true;
    $_SESSION['adviser_login_time'] = time();
    
    //If have a separate adviser record for the admin
    /*
    $database = new Database();
    $db = $database->connect();
    $stmt = mysqli_prepare($db, "SELECT * FROM users WHERE email = ? AND role = 'adviser'");
    mysqli_stmt_bind_param($stmt, 's', $admin_email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $adviser_record = mysqli_fetch_assoc($result);

    if ($adviser_record) {
        $_SESSION['user_id'] = $adviser_record['id'];
        $_SESSION['user_name'] = $adviser_record['name'];
        $_SESSION['user_role'] = 'adviser';
        $_SESSION['acting_as_adviser'] = true;
        $_SESSION['adviser_login_time'] = time();
    } else {
        // Create temporary adviser session with admin credentials
        $_SESSION['user_role'] = 'adviser';
        $_SESSION['acting_as_adviser'] = true;
        $_SESSION['adviser_login_time'] = time();
    }
    */
    
    echo json_encode([
        'success' => true, 
        'message' => 'Successfully switched to adviser dashboard',
        'redirect_url' => '/THESIS/dashboards/adviser_dash.php'
    ]);
    
} catch (Exception $e) {
    error_log("Admin to Adviser Login Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while switching to adviser dashboard'
    ]);
}
?>