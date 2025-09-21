<!-- Main Navigation Container -->
<div class="fixed inset-0 z-50 flex pointer-events-none">
  <header class="fixed top-0 left-0 right-0 bg-royal-blue py-4 shadow-lg z-20 pointer-events-auto">
    <div class="container mx-auto px-4 flex justify-between items-center">
      <div class="flex items-center">
        <button id="burger-menu" class="mr-4 text-white p-2 hover:bg-royal-blue-dark rounded-lg transition-colors duration-200">
          <svg id="burger-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <h1 class="text-white text-xl font-bold">Research Management System</h1>
      </div>

      <div class="flex items-center space-x-4">
        <div class="text-white text-right">
          <p class="opacity-90 mb-1"><?php echo $greeting; ?></p>
          <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></strong><br>
          <small><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></small><br>
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
      
      <h2 class="text-xl font-semibold mb-6 pt-8">Admin Panel</h2>
      <nav class="space-y-2" aria-label="Sidebar Navigation">
        <a href="#" onclick="showSection('dashboard')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
          <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
          </svg>
          Dashboard
        </a>
        <a href="#" onclick="showSection('emails')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors duration-200 bg-royal-blue-light">
          <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
          </svg>
          User Emails
        </a>
        <a href="#" onclick="showSection('reset-password')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
          <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
          </svg>
          Reset Passwords
        </a>
        <a href="#" onclick="showSection('adviser')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
          <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
          </svg>
          Adviser
        </a>
        <a href="#" onclick="showSection('english-critique')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
          <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
          </svg>
          English Critique
        </a>
        <a href="/THESIS/dashboards/finance_dash.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
          <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
          </svg>
          Financial Analyst
        </a>
        <a href="#" onclick="showSection('faculty')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
          <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
          </svg>
          Research Faculty
        </a>
        <a href="#" onclick="showSection('research-director')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
          <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
            <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"></path>
          </svg>
          Research Director
        </a>
        <a href="/THESIS/dashboards/statistician_dash.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
          <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
          </svg>
          Statistician
        </a>
        <a href="#" onclick="showSection('student')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
          <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 2L3 7v3c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-7-5z" clip-rule="evenodd"></path>
          </svg>
          Student
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
</script>