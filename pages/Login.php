<?php
session_start();
require_once '../config/database.php';
require_once '../classes/UnifiedAuth.php';

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    $auth = new UnifiedAuth();
    $dashboardUrl = $auth->getDashboardUrl($_SESSION['user_role']);

    // Force absolute URL
    if (!preg_match('#^https?://#', $dashboardUrl)) {
        $host = $_SERVER['HTTP_HOST'];
        $dashboardUrl = "http://{$host}{$dashboardUrl}";
    }

    // Normalize current script name
    $currentPage = basename($_SERVER['PHP_SELF']);
    $targetPage  = basename(parse_url($dashboardUrl, PHP_URL_PATH));

    error_log("Redirect check: currentPage={$currentPage} targetPage={$targetPage}");

    if ($currentPage !== $targetPage) {
        header("Location: " . $dashboardUrl);
        exit();
    }
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
            
            // Format dashboard URL properly
            $dashboardUrl = $result['dashboard'];
            if (!str_starts_with($dashboardUrl, '/') && !str_starts_with($dashboardUrl, 'http')) {
                $dashboardUrl = '/THESIS/' . $dashboardUrl;
            }
            
            // Redirect to appropriate dashboard
            header('Location: ' . $dashboardUrl);
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
    <style>
        #googleSignInBtn {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }

        .custom-google-btn {
            display: none;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #dadce0;
            border-radius: 4px;
            color: #3c4043;
            cursor: not-allowed;
            font-family: 'Roboto', arial, sans-serif;
            font-size: 14px;
            height: 40px;
            letter-spacing: 0.25px;
            padding: 0 12px;
            text-align: center;
            min-width: 54px;
            opacity: 0.6;
            transition: all 0.2s ease;
        }
        
        .custom-google-btn:hover {
            box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.30), 0 1px 3px 1px rgba(60, 64, 67, 0.15);
        }
        
        .google-icon {
            width: 18px;
            height: 18px;
            margin-right: 8px;
        }
        
        /* Loading spinner animation */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #4285f4;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-600 to-blue-900 min-h-screen font-sans">
    <!-- Navigation Bar -->
    <?php include "../components/navbar.php" ?>
    
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
                <h1 class="text-2xl font-bold text-gray-800">Welcome!</h1>
                <p class="text-gray-500 text-sm">Research Management System</p>
                <div class="mt-4 flex flex-wrap justify-center gap-2 text-xs">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Students</span>
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full">Faculty</span>
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

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or continue with</span>
                    </div>
                </div>

                <!-- Google Sign-In Section -->
                <div class="space-y-4">
                    <!-- Google Sign-In Button Container -->
                    <div class="flex justify-center items-center min-h-[50px]">
                        <div id="googleSignInBtn" class="w-full flex justify-center"></div>
                        
                        <!-- Offline Google Button -->
                        <button id="offlineGoogleBtn" class="custom-google-btn hidden">
                            <svg class="google-icon" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Sign in with Google (Offline)
                        </button>
                    </div>

                    <!-- Status Message -->
                    <div id="statusMessage" class="hidden text-center text-sm p-3 rounded-lg">
                        <div class="flex items-center justify-center space-x-2">
                            <div class="loading-spinner"></div>
                            <span>Loading Google Sign-In...</span>
                        </div>
                    </div>
                </div>
            </form>

            <div class="mt-8 text-center space-y-4">
                <div class="text-xs text-gray-500 bg-gray-50 border border-gray-200 p-4 rounded-lg">
                    <div class="font-semibold text-gray-700 mb-2">System Access:</div>
                    <div class="space-y-1">
                        <p>• <strong>Students:</strong> Submit and track research progress</p>
                        <p>• <strong>Faculty:</strong> Review and guide student research</p>
                        <p>• <strong>Administrators:</strong> Review research and teacher management</p>
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
                    <a href="/THESIS/index.php" class="text-sm text-gray-500 hover:text-gray-700 hover:underline transition-colors duration-200">
                        ← Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Include your existing Google login handler -->
    <script src="../js/handleGoogleLogin.js"></script>
    
    <!-- Google Sign-In Library -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <!-- Enhanced JavaScript for connection status only -->
    <script>
        // Connection status management
        let isOnline = navigator.onLine;

        function updateConnectionStatus() {
            const statusEl = document.getElementById('statusMessage');
            const googleBtnContainer = document.getElementById('googleSignInBtn');
            const offlineBtn = document.getElementById('offlineGoogleBtn');

            if (!isOnline) {
                // Show offline state
                statusEl.className = 'block text-center text-sm p-3 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800';
                statusEl.innerHTML = '⚠️ No internet connection. Google Sign-In unavailable.';
                statusEl.classList.remove('hidden');
                
                googleBtnContainer.classList.add('hidden');
                offlineBtn.classList.remove('hidden');
                offlineBtn.classList.add('flex');
            } else {
                // Hide offline elements when online
                statusEl.classList.add('hidden');
                offlineBtn.classList.add('hidden');
                googleBtnContainer.classList.remove('hidden');
            }
        }

        // Event listeners for connection changes
        window.addEventListener('online', () => {
            isOnline = true;
            console.log('Connection restored');
            updateConnectionStatus();
        });

        window.addEventListener('offline', () => {
            isOnline = false;
            console.log('Connection lost');
            updateConnectionStatus();
        });

        // Initialize connection status check
        document.addEventListener('DOMContentLoaded', function() {
            updateConnectionStatus();
        });
    </script>
</body>
</html>