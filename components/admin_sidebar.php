<div class="p-4">
  <h2 class="text-xl font-semibold mb-6">Admin Panel</h2>
  <nav class="space-y-2" aria-label="Sidebar Navigation">
    <a href="#" onclick="showSection('dashboard')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors">
      <svg class="w-5 h-5 mr-3" fill="currentColor"></svg>
      Dashboard
    </a>
    <a href="#" onclick="showSection('emails')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors bg-royal-blue-light">
      <svg class="w-5 h-5 mr-3" fill="currentColor"></svg>
      User Emails
    </a>
    <a href="#" onclick="showSection('reset-password')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors">
      <svg class="w-5 h-5 mr-3" fill="currentColor"></svg>
      Reset Passwords
    </a>
    <a href="#" onclick="confirmLogout()" class="nav-item flex items-center p-3 rounded-lg hover:bg-red-600 transition-colors">
      <svg class="w-5 h-5 mr-3" fill="currentColor"></svg>
      Logout
    </a>
  </nav>
</div>
