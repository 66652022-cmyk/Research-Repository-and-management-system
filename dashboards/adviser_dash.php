<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /THESIS/pages/Login.php');
    exit;
}

if (!in_array($_SESSION['user_role'], ['adviser', 'super_admin'])) {
    header('Location: /THESIS/pages/Login.php');
    exit;
}
$hour = date('H');
$greeting = $hour < 12 ? 'Good morning!' : ($hour < 18 ? 'Good afternoon!' : 'Good evening!');

if ($_SESSION['user_role'] === 'adviser') {
    // Adviser sees only their own assigned data
    $stats = [
        'assigned_students' => 12,   // e.g. query: SELECT COUNT(*) FROM students WHERE adviser_id = $_SESSION['user_id']
        'pending_reviews'   => 8,
        'approved_proposals'=> 25,
        'pending_submissions'=> 7
    ];
} elseif ($_SESSION['user_role'] === 'super_admin') {
    // Admin sees ALL advisers' data
    $stats = [
        'assigned_students' => 120,  // e.g. query: SELECT COUNT(*) FROM students
        'pending_reviews'   => 30,
        'approved_proposals'=> 200,
        'pending_submissions'=> 50
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Adviser Dashboard</title>
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
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Fixed Navigation Container -->
    <div class="fixed inset-0 z-50 flex pointer-events-none">
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 bg-royal-blue py-4 shadow-lg z-20 pointer-events-auto">
            <div class="container mx-auto px-4 flex justify-between items-center">
                <div class="flex items-center">
                    <button id="burger-menu" class="mr-4 text-white p-2 hover:bg-royal-blue-dark rounded-lg transition-colors duration-200">
                        <svg id="burger-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h1 class="text-white text-xl font-bold">Research Adviser Dashboard</h1>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="text-white text-right">
                        <p class="opacity-90 mb-1"><?php echo $greeting; ?></p>
                        <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong><br>
                        <small class="text-sm">Research Adviser</small><br>
                    </div>
                </div>
            </div>
        </header>

        <!-- Sidebar Overlay for mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-opacity-50 hidden pointer-events-auto lg:hidden" onclick="toggleSidebar()"></div>

        <!-- Sidebar - Initially open on desktop -->
        <aside id="sidebar" class="fixed top-16 left-0 w-64 bg-royal-blue-dark text-white shadow-lg transform transition-transform duration-300 ease-in-out z-10 pointer-events-auto overflow-y-auto" style="height: calc(100vh - 4rem);">
            <div class="p-4 relative">
                <!-- Close button - visible on desktop and mobile -->
                <button id="close-sidebar" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                
                <h2 class="text-xl font-semibold mb-6 pt-8">Navigation</h2>
                <nav class="space-y-2" aria-label="Sidebar Navigation">
                    <a href="#" onclick="showSection('dashboard')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors duration-200 bg-royal-blue-light">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="#" onclick="showSection('students')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                        </svg>
                        My Students
                    </a>
                    <a href="#" onclick="showSection('proposals')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                        </svg>
                        Research Proposals
                    </a>
                    <a href="#" onclick="showSection('submissions')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6zm2 2h8v2H6V8zm0 3h8v1H6v-1z"></path>
                        </svg>
                        Student Submissions
                    </a>
                    <a href="#" onclick="showSection('reports')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                        </svg>
                        Reports
                    </a>

                    <div class="border-t border-royal-blue-light my-4"></div>

                    <a href="#" onclick="confirmLogout()" class="nav-item flex items-center p-3 rounded-lg hover:bg-red-600 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"></path>
                        </svg>
                        Logout
                    </a>
                </nav>
            </div>
        </aside>
    </div>

    <!-- Main Content -->
    <div id="contentWrapper" class="pt-16 transition-all duration-300 ease-in-out">
        <main class="min-h-screen bg-gray-50 p-6">
            <!-- Dashboard Section -->
            <div id="dashboard-section" class="section">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Assigned Students</dt>
                                        <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['assigned_students']; ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-lg rounded-lg">
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

                    <div class="bg-white overflow-hidden shadow-lg rounded-lg">
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
                                        <dt class="text-sm font-medium text-gray-500 truncate">Approved Proposals</dt>
                                        <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['approved_proposals']; ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6zm2 2h8v2H6V8zm0 3h8v1H6v-1z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Submissions</dt>
                                        <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['pending_submissions']; ?></dd>
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
                            <button onclick="showSection('submissions')" class="bg-royal-blue hover:bg-royal-blue-dark text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6zm2 2h8v2H6V8zm0 3h8v1H6v-1z"></path>
                                </svg>
                                Review Submissions
                            </button>
                            <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                                </svg>
                                Review Proposal
                            </button>
                            <button class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                                </svg>
                                Generate Report
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
                                    <p class="text-sm font-medium text-gray-900">New file submission from John Doe - Chapter 2.docx</p>
                                    <p class="text-xs text-gray-500">2 hours ago</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 p-3 rounded-lg bg-gray-50">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Research proposal approved for Jane Smith</p>
                                    <p class="text-xs text-gray-500">4 hours ago</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 p-3 rounded-lg bg-gray-50">
                                <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Mike Johnson submitted revised methodology</p>
                                    <p class="text-xs text-gray-500">1 day ago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Sections (Hidden by default) -->
            <div id="students-section" class="section hidden">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">My Students</h2>
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <p class="text-gray-600">Student management interface will be implemented here.</p>
                </div>
            </div>

            <div id="proposals-section" class="section hidden">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Research Proposals</h2>
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <p class="text-gray-600">Research proposal management interface will be implemented here.</p>
                </div>
            </div>

            <div id="submissions-section" class="section hidden">
                <iframe src="/THESIS/pages/group_details.php" 
                        width="100%" height="100%" 
                        style="border:none; min-height:90vh;">
                </iframe>
            </div>

            <div id="reports-section" class="section hidden">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Reports</h2>
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <p class="text-gray-600">Reports interface will be implemented here.</p>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Initialize sidebar state - true means open (for desktop), false means closed (for mobile)
        let isSidebarOpen = false;

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const contentWrapper = document.getElementById('contentWrapper');
            const burgerIcon = document.getElementById('burger-icon');
            
            isSidebarOpen = !isSidebarOpen;
            
            if (isSidebarOpen) {
                // Open sidebar
                sidebar.style.transform = 'translateX(0)';
                
                // Change burger icon to X
                if (burgerIcon) {
                    burgerIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
                }
                
                // Show overlay on mobile only
                if (window.innerWidth < 1024) {
                    overlay.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
                
                // Adjust content wrapper
                if (contentWrapper && window.innerWidth >= 1024) {
                    contentWrapper.style.paddingLeft = '16rem'; // 256px = 16rem
                }
            } else {
                // Close sidebar
                sidebar.style.transform = 'translateX(-100%)';
                
                // Change burger icon back to hamburger
                if (burgerIcon) {
                    burgerIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
                }
                
                // Hide overlay
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
                
                // Reset content wrapper
                if (contentWrapper) {
                    contentWrapper.style.paddingLeft = '0';
                }
            }
        }

        function initializeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const contentWrapper = document.getElementById('contentWrapper');
            const burgerIcon = document.getElementById('burger-icon');
            
            if (window.innerWidth >= 1024) {
                // Desktop: sidebar open by default
                isSidebarOpen = true;
                sidebar.style.transform = 'translateX(0)';
                if (contentWrapper) {
                    contentWrapper.style.paddingLeft = '16rem';
                }
                // Set burger icon to X
                if (burgerIcon) {
                    burgerIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
                }
            } else {
                // Mobile: sidebar closed by default
                isSidebarOpen = false;
                sidebar.style.transform = 'translateX(-100%)';
                if (contentWrapper) {
                    contentWrapper.style.paddingLeft = '0';
                }
                // Set burger icon to hamburger
                if (burgerIcon) {
                    burgerIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize sidebar state based on screen size
            initializeSidebar();
            
            const burgerMenu = document.getElementById('burger-menu');
            const closeSidebar = document.getElementById('close-sidebar');
            
            if (burgerMenu) {
                burgerMenu.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleSidebar();
                });
            }
            
            if (closeSidebar) {
                closeSidebar.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleSidebar();
                });
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            // Reinitialize sidebar on window resize
            setTimeout(initializeSidebar, 100);
        });

        // ESC key to close sidebar (works on both mobile and desktop)
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isSidebarOpen) {
                toggleSidebar();
            }
        });

        // Click outside to close sidebar (works on both mobile and desktop)
        document.addEventListener('click', function(e) {
            if (isSidebarOpen && 
                !e.target.closest('#sidebar') && 
                !e.target.closest('#burger-menu')) {
                toggleSidebar();
            }
        });

        // Prevent sidebar clicks from bubbling up
        document.addEventListener('click', function(e) {
            if (e.target.closest('#sidebar')) {
                e.stopPropagation();
            }
        });

        function showSection(sectionName) {
            document.querySelectorAll('.section').forEach(section => {
                section.classList.add('hidden');
            });
            
            const targetSection = document.getElementById(sectionName + '-section');
            if (targetSection) {
                targetSection.classList.remove('hidden');
            }
            
            // Update active nav item
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('bg-royal-blue-light');
            });
            
            // Add active class to clicked item
            event.target.closest('.nav-item').classList.add('bg-royal-blue-light');
            
            // Close sidebar on mobile after navigation
            if (window.innerWidth < 1024 && isSidebarOpen) {
                toggleSidebar();
            }
        }

        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = '../classes/LogoutHandling.php';
            }
        }
    </script>
</body>
</html>