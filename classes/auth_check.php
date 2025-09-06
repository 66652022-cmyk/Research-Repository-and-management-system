<?php
//SUPER ADMIN ONLY ACCESS CHECK
session_start();
require_once 'classes/AdminAuth.php';

function checkSuperAdminAuth() {
    // Check if admin is logged in
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: adminLogin.php');
        exit();
    }
    
    // CRITICAL: Check if user is super admin - REJECT ALL OTHER ROLES
    if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') {
        session_destroy();
        header('Location: adminLogin.php?security=1');
        exit();
    }
    
    // Check session timeout
    if (isset($_SESSION['admin_last_activity'])) {
        $timeout = 30 * 60; // 30 minutes
        if (time() - $_SESSION['admin_last_activity'] > $timeout) {
            session_destroy();
            header('Location: adminLogin.php?timeout=1');
            exit();
        }
    }
    
    // Update last activity
    $_SESSION['admin_last_activity'] = time();
    
    // Double check if user still exists and is super admin
    $auth = new AdminAuth();
    $admin = $auth->getAdminById($_SESSION['admin_id']);
    if (!$admin || $admin['role'] !== 'super_admin') {
        session_destroy();
        header('Location: adminLogin.php?deactivated=1');
        exit();
    }
    
    // Session security checks
    if (isset($_SESSION['admin_ip']) && $_SESSION['admin_ip'] !== $_SERVER['REMOTE_ADDR']) {
        session_destroy();
        header('Location: adminLogin.php?security=1');
        exit();
    }
    
    // Regenerate session ID periodically
    if (!isset($_SESSION['last_regeneration']) || 
        (time() - $_SESSION['last_regeneration']) > 300) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// ENFORCE SUPER ADMIN ACCESS ONLY
checkSuperAdminAuth();
?>