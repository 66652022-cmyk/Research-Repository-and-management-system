<?php
class UnifiedAuth {
    private $db;
    
    public $user_roles = [
        'super_admin' => [
            'level' => 5,
            'dashboard' => 'adminDash.php',
            'name' => 'Super Administrator'
        ],
        'research_director' => [
            'level' => 4,
            'dashboard' => '/THESIS/dashboards/research_director_dash.php',
            'name' => 'Research Director'
        ],
        'research_faculty' => [
            'level' => 3,
            'dashboard' => '/THESIS/dashboards/faculty_dash.php',
            'name' => 'Research Faculty'
        ],
        'adviser' => [
            'level' => 3,
            'dashboard' => '/THESIS/dashboards/adviser_dash.php',
            'name' => 'Research Adviser'
        ],
        'critique_english' => [
            'level' => 2,
            'dashboard' => '/THESIS/dashboards/critique_dash.php',
            'name' => 'English Critique'
        ],
        'critique_statistician' => [
            'level' => 2,
            'dashboard' => '/THESIS/dashboards/statistician_dash.php',
            'name' => 'Statistician'
        ],
        'financial_critique' => [
            'level' => 2,
            'dashboard' => '/THESIS/dashboards/finance_dash.php',
            'name' => 'Financial Critique'
        ],
        'student' => [
            'level' => 1,
            'dashboard' => '/THESIS/dashboards/student_dash.php',
            'name' => 'Student'
        ]
    ];
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    /**
     * Universal login method for all user types
     */
    public function validateUserCredentials($email, $password) {
        $stmt = $this->db->prepare("
            SELECT id, name, email, password, role, specialization, course, status, created_at
            FROM users 
            WHERE email = ? AND status = 'active'
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $this->logActivity(null, 'login_failed_user_not_found', 'Login attempt for non-existent user: ' . $email);
            return [
                'success' => false, 
                'message' => 'Invalid email or password.',
                'error_code' => 'USER_NOT_FOUND'
            ];
        }
        
        if (!password_verify($password, $user['password'])) {
            $this->logActivity($user['id'], 'login_failed_wrong_password', 'Wrong password for user: ' . $email);
            return [
                'success' => false, 
                'message' => 'Invalid email or password.',
                'error_code' => 'WRONG_PASSWORD'
            ];
        }
        
        // Check if role is valid
        if (!isset($this->user_roles[$user['role']])) {
            $this->logActivity($user['id'], 'login_failed_invalid_role', 'User with invalid role attempted login: ' . $user['role']);
            return [
                'success' => false, 
                'message' => 'Account role is not properly configured. Contact administrator.',
                'error_code' => 'INVALID_ROLE'
            ];
        }
        
        // Successful login
        $this->logActivity($user['id'], 'login_success', 'Successful login for role: ' . $user['role']);
        
        return [
            'success' => true, 
            'user' => $user,
            'dashboard' => $this->user_roles[$user['role']]['dashboard'],
            'role_name' => $this->user_roles[$user['role']]['name'],
            'role_level' => $this->user_roles[$user['role']]['level']
        ];
    }
    
    /**
     * Check if user has specific role access
     */
    public function hasRoleAccess($userRole, $requiredRole) {
        if (!isset($this->user_roles[$userRole]) || !isset($this->user_roles[$requiredRole])) {
            return false;
        }
        
        return $this->user_roles[$userRole]['level'] >= $this->user_roles[$requiredRole]['level'];
    }
    
    /**
     * Check if user has minimum access level
     */
    public function hasMinimumLevel($userRole, $minimumLevel) {
        if (!isset($this->user_roles[$userRole])) {
            return false;
        }
        
        return $this->user_roles[$userRole]['level'] >= $minimumLevel;
    }
    
    /**
     * Get user's dashboard URL
     */
    public function getDashboardUrl($role) {
        return $this->user_roles[$role]['dashboard'] ?? '/THESIS/default_dash.php';
    }
    
