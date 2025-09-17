<?php
// File: classes/googleLogin.php - Fixed Version
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Only POST requests allowed.'
    ]);
    exit;
}

try {
    // Get input
    $input = file_get_contents("php://input");
    if (!$input) throw new Exception('No input data received');

    $data = json_decode($input, true);
    if (!$data || !isset($data['token']) || empty($data['token'])) throw new Exception('No token provided');

    $token = $data['token'];

    // Verify token with Google
    $client_id = "1027047820121-8ttrsc7g4io22un3o971io4tnj961cbq.apps.googleusercontent.com";
    $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($token);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_error($ch)) {
        curl_close($ch);
        throw new Exception('Failed to verify token with Google');
    }
    curl_close($ch);

    if ($http_code !== 200) throw new Exception('Google API returned error: HTTP ' . $http_code);

    $userinfo = json_decode($response, true);
    if (!$userinfo || isset($userinfo['error'])) throw new Exception('Invalid token');

    // Validate token
    if (!isset($userinfo['email']) || !isset($userinfo['aud']) || $userinfo['aud'] !== $client_id) {
        throw new Exception('Token validation failed');
    }

    $email = $userinfo['email'];
    $name = $userinfo['name'] ?? 'Unknown';

    // Check allowed domain
    if (!str_ends_with(strtolower($email), '@holycross.edu.ph')) {
        echo json_encode([
            'success' => false,
            'message' => 'Only @holycross.edu.ph emails are allowed'
        ]);
        exit;
    }

    // Database operations
    require_once '../config/database.php';
    require_once '../classes/UnifiedAuth.php';

    $database = new Database();
    $db = $database->connect();
    $auth = new UnifiedAuth();

    session_start();

    // Check if user exists
    $stmt = mysqli_prepare($db, "SELECT id, name, role, course, educational_attainment, specialization FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        // New student user
        $stmt = mysqli_prepare($db, "INSERT INTO users (name, email, role, status) VALUES (?, ?, 'student', 'active')");
        mysqli_stmt_bind_param($stmt, 'ss', $name, $email);
        mysqli_stmt_execute($stmt);

        $_SESSION['user_id'] = mysqli_insert_id($db);
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = 'student';

        echo json_encode([
            'success' => true,
            'needs_profile' => true,
            'role' => 'student',
            'message' => 'New user created - profile completion required'
        ]);
        exit;
    }

    // Existing user
    $role = $user['role'] === 'students' ? 'student' : $user['role'];

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $role;

    // Admins and Research Directors do not need profile completion
    $needs_profile = false;
    if (!in_array($role, ['super_admin', 'research_director'])) {
        if ($role === 'student') {
            $needs_profile = empty($user['course']);
        } else {
            $needs_profile = empty($user['educational_attainment']) || empty($user['specialization']);
        }
    }

    // Get dashboard URL if profile complete
    $dashboard = $needs_profile ? null : $auth->getDashboardUrl($role);

    echo json_encode([
        'success' => true,
        'needs_profile' => $needs_profile,
        'role' => $role,
        'dashboard' => $dashboard,
        'message' => $needs_profile ? 'Profile completion required' : 'Login successful'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
