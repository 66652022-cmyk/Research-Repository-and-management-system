<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /THESIS/pages/Login.php');
    exit;
}

if (!in_array($_SESSION['user_role'], ['super_admin', 'critique_english'])) {
    header('Location: /THESIS/pages/Login.php');
    exit;
}

$hour = date('H');
$greeting = $hour < 12 ? 'Good morning!' : ($hour < 18 ? 'Good afternoon!' : 'Good evening!');

$stats = [
    'drafts_reviewed'   => 12,
    'pending_approval'  => 5,
    'revisions_requested'=> 7,
    'final_copies'      => 4
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>English Critic Dashboard</title>
  <link rel="stylesheet" href="/THESIS/src/output.css">
  <style>
    .bg-royal-blue { background-color: #4169E1; }
    .bg-royal-blue-dark { background-color: #1E3A8A; }
    .bg-royal-blue-light { background-color: #6366F1; }
    .text-royal-blue { color: #4169E1; }
    .active-nav { background-color: #4169E1; }
  </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
 <header class="fixed top-0 left-0 right-0 bg-royal-blue py-4 shadow-lg z-20 pointer-events-auto">
            <div class="container mx-auto px-4 flex justify-between items-center">
                <div class="flex items-center">
                    <button id="burger-menu" class="mr-4 text-white p-2 hover:bg-royal-blue-dark rounded-lg transition-colors duration-200">
                        <svg id="burger-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h1 class="text-white text-xl font-bold">English Critique Dashboard</h1>
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
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-25 left-0 w-64 bg-royal-blue-dark text-white shadow-lg transform transition-transform duration-300 ease-in-out z-10 pointer-events-auto overflow-y-auto" style="height: calc(100vh - 4rem);">
        <div class="flex justify-between items-center p-4 md:hidden">
            <h2 class="text-lg font-semibold">Menu</h2>
            <button id="close-sidebar" class="text-white">✖</button>
        </div>
        
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-6 hidden md:block">Navigation</h2>
                <nav class="space-y-2">
                    <a href="#" onclick="showSection('dashboard')" class="nav-item block p-3 rounded-lg hover:bg-royal-blue-light active-nav">Dashboard</a>
                    <a href="#" onclick="showSection('review')" class="nav-item block p-3 rounded-lg hover:bg-royal-blue-light">Document Review</a>
                    <a href="#" onclick="showSection('editor')" class="nav-item block p-3 rounded-lg hover:bg-royal-blue-light">Text Editor</a>
                    <a href="#" onclick="showSection('workflow')" class="nav-item block p-3 rounded-lg hover:bg-royal-blue-light">Approval Workflow</a>
                    <a href="#" onclick="showSection('management')" class="nav-item block p-3 rounded-lg hover:bg-royal-blue-light">Document Management</a>
                    <a href="#" onclick="showSection('submissions')" class="nav-item block p-3 rounded-lg hover:bg-royal-blue-light">Submissions</a>
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

<!-- Overlay for mobile -->
<div id="sidebar-overlay" class="fixed inset-0 bg-opacity-50 hidden"></div>

<!-- Main Content -->
<div id="contentWrapper" class="pt-25 transition-all duration-300 ease-in-out">
  <main class="min-h-screen bg-gray-50 p-6">
    <!-- Dashboard Section -->
    <div id="dashboard-section" class="section">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h1>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white shadow-lg rounded-lg p-6">
          <h3 class="text-sm text-gray-500">Drafts Reviewed</h3>
          <p class="text-3xl font-bold"><?php echo $stats['drafts_reviewed']; ?></p>
        </div>
        <div class="bg-white shadow-lg rounded-lg p-6">
          <h3 class="text-sm text-gray-500">Pending Approval</h3>
          <p class="text-3xl font-bold"><?php echo $stats['pending_approval']; ?></p>
        </div>
        <div class="bg-white shadow-lg rounded-lg p-6">
          <h3 class="text-sm text-gray-500">Revisions Requested</h3>
          <p class="text-3xl font-bold"><?php echo $stats['revisions_requested']; ?></p>
        </div>
        <div class="bg-white shadow-lg rounded-lg p-6">
          <h3 class="text-sm text-gray-500">Final Copies Approved</h3>
          <p class="text-3xl font-bold"><?php echo $stats['final_copies']; ?></p>
        </div>
      </div>
    </div>

    <!-- Document Review Section -->
    <div id="review-section" class="section hidden">
      <h2 class="text-2xl font-bold mb-6">Document Review</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="font-semibold text-lg mb-2">Thesis Draft Viewer</h3>
          <p class="text-gray-600">Preview and analyze uploaded manuscripts.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="font-semibold text-lg mb-2">Grammar Check</h3>
          <p class="text-gray-600">Identify grammar and spelling issues.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="font-semibold text-lg mb-2">Structure Analysis</h3>
          <p class="text-gray-600">Review document flow and formatting.</p>
        </div>
      </div>
    </div>

    <!-- Annotation Section -->
    <section id="editor-section" class="section hidden">
      <iframe id="editorFrame" src="../pages/editor.php" style="width:100%; height:800px; border:none;"></iframe>
    </section>

    <!-- Workflow Section -->
    <div id="workflow-section" class="section hidden">
      <h2 class="text-2xl font-bold mb-6">Approval Workflow</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="font-semibold text-lg mb-2">Review & Approve</h3>
          <p class="text-gray-600">Approve or reject drafts with comments.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="font-semibold text-lg mb-2">Revision Requests</h3>
          <p class="text-gray-600">Send back drafts with requested changes.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="font-semibold text-lg mb-2">Final Approval</h3>
          <p class="text-gray-600">Mark documents as final and publish-ready.</p>
        </div>
      </div>
    </div>

    <!-- Document Management Section -->
    <div id="management-section" class="section hidden">
      <h2 class="text-2xl font-bold mb-6">Document Management</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="font-semibold text-lg mb-2">Document History</h3>
          <p class="text-gray-600">Track changes and past versions of documents.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="font-semibold text-lg mb-2">Version Comparison</h3>
          <p class="text-gray-600">Compare two versions side by side.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="font-semibold text-lg mb-2">Final Copy Approval</h3>
          <p class="text-gray-600">Approve and store the final thesis copy.</p>
        </div>
      </div>
    </div>

    <!-- Submissions Section (full page, iframe) -->
    <div id="submissions-section" class="section hidden">
      <iframe src="/THESIS/pages/group_details.php" 
              width="100%" height="100%" 
              style="border:none; min-height:90vh;">
      </iframe>
    </div>
  </main>
</div>
</body>
<script src="../js/right-sidebar.js"></script>
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
                    contentWrapper.style.paddingLeft = '16rem';
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
//para sa editor galing sa group details
        function showEditorWithDoc(docId, type) {
            const editorFrame = document.getElementById('editorFrame');
            const editorSection = document.getElementById('editor-section');

            const url = type === 'text' 
                ? `../pages/editor.php?id=${docId}` 
                : `../pages/spreadsheet.php?id=${docId}`;

            editorFrame.src = url;

            document.querySelectorAll('.section').forEach(s => s.classList.add('hidden'));
            editorSection.classList.remove('hidden');

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = '../classes/LogoutHandling.php';
            }
        }
    </script>
</html>
