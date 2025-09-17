<?php
session_start();

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['original_admin_session'])) {
        echo json_encode(['success' => false, 'message' => 'No admin session to restore']);
        exit();
    }
    
    // Restore original admin session
    $original_session = $_SESSION['original_admin_session'];
    
    $_SESSION['user_id'] = $original_session['user_id'];
    $_SESSION['user_name'] = $original_session['user_name'];
    $_SESSION['user_email'] = $original_session['user_email'];
    $_SESSION['user_role'] = $original_session['user_role'];
    $_SESSION['login_time'] = $original_session['login_time'];
    
    unset($_SESSION['acting_as_adviser']);
    unset($_SESSION['adviser_login_time']);
    unset($_SESSION['original_admin_session']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Admin session restored successfully',
        'redirect_url' => '/THESIS/adminDash.php'
    ]);
    
} catch (Exception $e) {
    error_log("Restore Admin Session Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while restoring admin session'
    ]);
}
?>