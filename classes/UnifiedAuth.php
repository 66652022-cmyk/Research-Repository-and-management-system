<?php
class UnifiedAuth {
    private $db;
    
    public $user_roles = [
        'super_admin' => [
            'level' => 5,
            'dashboard' => '/THESIS/adminDash.php',
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

    private function prepareAndExecute($query, $params = []) {
        $stmt = mysqli_prepare($this->db, $query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($this->db));
        }

        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }

            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
        }

        return $stmt;
    }

    private function fetchAssoc($stmt) {
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    private function fetchAllAssoc($stmt) {
        $result = mysqli_stmt_get_result($stmt);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    
    /**
     * Universal login method for all user types
     */
    public function validateUserCredentials($email, $password) {
        try {
            $stmt = $this->prepareAndExecute("
                SELECT id, name, email, password, role, specialization, course, status, created_at
                FROM users 
                WHERE email = ? AND status = 'active'
            ", [$email]);
            $user = $this->fetchAssoc($stmt);
        } catch (Exception $e) {
            $this->logActivity(null, 'login_failed_user_not_found', 'Login attempt for non-existent user: ' . $email);
            return [
                'success' => false, 
                'message' => 'Invalid email or password.',
                'error_code' => 'USER_NOT_FOUND'
            ];
        }
        
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
     * bug pag nag back galing s admin as adviser
     */
    public function getDashboardUrl($role) {
        return $this->user_roles[$role]['dashboard'] ?? '/THESIS/default_dash.php';
    }
    
    /**
     * Get all users by role
     */
    public function getUsersByRole($role) {
        try {
            $stmt = $this->prepareAndExecute("
                SELECT id, name, email, role, specialization, course, status, created_at, updated_at
                FROM users
                WHERE role = ? AND status = 'active'
                ORDER BY created_at DESC
            ", [$role]);
            return $this->fetchAllAssoc($stmt);
        } catch (Exception $e) {
            error_log("Database error in getUsersByRole: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all users (for admin purposes)
     */
    public function getAllUsers() {
        try {
            $stmt = $this->prepareAndExecute("
                SELECT id, name, email, role, specialization, course, status, created_at, updated_at
                FROM users
                ORDER BY created_at DESC
            ");
            return $this->fetchAllAssoc($stmt);
        } catch (Exception $e) {
            error_log("Database error in getAllUsers: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        try {
            $stmt = $this->prepareAndExecute("
                SELECT * FROM users
                WHERE id = ? AND status = 'active'
            ", [$id]);
            return $this->fetchAssoc($stmt);
        } catch (Exception $e) {
            error_log("Database error in getUserById: " . $e->getMessage());
            return null;
        }
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
    try {
        $stmt = $this->prepareAndExecute("SELECT id FROM users WHERE email = ?", [$data['email']]);
        if ($this->fetchAssoc($stmt)) {
            return ['success' => false, 'message' => 'Email already exists.'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Database error occurred.'];
    }

    try {
        $stmt = $this->prepareAndExecute("
            INSERT INTO users (name, email, password, role, educational_attainment, specialization, course, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW())
        ", [
            trim($data['name']),
            trim($data['email']),
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'],
            $data['educational_attainment'] ?? null,
            $data['specialization'] ?? null,
            $data['course'] ?? null
        ]);

        $userId = mysqli_insert_id($this->db);
        $this->logActivity($userId, 'user_created', 'New user created with role: ' . $data['role']);
        return ['success' => true, 'user_id' => $userId];

    } catch (Exception $e) {
        error_log("Database error in createUser: " . $e->getMessage());

        // Check for specific constraint violations
        if (mysqli_errno($this->db) == 1062) {
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

        try {
            $this->prepareAndExecute($sql, $params);
            $this->logActivity($id, 'user_updated', 'User profile updated');
            return ['success' => true];
        } catch (Exception $e) {
            error_log("Database error in updateUser: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update user.'];
        }
    }

    /**
     * Soft delete user (deactivate)
     */
    public function deleteUser($id) {
        try {
            $this->prepareAndExecute("UPDATE users SET status = 'inactive', updated_at = NOW() WHERE id = ?", [$id]);
            $this->logActivity($id, 'user_deactivated', 'User account deactivated');
            return ['success' => true];
        } catch (Exception $e) {
            error_log("Database error in deleteUser: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to deactivate user.'];
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        try {
            $this->prepareAndExecute("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?", [$hashedPassword, $id]);
            $this->logActivity($id, 'password_reset', 'Password reset by administrator');
            return ['success' => true];
        } catch (Exception $e) {
            error_log("Database error in resetPassword: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to reset password.'];
        }
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

            $this->prepareAndExecute("
                INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ", [
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
        try {
            $stmt = $this->prepareAndExecute("
                SELECT action, details, ip_address, created_at
                FROM activity_logs
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT ?
            ", [$userId, $limit]);
            return $this->fetchAllAssoc($stmt);
        } catch (Exception $e) {
            error_log("Database error in getUserActivityLogs: " . $e->getMessage());
            return [];
        }
    }
}
?>