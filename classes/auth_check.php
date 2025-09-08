<?php
/**
 * Session Management and Authentication Check
 * Include this file at the top of any protected page
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';
require_once 'classes/UnifiedAuth.php';

class SessionManager {
    private $auth;
    private $sessionTimeout = 3600; // 1 hour in seconds
    private $maxInactivity = 1800; // 30 minutes in seconds
    
    public function __construct() {
        $this->auth = new UnifiedAuth();
    }
    
    /**
     * Check if user is authenticated and session is valid
     */
    public function requireAuth($requiredRole = null, $minimumLevel = null) {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin('access_denied');
            return false;
        }
        
        // Check session validity
        if (!$this->isSessionValid()) {
            $this->destroySession();
            $this->redirectToLogin('timeout');
            return false;
        }
        
        // Check role-based access
        if ($requiredRole && !$this->hasRoleAccess($requiredRole)) {
            $this->redirectToLogin('insufficient_privileges');
            return false;
        }
        
        // Check minimum level access
        if ($minimumLevel && !$this->hasMinimumLevel($minimumLevel)) {
            $this->redirectToLogin('insufficient_privileges');
            return false;
        }
        
        // Update last activity
        $this->updateActivity();
        
        return true;
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_logged_in']) && 
               $_SESSION['user_logged_in'] === true &&
               isset($_SESSION['user_id']) &&
               isset($_SESSION['user_role']);
    }
    
    /**
     * Check if session is still valid
     */
    public function isSessionValid() {
        // Check if session has expired
        if (isset($_SESSION['user_login_time'])) {
            $loginTime = $_SESSION['user_login_time'];
            if ((time() - $loginTime) > $this->sessionTimeout) {
                return false;
            }
        }
        
        // Check for inactivity timeout
        if (isset($_SESSION['user_last_activity'])) {
            $lastActivity = $_SESSION['user_last_activity'];
            if ((time() - $lastActivity) > $this->maxInactivity) {
                return false;
            }
        }
        
        // Check IP consistency (optional security measure)
        if (isset($_SESSION['user_ip'])) {
            $currentIP = $_SERVER['REMOTE_ADDR'];
            if ($_SESSION['user_ip'] !== $currentIP) {
                // IP changed - potential security issue
                error_log("Session IP mismatch for user " . ($_SESSION['user_id'] ?? 'unknown') . 
                         ". Session IP: " . $_SESSION['user_ip'] . ", Current IP: " . $currentIP);
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check if user has required role access
     */
    public function hasRoleAccess($requiredRole) {
        if (!isset($_SESSION['user_role'])) {
            return false;
        }
        
        return $this->auth->hasRoleAccess($_SESSION['user_role'], $requiredRole);
    }
    
    /**
     * Check if user has minimum access level
     */
    public function hasMinimumLevel($minimumLevel) {
        if (!isset($_SESSION['user_role'])) {
            return false;
        }
        
        return $this->auth->hasMinimumLevel($_SESSION['user_role'], $minimumLevel);
    }
    
    /**
     * Update user's last activity timestamp
     */
    public function updateActivity() {
        $_SESSION['user_last_activity'] = time();
    }
    
    /**
     * Get current user information
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'],
            'role_name' => $_SESSION['user_role_name'],
            'role_level' => $_SESSION['user_role_level'],
            'specialization' => $_SESSION['user_specialization'] ?? null,
            'course' => $_SESSION['user_course'] ?? null,
            'login_time' => $_SESSION['user_login_time'],
            'last_activity' => $_SESSION['user_last_activity']
        ];
    }
    
    /**
     * Get current user's role
     */
    public function getCurrentUserRole() {
        return $_SESSION['user_role'] ?? null;
    }
    
    /**
     * Get current user's ID
     */
    public function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get current user's name
     */
    public function getCurrentUserName() {
        return $_SESSION['user_name'] ?? null;
    }
    
    /**
     * Get current user's role level
     */
    public function getCurrentUserLevel() {
        return $_SESSION['user_role_level'] ?? 0;
    }
    
    /**
     * Check if current user is admin or super admin
     */
    public function isAdmin() {
        $adminRoles = ['super_admin', 'research_director'];
        return in_array($_SESSION['user_role'] ?? '', $adminRoles);
    }
    
    /**
     * Check if current user is faculty
     */
    public function isFaculty() {
        $facultyRoles = ['research_faculty', 'research_adviser'];
        return in_array($_SESSION['user_role'] ?? '', $facultyRoles);
    }
    
    /**
     * Check if current user is student
     */
    public function isStudent() {
        return ($_SESSION['user_role'] ?? '') === 'student';
    }
    
    /**
     * Check if current user is specialist (critique roles)
     */
    public function isSpecialist() {
        $specialistRoles = ['english_critique', 'statistician', 'financial_critique'];
        return in_array($_SESSION['user_role'] ?? '', $specialistRoles);
    }
    
    /**
     * Refresh user data from database
     */
    public function refreshUserData() {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $user = $this->auth->getUserById($_SESSION['user_id']);
        if (!$user) {
            $this->destroySession();
            return false;
        }
        
        // Update session with fresh data
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_specialization'] = $user['specialization'];
        $_SESSION['user_course'] = $user['course'];
        
        // Update role-related data
        $roles = $this->auth->getAvailableRoles();
        foreach ($roles as $role) {
            if ($role['value'] === $user['role']) {
                $_SESSION['user_role_name'] = $role['name'];
                $_SESSION['user_role_level'] = $role['level'];
                break;
            }
        }
        
        return true;
    }
    
    /**
     * Set session timeout values
     */
    public function setTimeouts($sessionTimeout = null, $inactivityTimeout = null) {
        if ($sessionTimeout !== null) {
            $this->sessionTimeout = $sessionTimeout;
        }
        if ($inactivityTimeout !== null) {
            $this->maxInactivity = $inactivityTimeout;
        }
    }
    
    /**
     * Get session information
     */
    public function getSessionInfo() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $loginTime = $_SESSION['user_login_time'] ?? time();
        $lastActivity = $_SESSION['user_last_activity'] ?? time();
        $currentTime = time();
        
        return [
            'session_duration' => $currentTime - $loginTime,
            'time_since_activity' => $currentTime - $lastActivity,
            'session_expires_in' => $this->sessionTimeout - ($currentTime - $loginTime),
            'inactivity_expires_in' => $this->maxInactivity - ($currentTime - $lastActivity),
            'ip_address' => $_SESSION['user_ip'] ?? 'unknown',
            'login_time' => date('Y-m-d H:i:s', $loginTime),
            'last_activity' => date('Y-m-d H:i:s', $lastActivity)
        ];
    }
    
    /**
     * Destroy session and logout user
     */
    public function destroySession() {
        // Log the logout activity
        if (isset($_SESSION['user_id'])) {
            $this->auth->logActivity($_SESSION['user_id'], 'logout', 'User logged out');
        }
        
        // Clear all session data
        $_SESSION = [];
        
        // Destroy session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
    }

    public function redirectToLogin($message = '') {
        $loginUrl = 'login.php';
        
        if (!empty($message)) {
            $loginUrl .= '?' . $message;
        }
        
        // If it's an AJAX request, send JSON response instead of redirect
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Session expired. Please log in again.',
                'redirect' => $loginUrl
            ]);
            exit();
        }
        
        header('Location: ' . $loginUrl);
        exit();
    }

    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }
    

    public function logSecurityEvent($event, $details = '') {
        $userId = $_SESSION['user_id'] ?? null;
        $this->auth->logActivity($userId, 'security_' . $event, $details);
        
        // Also log to error log for critical events
        $criticalEvents = ['session_hijack', 'ip_change', 'invalid_csrf', 'brute_force'];
        if (in_array($event, $criticalEvents)) {
            error_log("SECURITY: $event - User: " . ($userId ?? 'unknown') . " - Details: $details");
        }
    }
    
    /**
     * Extend session (useful for "remember me" functionality)
     */
    public function extendSession($additionalTime = 3600) {
        if ($this->isLoggedIn()) {
            $_SESSION['user_login_time'] = time() - ($this->sessionTimeout - $additionalTime);
            $this->updateActivity();
        }
    }
    
    /**
     * Check if session will expire soon
     */
    public function isSessionExpiringSoon($warningMinutes = 5) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $sessionInfo = $this->getSessionInfo();
        $warningSeconds = $warningMinutes * 60;
        
        return ($sessionInfo['session_expires_in'] <= $warningSeconds) || 
               ($sessionInfo['inactivity_expires_in'] <= $warningSeconds);
    }
}

// Usage example at the top of protected pages:
/*
require_once 'classes/SessionManager.php';

$sessionManager = new SessionManager();

// For pages that require any authenticated user
$sessionManager->requireAuth();

// For pages that require specific role
$sessionManager->requireAuth('research_faculty');

// For pages that require minimum access level
$sessionManager->requireAuth(null, 3); // Level 3 or higher

// For pages that require both
$sessionManager->requireAuth('research_director', 4);

// Get current user info
$currentUser = $sessionManager->getCurrentUser();
*/
?>