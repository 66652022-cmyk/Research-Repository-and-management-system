<?php
require_once '../config/database.php'; 
require_once 'UnifiedAuth.php';  

$auth = new UnifiedAuth();

// Define new admin details
$newAdmin = [
    'name' => 'English Critique1',
    'email' => 'critique1@example.com',
    'password' => 'password123',     
    'role' => 'critique_english',
    'specialization' => null,
    'course' => null
];

// Attempt to create admin
$result = $auth->createUser($newAdmin);

// Check result and output proper message
if ($result['success']) {
    echo "✅ New admin created successfully! Admin ID: " . $result['user_id'];
} else {
    echo "❌ Failed to create new admin: " . ($result['message'] ?? 'Unknown error');
}
