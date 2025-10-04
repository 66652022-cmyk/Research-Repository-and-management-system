<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Analyst Dashboard</title>
    <link rel="stylesheet" href="/THESIS/src/finance.css">
</head>
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

            <div class="user-info">
                <p class="user-greeting">Good morning!</p>
                <strong class="user-name">John Doe</strong><br>
                <small class="user-role">Financial Analyst</small>
            </div>
        </div>
    </header>

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
                <a class="nav-item" onclick="showSection('budget')">Budget Analysis</a>
                <a class="nav-item" onclick="showSection('financial')">Financial Reports</a>
                <a class="nav-item" onclick="showSection('forecasting')">Forecasting</a>
                <a class="nav-item" onclick="showSection('audit')">Audit Management</a>
                <a class="nav-item" onclick="showSection('expense')">Expense Tracking</a>
                <a class="nav-item">Submissions</a>
                <a class="nav-item logout" onclick="confirmLogout()">Logout</a>
            </nav>
        </div>
    </aside>

    <!-- Overlay -->
    <div id="sidebar-overlay" class="sidebar-overlay"></div>

    <!-- Main Content -->
    <div id="contentWrapper" class="content-wrapper">
        <main class="main-content">
            <!-- Dashboard Section -->
            <div id="dashboard-section" class="section active">
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

            <!-- Budget Analysis Section -->
            <div id="budget-section" class="section">
                <h2 class="page-title">Budget Analysis</h2>
                <div class="card-grid">
                    <div class="card">
                        <h3>Budget Variance Analysis</h3>
                        <p>Compare actual vs. budgeted expenses and revenues.</p>
                    </div>
                    <div class="card">
                        <h3>Cost Center Review</h3>
                        <p>Analyze departmental spending and allocations.</p>
                    </div>
                    <div class="card">
                        <h3>Budget Forecasting</h3>
                        <p>Project future budget requirements and trends.</p>
                    </div>
                </div>
            </div>

            <!-- Financial Reports Section -->
            <div id="financial-section" class="section">
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
            <div id="forecasting-section" class="section">
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
            <div id="audit-section" class="section">
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
            <div id="expense-section" class="section">
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
        </main>
    </div>

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
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
            });
            
            const targetSection = document.getElementById(sectionName + '-section');
            if (targetSection) {
                targetSection.classList.add('active');
            }
            
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            event.target.classList.add('active');
            
            if (window.innerWidth < 1024 && isSidebarOpen) {
                toggleSidebar();
            }
        }

        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                alert("Logout successful!");
            }
        }
    </script>
</body>
</html>