<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /THESIS/pages/login.php');
    exit;
}

if (!in_array($_SESSION['user_role'], ['students', 'super_admin'])) {
    header('Location: /THESIS/pages/Login.php');
    exit;
}

$hour = date('H');
$greeting = $hour < 12 ? 'Good morning!' : ($hour < 18 ? 'Good afternoon!' : 'Good evening!');

// Student-specific stats
$stats = [
    'thesis_progress' => 75,     // Overall thesis completion percentage
    'pending_reviews' => 2,      // Documents awaiting feedback
    'approved_chapters' => 3,    // Approved chapters
    'upcoming_deadlines' => 4    // Upcoming submission deadlines
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="/THESIS/src/output.css">
    <style>
        /* Custom Royal Blue Theme */
        .bg-royal-blue { background-color: #4169E1; }
        .bg-royal-blue-dark { background-color: #1E3A8A; }
        .bg-royal-blue-light { background-color: #6366F1; }
        .hover\:bg-royal-blue:hover { background-color: #4169E1; }
        .hover\:bg-royal-blue-dark:hover { background-color: #1E3A8A; }
        .hover\:bg-royal-blue-light:hover { background-color: #6366F1; }
        .text-royal-blue { color: #4169E1; }
        .border-royal-blue { border-color: #4169E1; }
        
        /* Sidebar positioning and animations */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background-color: #1E3A8A;
            transform: translateX(0);
            transition: transform 0.3s ease-in-out;
            z-index: 50;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .sidebar.closed {
            transform: translateX(-100%);
        }
        
        /* Main content adjustment */
        .main-content {
            margin-left: 280px;
            transition: margin-left 0.3s ease-in-out;
        }
        
        .main-content.sidebar-closed {
            margin-left: 0;
        }
        
        /* Header adjustment */
        .header {
            margin-left: 280px;
            transition: margin-left 0.3s ease-in-out;
        }
        
        .header.sidebar-closed {
            margin-left: 0;
        }
        
        /* Overlay */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 40;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
        }
        
        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content,
            .header {
                margin-left: 0;
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .sidebar-overlay.mobile-show {
                opacity: 1;
                visibility: visible;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Sidebar -->
    <div id="sidebar-overlay" class="sidebar-overlay"></div>
    <div id="sidebar" class="sidebar">
        <div class="p-6">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl font-semibold text-white">Menu</h2>
                <button id="close-sidebar" class="text-white hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <nav class="space-y-3">
                <a href="#" onclick="showSection('dashboard')" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                    </svg>
                    Dashboard
                </a>
                <a href="#" onclick="showSection('manuscripts')" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6zm2 2h8v2H6V8zm0 3h8v1H6v-1z"></path>
                    </svg>
                    My Manuscripts
                </a>
                <a href="#" onclick="showSection('submissions')" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                    </svg>
                    Submissions
                </a>
                <a href="#" onclick="showSection('feedback')" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Feedback
                </a>
                <a href="#" onclick="showSection('deadlines')" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    Deadlines
                </a>
                <div class="border-t border-royal-blue-light my-4"></div>
                <a href="#" onclick="confirmLogout()" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-red-600 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"></path>
                    </svg>
                    Logout
                </a>
            </nav>
        </div>
    </div>

    <!-- Header -->
    <header id="header" class="header bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <button id="menu-button" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-royal-blue mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="flex-shrink-0">
                        <h1 class="text-3xl font-bold text-gray-900">Student Dashboard</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-600"><?php echo $greeting; ?></p>
                        <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                        <p class="text-sm text-royal-blue">Student</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main id="main-content" class="main-content max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Dashboard Section -->
        <div id="dashboard-section" class="section">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-blue-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Thesis Progress</dt>
                                    <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['thesis_progress']; ?>%</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-yellow-400">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Reviews</dt>
                                    <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['pending_reviews']; ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-green-400">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Approved Chapters</dt>
                                    <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['approved_chapters']; ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-purple-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Deadlines</dt>
                                    <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['upcoming_deadlines']; ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-lg rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button onclick="showSection('manuscripts')" class="bg-royal-blue hover:bg-royal-blue-dark text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6zm2 2h8v2H6V8zm0 3h8v1H6v-1z"></path>
                            </svg>
                            Upload Manuscript
                        </button>
                        <button onclick="showSection('submissions')" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            View Submissions
                        </button>
                        <button onclick="showSection('feedback')" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            Check Feedback
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3 p-3 rounded-lg bg-gray-50">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Chapter 3 submitted for review</p>
                                <p class="text-xs text-gray-500">2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 p-3 rounded-lg bg-gray-50">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Chapter 1 approved by advisor</p>
                                <p class="text-xs text-gray-500">1 day ago</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 p-3 rounded-lg bg-gray-50">
                            <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Feedback received on methodology section</p>
                                <p class="text-xs text-gray-500">2 days ago</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 p-3 rounded-lg bg-gray-50">
                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Proposal presentation scheduled</p>
                                <p class="text-xs text-gray-500">3 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manuscripts Section -->
        <div id="manuscripts-section" class="section hidden">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">My Manuscripts</h2>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <p class="text-gray-600">Manuscript management interface will be implemented here.</p>
            </div>
        </div>

        <!-- Submissions Section -->
        <div id="submissions-section" class="section hidden">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">My Submissions</h2>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <p class="text-gray-600">Submission tracking interface will be implemented here.</p>
            </div>
        </div>

        <!-- Feedback Section -->
        <div id="feedback-section" class="section hidden">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Feedback & Reviews</h2>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <p class="text-gray-600">Feedback management interface will be implemented here.</p>
            </div>
        </div>

        <!-- Deadlines Section -->
        <div id="deadlines-section" class="section hidden">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Important Deadlines</h2>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <p class="text-gray-600">Deadline tracking interface will be implemented here.</p>
            </div>
        </div>
    </main>

    <script>
        const menuButton = document.getElementById('menu-button');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const closeSidebar = document.getElementById('close-sidebar');
        const mainContent = document.getElementById('main-content');
        const header = document.getElementById('header');

        let sidebarOpen = true; // Initially open on desktop
        let isMobile = window.innerWidth < 768;

        // Check if mobile on window resize
        window.addEventListener('resize', () => {
            const wasMobile = isMobile;
            isMobile = window.innerWidth < 768;
            
            if (wasMobile !== isMobile) {
                initializeSidebar();
            }
        });

        function initializeSidebar() {
            if (isMobile) {
                // Mobile: sidebar closed by default
                sidebar.classList.add('closed');
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('mobile-show');
                mainContent.classList.add('sidebar-closed');
                header.classList.add('sidebar-closed');
                sidebarOpen = false;
            } else {
                // Desktop: sidebar open by default
                sidebar.classList.remove('closed', 'mobile-open');
                sidebarOverlay.classList.remove('show', 'mobile-show');
                mainContent.classList.remove('sidebar-closed');
                header.classList.remove('sidebar-closed');
                sidebarOpen = true;
            }
        }

        function toggleSidebar() {
            if (isMobile) {
                // Mobile toggle
                if (sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('mobile-open');
                    sidebarOverlay.classList.remove('mobile-show');
                    sidebarOpen = false;
                } else {
                    sidebar.classList.add('mobile-open');
                    sidebarOverlay.classList.add('mobile-show');
                    sidebarOpen = true;
                }
            } else {
                // Desktop toggle
                if (sidebarOpen) {
                    sidebar.classList.add('closed');
                    mainContent.classList.add('sidebar-closed');
                    header.classList.add('sidebar-closed');
                    sidebarOpen = false;
                } else {
                    sidebar.classList.remove('closed');
                    mainContent.classList.remove('sidebar-closed');
                    header.classList.remove('sidebar-closed');
                    sidebarOpen = true;
                }
            }
        }

        function closeSidebarFn() {
            if (isMobile) {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('mobile-show');
            } else {
                sidebar.classList.add('closed');
                mainContent.classList.add('sidebar-closed');
                header.classList.add('sidebar-closed');
            }
            sidebarOpen = false;
        }

        // Event listeners
        menuButton.addEventListener('click', toggleSidebar);
        closeSidebar.addEventListener('click', closeSidebarFn);
        sidebarOverlay.addEventListener('click', closeSidebarFn);

        function showSection(sectionName) {
            document.querySelectorAll('.section').forEach(section => {
                section.classList.add('hidden');
            });
            
            const targetSection = document.getElementById(sectionName + '-section');
            if (targetSection) {
                targetSection.classList.remove('hidden');
            }
            
            // Close sidebar on mobile when navigating
            if (isMobile) {
                closeSidebarFn();
            }
        }

        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = '../classes/LogoutHandling.php';
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isMobile && sidebarOpen) {
                closeSidebarFn();
            }
        });

        // Initialize sidebar state on page load
        initializeSidebar();
    </script>
</body>
</html>