
  const profileSidebar = document.getElementById('profile-sidebar');
  const closeProfileBtn = document.getElementById('close-profile');

  function toggleProfileSidebar() {
    profileSidebar.classList.toggle('translate-x-full');
  }

  if (closeProfileBtn) {
    closeProfileBtn.addEventListener('click', () => {
      profileSidebar.classList.add('translate-x-full');
    });
  }

  function confirmLogout() {
    if (confirm('Are you sure you want to log out?')) {
      window.location.href = '/THESIS/classes/LogoutHandling.php';
    }
  }