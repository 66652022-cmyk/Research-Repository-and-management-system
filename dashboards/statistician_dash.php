<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /THESIS/pages/Login.php');
    exit;
}

$hour = date('H');
$greeting = $hour < 12 ? 'Good morning!' : ($hour < 18 ? 'Good afternoon!' : 'Good evening!');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistician Dashboard</title>
    <link rel="stylesheet" href="/THESIS/src/statistician.css">

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
                <h1>Statistician Dashboard</h1>
            </div>

            <div class="user-info">
                <p class="user-greeting"><?php echo $greeting; ?></p>
                <strong class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong><br>
                <small class="user-role">Statistician</small>
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
                <a class="nav-item" onclick="showSection('editor')">Text Editor</a>
                <a class="nav-item" onclick="showSection('visualization')">Visualization Tools</a>
                <a class="nav-item" onclick="showSection('import')">Data Import</a>
                <a class="nav-item" onclick="showSection('export')">Export Results</a>
                <a class="nav-item" onclick="showSection('submissions')">Submissions</a>
                <a class="nav-item" onclick="showSection('reports')">Reports</a>
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
                        <h3 class="stat-label">Datasets Analyzed</h3>
                        <p class="stat-value"></p>
                    </div>
                    <div class="stat-card">
                        <h3 class="stat-label">Active Projects</h3>
                        <p class="stat-value"></p>
                    </div>
                    <div class="stat-card">
                        <h3 class="stat-label">Models Generated</h3>
                        <p class="stat-value"></p>
                    </div>
                    <div class="stat-card">
                        <h3 class="stat-label">Reports Completed</h3>
                        <p class="stat-value"></p>
                    </div>
                </div>
            </div>

             <!-- Editor Section -->
            <section id="editor-section" class="section hidden">
                <iframe id="editorFrame" src="" style="width:100%; height:90vh; border:none;"></iframe>
            </section>

            <!-- Reports Section -->
            <div id="reports-section" class="section">
                <h2 class="page-title">Reports</h2>
                <div class="list-card">
                    <ul>
                        <li>📊 Statistical summary reports</li>
                        <li>📈 Data visualization reports</li>
                        <li>📝 Analysis documentation</li>
                        <li>📑 Executive summaries</li>
                    </ul>
                </div>
            </div>

            <!-- Visualization Section -->
            <div id="visualization-section" class="section">
                <h2 class="page-title">Visualization Tools</h2>
                <div class="card-grid">
                    <div class="card">
                        <h3>Chart Builder</h3>
                        <p>Create bar charts, line graphs, and pie charts.</p>
                    </div>
                    <div class="card">
                        <h3>Heat Maps</h3>
                        <p>Visualize correlation matrices and patterns.</p>
                    </div>
                    <div class="card">
                        <h3>Distribution Plots</h3>
                        <p>Histogram and density plot generation.</p>
                    </div>
                </div>
            </div>

            <!-- Data Import Section -->
            <div id="import-section" class="section">
                <h2 class="page-title">Data Import</h2>
                <div class="card-grid">
                    <div class="card">
                        <h3>CSV Import</h3>
                        <p>Upload and process CSV files.</p>
                    </div>
                    <div class="card">
                        <h3>Excel Import</h3>
                        <p>Import data from Excel spreadsheets.</p>
                    </div>
                    <div class="card">
                        <h3>Database Connection</h3>
                        <p>Connect to SQL databases directly.</p>
                    </div>
                </div>
            </div>

            <!-- Export Section -->
            <div id="export-section" class="section">
                <h2 class="page-title">Export Results</h2>
                <div class="list-card">
                    <ul>
                        <li>📄 Export to PDF format</li>
                        <li>📊 Export charts as PNG/SVG</li>
                        <li>📑 Generate CSV reports</li>
                        <li>📈 Create PowerPoint presentations</li>
                    </ul>
                </div>
            </div>
            <!-- submmissions -->
            <section id="submissions-section" class="section hidden">
                <iframe src="../pages/group_details.php" 
                        width="100%" height="100%" 
                        style="border:none; min-height:90vh;">
                </iframe>
            </section>
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

        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = '../classes/LogoutHandling.php';
            }
        }
    </script>
</body>
</html>