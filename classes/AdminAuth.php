<?php
class AdminAuth {
    private $db;
    private $admin_roles = ['super_admin']; // Define which roles are admins
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    // ORIGINAL METHOD - for all admin roles
    public function validateCredentials($email, $password) {
        $stmt = $this->db->prepare("
            SELECT id, name, email, password, role, specialization, course, status 
            FROM users 
            WHERE email = ? AND status = 'active' AND role IN ('" . implode("','", $this->admin_roles) . "')
        ");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $this->logActivity($admin['id'], 'admin_login', 'Successful admin login');
            return ['success' => true, 'admin' => $admin];
        } else {
            $this->logActivity($admin['id'] ?? null, 'admin_login_failed', 'Failed admin login attempt for: ' . $email);
            return ['success' => false, 'message' => 'Invalid credentials or insufficient permissions'];
        }
    }
    
    // SUPER ADMIN ONLY METHOD - STRICTLY ENFORCED
    public function validateSuperAdminCredentials($email, $password) {
        $stmt = $this->db->prepare("
            SELECT id, name, email, password, role, specialization, course, status 
            FROM users 
            WHERE email = ? AND status = 'active' AND role = 'super_admin'
        ");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Double check the role to be absolutely sure
            if ($admin['role'] !== 'super_admin') {
                $this->logActivity($admin['id'], 'unauthorized_access_attempt', 'Non-super-admin tried to access super admin panel');
                return ['success' => false, 'message' => 'Access denied. Super Administrator privileges required.'];
            }
            
            $this->logActivity($admin['id'], 'super_admin_login', 'Successful super admin login');
            return ['success' => true, 'admin' => $admin];
        } else {
            // Log failed attempt with more details
            $this->logActivity($admin['id'] ?? null, 'super_admin_login_failed', 'Failed super admin login attempt for: ' . $email);
            return ['success' => false, 'message' => 'Access denied. Only Super Administrator accounts can access this system.'];
        }
    }
    
    // NEW METHOD - for teachers/advisers only
    public function validateTeacherCredentials($email, $password) {
        $teacher_roles = ['research_director', 'adviser'];
        $stmt = $this->db->prepare("
            SELECT id, name, email, password, role, specialization, course, status 
            FROM users 
            WHERE email = ? AND status = 'active' AND role IN ('" . implode("','", $teacher_roles) . "')
        ");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $this->logActivity($admin['id'], 'teacher_login', 'Successful teacher login');
            return ['success' => true, 'admin' => $admin];
        } else {
            $this->logActivity($admin['id'] ?? null, 'teacher_login_failed', 'Failed teacher login attempt for: ' . $email);
            return ['success' => false, 'message' => 'Invalid credentials or insufficient permissions'];
        }
    }
    
    public function isAdmin($role) {
        return in_array($role, $this->admin_roles);
    }
    
    public function getAdminById($id) {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE id = ? AND status = 'active' AND role IN ('" . implode("','", $this->admin_roles) . "')
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getAllAdmins() {
        $stmt = $this->db->prepare("
            SELECT id, name, email, role, specialization, course, status, created_at, updated_at
            FROM users 
            WHERE role IN ('" . implode("','", $this->admin_roles) . "')
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function createAdmin($data) {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password, role, specialization, course) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'],
            $data['specialization'] ?? null,
            $data['course'] ?? null
        ]);
    }
    
    public function updateAdmin($id, $data) {
        $fields = [];
        $params = [];
        
        if (isset($data['name'])) {
            $fields[] = "name = ?";
            $params[] = $data['name'];
        }
        if (isset($data['email'])) {
            $fields[] = "email = ?";
            $params[] = $data['email'];
        }
        if (isset($data['role'])) {
            $fields[] = "role = ?";
            $params[] = $data['role'];
        }
        if (isset($data['specialization'])) {
            $fields[] = "specialization = ?";
            $params[] = $data['specialization'];
        }
        if (isset($data['course'])) {
            $fields[] = "course = ?";
            $params[] = $data['course'];
        }
        if (isset($data['status'])) {
            $fields[] = "status = ?";
            $params[] = $data['status'];
        }
        if (isset($data['password'])) {
            $fields[] = "password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (empty($fields)) return false;
        
        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function deleteAdmin($id) {
        // Don't actually delete, just deactivate
        $stmt = $this->db->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function hasPermission($admin_role, $required_role) {
        // Define role hierarchy for your system
        $hierarchy = [
            'super_admin' => 3,
            'research_director' => 2,
            'adviser' => 1,
            'critique_english' => 0,
            'critique_statistician' => 0,
            'student' => 0
        ];
        
        return ($hierarchy[$admin_role] ?? 0) >= ($hierarchy[$required_role] ?? 0);
    }
    
    
    // Activity log to database
    private function logActivity($user_id, $action, $details) {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        $stmt = $this->db->prepare("
            INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user_id,
            $action,
            $details,
            $ip_address,
            $user_agent
        ]);
    }

}
?>