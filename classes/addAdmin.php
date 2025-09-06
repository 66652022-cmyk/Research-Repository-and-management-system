<?php
require_once '../config/database.php'; 
require_once 'AdminAuth.php';  
$auth = new AdminAuth();

$newAdmin = [
    'name' => 'Admin 1',
    'email' => 'admin@example.com',
    'password' => 'password123',     
    'role' => 'super_admin',
    'specialization' => null,
    'course' => null
];
// ilagay sa browser yung url nato for new admin creation
if ($auth->createAdmin($newAdmin)) {
    echo "New admin created successfully!";
} else {
    echo "Failed to create new admin.";
}