    /**
     * Get all users by role
     */
    public function getUsersByRole($role) {
        $stmt = $this->db->prepare("
            SELECT id, name, email, role, specialization, course, status, created_at, updated_at
            FROM users 
            WHERE role = ? AND status = 'active'
            ORDER BY created_at DESC
        ");
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all users (for admin purposes)
     */
    public function getAllUsers() {
        $stmt = $this->db->prepare("
            SELECT id, name, email, role, specialization, course, status, created_at, updated_at
            FROM users 
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE id = ? AND status = 'active'
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create new user
     */
    public function createUser($data) {
    // Set default password if not provided
    if (empty($data['password'])) {
        $data['password'] = 'pssword123';
    }
    
    // Valid roles based on your database enum
    $validRoles = ['super_admin', 'research_faculty', 'financial_critique', 'research_director', 'adviser', 'critique_english', 'critique_statistician', 'student'];
    
    if (!in_array($data['role'], $validRoles)) {
        return ['success' => false, 'message' => 'Invalid role specified.'];
    }

    // Validate required fields
    if (empty($data['name']) || empty($data['email'])) {
        return ['success' => false, 'message' => 'Name and email are required.'];
    }

    // Check if email already exists
    $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Email already exists.'];
    }

    try {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password, role, educational_attainment, specialization, course, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW())
        ");

        $success = $stmt->execute([
            trim($data['name']),
            trim($data['email']),
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'],
            $data['educational_attainment'] ?? null,
            $data['specialization'] ?? null,
            $data['course'] ?? null
        ]);

        if ($success) {
            $userId = $this->db->lastInsertId();
            $this->logActivity($userId, 'user_created', 'New user created with role: ' . $data['role']);
            return ['success' => true, 'user_id' => $userId];
        }

        return ['success' => false, 'message' => 'Failed to create user.'];

    } catch (PDOException $e) {
        error_log("Database error in createUser: " . $e->getMessage());
        
        // Check for specific constraint violations
        if ($e->getCode() == 23000) {
            return ['success' => false, 'message' => 'Email already exists or invalid data.'];
        }
        
        return ['success' => false, 'message' => 'Database error occurred.'];
    }
}
    
    /**
     * Update user
     */
    public function updateUser($id, $data) {
        $fields = [];
        $params = [];
        
        $allowedFields = ['name', 'email', 'role', 'specialization', 'course', 'status'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (isset($data['password']) && !empty($data['password'])) {
            $fields[] = "password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (empty($fields)) {
            return ['success' => false, 'message' => 'No fields to update.'];
        }
        
        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute($params);
        
        if ($success) {
            $this->logActivity($id, 'user_updated', 'User profile updated');
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Failed to update user.'];
    }
    
    /**
     * Soft delete user (deactivate)
     */
    public function deleteUser($id) {
        $stmt = $this->db->prepare("UPDATE users SET status = 'inactive', updated_at = NOW() WHERE id = ?");
        $success = $stmt->execute([$id]);
        
        if ($success) {
            $this->logActivity($id, 'user_deactivated', 'User account deactivated');
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Failed to deactivate user.'];
    }
    
    /**
     * Reset user password
     */
    public function resetPassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        $success = $stmt->execute([$hashedPassword, $id]);
        
        if ($success) {
            $this->logActivity($id, 'password_reset', 'Password reset by administrator');
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Failed to reset password.'];
    }
    
    /**
     * Get available roles
     */
    public function getAvailableRoles() {
        return array_map(function($role, $config) {
            return [
                'value' => $role,
                'name' => $config['name'],
                'level' => $config['level']
            ];
        }, array_keys($this->user_roles), $this->user_roles);
    }
    
    /**
     * Log user activity
     */
    public function logActivity($user_id, $action, $details) {
        try {
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

            $stmt = $this->db->prepare("
                INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $user_id,
                $action,
                $details,
                $ip_address,
                substr($user_agent, 0, 255) // Limit user agent length
            ]);
        } catch (Exception $e) {
            // Log to error file if database logging fails
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }
    
    /**
     * Get user activity logs
     */
    public function getUserActivityLogs($userId, $limit = 50) {
        $stmt = $this->db->prepare("
            SELECT action, details, ip_address, created_at
            FROM activity_logs 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
}
?>