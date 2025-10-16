<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /THESIS/pages/Login.php');
    exit;
}

if (!in_array($_SESSION['user_role'], ['financial_critique', 'super_admin'])) {
    header('Location: /THESIS/pages/Login.php');
    exit;
}
$hour = date('H');
$greeting = $hour < 12 ? 'Good morning!' : ($hour < 18 ? 'Good afternoon!' : 'Good evening!');

require_once '../config/database.php';

$db = new Database();
$dbConn = $db->connect();

include "../queries/get_assigned_groups.php";

$data = getUserGroupsAndStats($dbConn, $_SESSION['user_role'], $_SESSION['user_id']);
$groups = $data['groups'];
$stats  = $data['stats'];

$assignedGroups = $data['groups'];
$stats = $data['stats'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Analyst Dashboard</title>
    <link rel="stylesheet" href="/THESIS/src/finance.css">
    <link rel="stylesheet" href="/THESIS/src/output.css">
</head>
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
<body>

    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="header-left">
                <button id="burger-menu" class="burger-menu">
                    <svg id="burger-icon" class="burger-icon" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1>Financial Analyst Dashboard</h1>
            </div>

            <?php if ($_SESSION['user_role'] === 'super_admin'): ?>
                    <style>
                    #profileButton {
                        pointer-events: none;
                        cursor: not-allowed;
                    }
                    </style>
                <?php endif; ?>    

                <div class="flex items-center space-x-4">
                    <button id="profileButton" onclick="toggleProfileSidebar()" class="flex items-center space-x-2 text-white hover:opacity-80 transition">
                    <div class="text-right">
                        <p class="opacity-90 mb-1"><?php echo $greeting; ?></p>
                        <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong><br>
                        <small class="text-sm">Research Adviser</small>
                    </div>
                    </button>
                </div>
        </div>
    </header>
    <!-- Overlay -->
    <div id="sidebar-overlay" class="sidebar-overlay"></div>
    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
        <div class="sidebar-mobile-header">
            <h2>Menu</h2>
            <button id="close-sidebar" class="close-sidebar">✖</button>
        </div>
        
        <div class="sidebar-content">
            <h2 class="sidebar-title">Navigation</h2>
            <nav>
                <a class="nav-item active" onclick="showSection('dashboard')">Dashboard</a>
                <a class="nav-item" onclick="showSection('editor')">Text Editor</a>
                <a class="nav-item" onclick="showSection('financial')">Financial Reports</a>
                <a class="nav-item" onclick="showSection('spreadsheet')">Spreadsheet</a>
                <a class="nav-item" onclick="showSection('audit')">Audit Management</a>
                <a class="nav-item" onclick="showSection('expense')">Expense Tracking</a>
                <a class="nav-item" onclick="showSection('submissions')">Submissions</a>
            </nav>
        </div>
    </aside>
    <!-- profile Sidebar -->
    <aside 
    id="profile-sidebar" 
    class="fixed top-0 right-0 w-80 bg-royal-blue-dark text-white h-full shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-50 pointer-events-auto">
    
        <div class="p-6 flex flex-col h-full">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold">Profile</h2>
            <button id="close-profile" class="text-white hover:text-gray-400 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            </div>

            <!-- Profile Info -->
            <div class="flex flex-col items-center text-center mb-6">
            <div class="w-20 h-20 bg-royal-blue text-white flex items-center justify-center rounded-full text-2xl font-bold mb-3">
                <?php
                $initials = strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1));
                echo $initials;
                ?>
            </div>
            <p class="font-bold text-lg">
                <?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>
            </p>
            <p class="text-sm text-white opacity-80">
                <?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>
            </p>
            </div>

            <!-- Actions -->
            <div class="mt-auto space-y-2">
            <button 
                class="w-full p-3 bg-royal-blue text-white rounded-lg hover:bg-royal-blue-dark transition-colors">
                View Profile
            </button>

            <a href="#" 
                onclick="confirmLogout()" 
                class="nav-item flex items-center p-3 rounded-lg hover:bg-red-600 transition-colors duration-200 text-white">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"></path>
                </svg>
                Logout
            </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div id="contentWrapper" class="content-wrapper">
        <main class="main-content">
            <!-- Dashboard Section -->
            <div id="dashboard-section" class="section">
                <h1 class="page-title">Dashboard</h1>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3 class="stat-label">Reports Reviewed</h3>
                        <p class="stat-value">18</p>
                    </div>
                    <div class="stat-card">
                        <h3 class="stat-label">Budgets Analyzed</h3>
                        <p class="stat-value">12</p>
                    </div>
                    <div class="stat-card">
                        <h3 class="stat-label">Forecasts Completed</h3>
                        <p class="stat-value">9</p>
                    </div>
                    <div class="stat-card">
                        <h3 class="stat-label">Audits Pending</h3>
                        <p class="stat-value">5</p>
                    </div>
                </div>
            </div>

            <!-- Financial Reports Section -->
            <div id="financial-section" class="section hidden">
                <h2 class="page-title">Financial Reports</h2>
                <div class="list-card">
                    <ul>
                        <li>💼 Income Statement Analysis</li>
                        <li>📊 Balance Sheet Review</li>
                        <li>💰 Cash Flow Statements</li>
                        <li>📈 Financial Ratio Analysis</li>
                        <li>📋 Management Reports</li>
                    </ul>
                </div>
            </div>

            <!-- Forecasting Section -->
            <div id="forecasting-section" class="section hidden">
                <h2 class="page-title">Forecasting</h2>
                <div class="card-grid">
                    <div class="card">
                        <h3>Revenue Forecasting</h3>
                        <p>Project future revenue based on historical data.</p>
                    </div>
                    <div class="card">
                        <h3>Expense Modeling</h3>
                        <p>Forecast operational and capital expenditures.</p>
                    </div>
                    <div class="card">
                        <h3>Scenario Analysis</h3>
                        <p>Create best, worst, and most likely financial scenarios.</p>
                    </div>
                </div>
            </div>

            <!-- Audit Management Section -->
            <div id="audit-section" class="section hidden">
                <h2 class="page-title">Audit Management</h2>
                <div class="card-grid">
                    <div class="card">
                        <h3>Internal Audits</h3>
                        <p>Schedule and conduct internal financial audits.</p>
                    </div>
                    <div class="card">
                        <h3>Compliance Tracking</h3>
                        <p>Monitor regulatory and policy compliance.</p>
                    </div>
                    <div class="card">
                        <h3>Audit Documentation</h3>
                        <p>Maintain audit trails and supporting documents.</p>
                    </div>
                </div>
            </div>

            <!-- Expense Tracking Section -->
            <div id="expense-section" class="section hidden">
                <h2 class="page-title">Expense Tracking</h2>
                <div class="list-card">
                    <ul>
                        <li>🧾 Receipt Management</li>
                        <li>💳 Purchase Order Tracking</li>
                        <li>📅 Monthly Expense Reports</li>
                        <li>🔍 Expense Categorization</li>
                        <li>📊 Spending Analytics</li>
                    </ul>
                </div>
            </div>
            <!-- Text Editor Section -->
            <section id="editor-section" class="section hidden">
                <iframe id="editorFrame" src="../pages/editor.php" style="width:100%; height:90vh; border:none;"></iframe>
            </section>

             <!-- spread Sheet Section -->
            <section id="spreadsheet-section" class="section hidden">
                <iframe id="spreadsheetframe" src="../pages/spreadsheet.php" style="width:100%; height:800px; border:none;"></iframe>
            </section>
            <!-- submissions page -->
            <section id="submissions-section" class="section hidden">
                <iframe src="../pages/group_details.php" 
                        width="100%" height="100%" 
                        style="border:none; min-height:90vh;">
                </iframe>
            </section>
        </main>
    </div>
    <script src="../js/right-sidebar.js"></script>
    <script>
        let isSidebarOpen = false;

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const burgerIcon = document.getElementById('burger-icon');
            
            isSidebarOpen = !isSidebarOpen;
            
            if (isSidebarOpen) {
                sidebar.classList.add('open');
                
                if (burgerIcon) {
                    burgerIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
                }
                
                if (window.innerWidth < 1024) {
                    overlay.classList.add('show');
                    document.body.style.overflow = 'hidden';
                }
            } else {
                sidebar.classList.remove('open');
                
                if (burgerIcon) {
                    burgerIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
                }
                
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        function initializeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const burgerIcon = document.getElementById('burger-icon');
            
            if (window.innerWidth >= 1024) {
                isSidebarOpen = true;
                sidebar.classList.add('open');
                if (burgerIcon) {
                    burgerIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
                }
            } else {
                isSidebarOpen = false;
                sidebar.classList.remove('open');
                if (burgerIcon) {
                    burgerIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initializeSidebar();
            
            const burgerMenu = document.getElementById('burger-menu');
            const closeSidebar = document.getElementById('close-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
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

            if (overlay) {
                overlay.addEventListener('click', function() {
                    if (isSidebarOpen) {
                        toggleSidebar();
                    }
                });
            }
        });

        window.addEventListener('resize', function() {
            setTimeout(initializeSidebar, 100);
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isSidebarOpen && window.innerWidth < 1024) {
                toggleSidebar();
            }
        });

        function showSection(sectionName) {
            // Hide all sections first
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
                section.classList.add('hidden');
            });
            
            const targetSection = document.getElementById(sectionName + '-section');
            if (targetSection) {
                targetSection.classList.add('active');
                targetSection.classList.remove('hidden');
            }
            
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Only update nav item if event exists (not called programmatically)
            if (event) {
                event.target.classList.add('active');
            }
            
            if (window.innerWidth < 1024 && isSidebarOpen) {
                toggleSidebar();
            }
        }
        //para sa editor galing sa group details
        function showEditorWithDoc(docId, type) {
            const editorFrame = document.getElementById('editorFrame');
            const editorSection = document.getElementById('editor-section');

            const url = type === 'text' 
                ? `../pages/editor.php?id=${docId}` 
                : `../pages/spreadsheet.php?id=${docId}`;

            editorFrame.src = url;

            // Hide all sections and remove active class
            document.querySelectorAll('.section').forEach(s => {
                s.classList.add('hidden');
                s.classList.remove('active');
            });
            
            // Show editor section
            editorSection.classList.remove('hidden');
            editorSection.classList.add('active');

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
</body>
</html>