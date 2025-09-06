<?php
session_start();
require_once 'config/database.php';
require_once 'classes/UnifiedAuth.php';

// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    $auth = new UnifiedAuth();
    $dashboardUrl = $auth->getDashboardUrl($_SESSION['user_role']);
    header('Location: ' . $dashboardUrl);
    exit();
}

$error = '';
$success = '';
$auth = new UnifiedAuth();

if ($_POST) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } else {
        $result = $auth->validateUserCredentials($email, $password);
        
        if ($result['success']) {
            $user = $result['user'];
            
            // Set universal session variables
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_role_name'] = $result['role_name'];
            $_SESSION['user_role_level'] = $result['role_level'];
            $_SESSION['user_specialization'] = $user['specialization'];
            $_SESSION['user_course'] = $user['course'];
            $_SESSION['user_last_activity'] = time();
            $_SESSION['user_login_time'] = time();
            $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
            
            // Redirect to appropriate dashboard
            header('Location: ' . $result['dashboard']);
            exit();
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Research Management System</title>
    <link rel="stylesheet" href="/THESIS/src/output.css">
</head>
<body class="bg-gradient-to-br from-blue-600 to-blue-900 min-h-screen font-sans">
    <!-- Navigation Bar -->
    <?php include "components/navbar.php" ?>
    
    <!-- Main Content Container -->
    <div class="min-h-screen pt-16 pb-8 flex items-center justify-center px-4">
        <div class="w-full max-w-md bg-white shadow-2xl rounded-2xl p-8 border-t-4 border-blue-700">
            <div class="text-center mb-8">
                <div class="mb-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Welcome Back</h1>
                <p class="text-gray-500 text-sm">Research Management System</p>
                <div class="mt-4 flex flex-wrap justify-center gap-2 text-xs">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Students</span>
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full">Faculty</span>
                    <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full">Administrators</span>
                </div>
            </div>

            <!-- Error Messages -->
            <?php if ($error): ?>
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm"><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Success Messages -->
            <?php if ($success): ?>
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 text-green-700 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm"><?php echo htmlspecialchars($success); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- URL Parameter Messages -->
            <?php if (isset($_GET['timeout'])): ?>
                <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-4 rounded-lg">
                    <p class="text-sm">Your session has expired. Please log in again.</p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['logout'])): ?>
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 text-green-700 p-4 rounded-lg">
                    <p class="text-sm">You have been logged out successfully.</p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['access_denied'])): ?>
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-lg">
                    <p class="text-sm">Access denied. Please log in to continue.</p>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input 
                            type="email" 
                            id="email" 
                            name="email"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            placeholder="Enter your email address"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200"
                            required
                            autocomplete="email"
                        >
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Enter your password"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200"
                            required
                            autocomplete="current-password"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember-me" 
                            name="remember-me" 
                            type="checkbox" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="forgot-password.php" class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200">
                            Forgot password?
                        </a>
                    </div>
                </div>

                <button 
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl"
                >
                    Sign In
                </button>
            </form>

            <!-- Additional Information -->
            <div class="mt-8 text-center space-y-4">
                <div class="text-xs text-gray-500 bg-gray-50 border border-gray-200 p-4 rounded-lg">
                    <div class="font-semibold text-gray-700 mb-2">System Access:</div>
                    <div class="space-y-1">
                        <p>• <strong>Students:</strong> Submit and track research progress</p>
                        <p>• <strong>Faculty:</strong> Review and guide student research</p>
                        <p>• <strong>Administrators:</strong> Full system management</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <p class="text-sm text-gray-500">
                        Don't have an account? 
                        <a href="register.php" class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200 font-medium">
                            Contact your administrator
                        </a>
                    </p>
                </div>

                <div>
                    <a href="index.php" class="text-sm text-gray-500 hover:text-gray-700 hover:underline transition-colors duration-200">
                        ← Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>