let isSidebarOpen = false;

// MAIN FUNCTION: Toggle sidebar open/close
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const contentWrapper = document.getElementById('contentWrapper');
    const burgerIcon = document.getElementById('burger-icon');
    
    // Switch the sidebar state
    isSidebarOpen = !isSidebarOpen;
    
    if (isSidebarOpen) {
        // OPEN SIDEBAR
        sidebar.style.transform = 'translateX(0)'; // Slide in from left
        
        // Change hamburger icon to X
        burgerIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
        
        // On mobile: show dark overlay
        if (window.innerWidth < 1024) {
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent page scrolling
        }
        
        // On desktop: push content to the right
        if (contentWrapper && window.innerWidth >= 1024) {
            contentWrapper.style.paddingLeft = '16rem'; // 256px = 16rem
        }
    } else {
        // CLOSE SIDEBAR
        sidebar.style.transform = 'translateX(-100%)'; // Slide out to left
        
        // Change X icon back to hamburger
        burgerIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
        
        // Hide overlay and restore scrolling
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
        
        // Reset content wrapper
        if (contentWrapper) {
            contentWrapper.style.paddingLeft = '0';
        }
    }
}

// INITIALIZE: Set sidebar state when page loads
function initializeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const contentWrapper = document.getElementById('contentWrapper');
    const burgerIcon = document.getElementById('burger-icon');
    
    if (window.innerWidth >= 1024) {
        // DESKTOP: sidebar open by default
        isSidebarOpen = true;
        sidebar.style.transform = 'translateX(0)';
        
        // Hide overlay on desktop
        if (overlay) {
            overlay.classList.add('hidden');
        }
        
        // Push content to the right
        if (contentWrapper) {
            contentWrapper.style.paddingLeft = '16rem';
        }
        
        // Set burger icon to X
        if (burgerIcon) {
            burgerIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
        }
    } else {
        // MOBILE: sidebar closed by default
        isSidebarOpen = false;
        sidebar.style.transform = 'translateX(-100%)';
        
        // Hide overlay
        if (overlay) {
            overlay.classList.add('hidden');
        }
        
        // Reset content wrapper
        if (contentWrapper) {
            contentWrapper.style.paddingLeft = '0';
        }
        
        // Set burger icon to hamburger
        if (burgerIcon) {
            burgerIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
        }
    }
}

// SECTION MANAGEMENT: Show/hide different content sections
function showSection(sectionName) {
    // Hide all sections
    document.querySelectorAll('.section').forEach(section => {
        section.classList.add('hidden');
    });

    // Show the selected section
    const targetSection = document.getElementById(sectionName + '-section');
    if (targetSection) {
        targetSection.classList.remove('hidden');
    }

    // Remove active styling from all nav items
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('bg-royal-blue-light');
    });

    // Add active styling to the clicked nav item
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        if (item.getAttribute('onclick') === `showSection('${sectionName}')`) {
            item.classList.add('bg-royal-blue-light');
        }
    });

    // Auto-close sidebar on mobile after selecting item
    if (window.innerWidth < 1024 && isSidebarOpen) {
        toggleSidebar();
    }
}

// LOGOUT CONFIRMATION
function confirmLogout() {
    if (confirm("Are you sure you want to log out?")) {
        // Replace this with your actual logout URL
        window.location.href = '/THESIS/classes/LogoutHandling.php';
    }
}

// INITIALIZE EVERYTHING WHEN PAGE LOADS
document.addEventListener('DOMContentLoaded', function() {
    // Set up initial sidebar state
    initializeSidebar();
    
    // Get all the interactive elements
    const burgerMenu = document.getElementById('burger-menu');
    const closeSidebar = document.getElementById('close-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    // BURGER MENU CLICK: Toggle sidebar
    if (burgerMenu) {
        burgerMenu.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }
    
    // CLOSE BUTTON CLICK: Close sidebar
    if (closeSidebar) {
        closeSidebar.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }
    
    // OVERLAY CLICK: Close sidebar (mobile only)
    if (overlay) {
        overlay.addEventListener('click', function() {
            if (isSidebarOpen) {
                toggleSidebar();
            }
        });
    }

    // Show dashboard by default
    showSection('dashboard');
});

// WINDOW RESIZE: Adjust sidebar when screen size changes
window.addEventListener('resize', function() {
    // Small delay to ensure resize is complete
    setTimeout(initializeSidebar, 100);
});

// ESCAPE KEY: Close sidebar when Escape is pressed
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && isSidebarOpen) {
        toggleSidebar();
    }
});

// CLICK OUTSIDE: Close sidebar when clicking outside (but not on burger menu)
document.addEventListener('click', function(e) {
    if (isSidebarOpen && 
        !e.target.closest('#sidebar') && 
        !e.target.closest('#burger-menu')) {
        toggleSidebar();
    }
});

// PREVENT SIDEBAR CLICKS FROM CLOSING: Stop event bubbling inside sidebar
document.addEventListener('click', function(e) {
    if (e.target.closest('#sidebar')) {
        e.stopPropagation();
    }
});


function createAddGroupModal() {
    const modal = document.createElement('div');
    modal.id = 'addGroupModal';
    modal.className = 'fixed inset-0 z-50 overflow-y-auto hidden';
    modal.innerHTML = `
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Create New Research Group</h3>
                            <form id="addGroupForm" onsubmit="submitNewGroup(event)">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Group Name</label>
                                    <input type="text" name="groupName" required
                                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-royal-blue focus:border-transparent">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <textarea name="groupDescription" rows="3"
                                              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-royal-blue focus:border-transparent"></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Members</label>
                                    <div class="max-h-40 overflow-y-auto border border-gray-300 rounded p-2">
                                        <div class="space-y-2">
                                            <label class="flex items-center">
                                                <input type="checkbox" name="groupMembers[]" value="1" class="mr-2">
                                                <span class="text-sm">Student 1</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="checkbox" name="groupMembers[]" value="2" class="mr-2">
                                                <span class="text-sm">Student 2</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Assign Adviser</label>
                                    <select name="adviserId" class="w-full border border-gray-300 rounded px-3 py-2">
                                        <option value="">Select Adviser...</option>
                                        <option value="1">Dr. Smith</option>
                                        <option value="2">Prof. Johnson</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" form="addGroupForm"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-royal-blue text-base font-medium text-white hover:bg-royal-blue-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-royal-blue sm:ml-3 sm:w-auto sm:text-sm">
                        Create Group
                    </button>
                    <button type="button" onclick="hideAddGroupModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

function hideAddGroupModal() {
    const modal = document.getElementById('addGroupModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    // Reset form
    document.getElementById('addGroupForm').reset();
}

function submitNewGroup(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    // Validate at least one member selected
    if (!formData.getAll('groupMembers[]').length) {
        alert('Please select at least one member.');
        return;
    }

    // Here you would typically send the data to your PHP API
    console.log('Creating new group:', Object.fromEntries(formData));

    alert('Group created successfully!');
    hideAddGroupModal();
    // Optionally reload the groups table
    location.reload();
}

function viewGroupDetails(groupId) {
    alert('View group details for group ID: ' + groupId);
    // Implement group details modal/view
}

function editGroup(groupId) {
    alert('Edit group ID: ' + groupId);
    // Implement edit group functionality
}

function deleteGroup(groupId) {
    if (confirm('Are you sure you want to delete this group? This action cannot be undone.')) {
        // Implement delete functionality
        console.log('Deleting group:', groupId);
        alert('Group deleted successfully!');
        location.reload();
    }
}

// Add CSS for royal blue theme colors
const style = document.createElement('style');
style.textContent = `
    .bg-royal-blue { background-color: #1e40af; }
    .bg-royal-blue-dark { background-color: #1e3a8a; }
    .bg-royal-blue-light { background-color: #3b82f6; }
    .hover\\:bg-royal-blue:hover { background-color: #1e40af; }
    .hover\\:bg-royal-blue-dark:hover { background-color: #1e3a8a; }
    .hover\\:bg-royal-blue-light:hover { background-color: #3b82f6; }
    .text-royal-blue { color: #1e40af; }
    .border-royal-blue { border-color: #1e40af; }

    /* Status badges */
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-active { background-color: #dcfce7; color: #166534; }
    .status-completed { background-color: #dbeafe; color: #1e40af; }
    .status-inactive { background-color: #fef2f2; color: #dc2626; }
    .status-pending { background-color: #fef3c7; color: #92400e; }

    /* Custom animations */
    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);

// Assignment Management Functions
function refreshAssignments() {
    // Refresh the assignments data
    location.reload();
}

function showAssignmentModal(groupId, assignmentType) {
    alert('Show assignment modal for group ' + groupId + ' and type ' + assignmentType);
    // Implement assignment modal functionality
}

function unassignGroup(groupId, assignmentType) {
    if (confirm('Are you sure you want to unassign this group from ' + assignmentType + '?')) {
        // Implement unassign functionality
        console.log('Unassigning group:', groupId, 'from:', assignmentType);
        alert('Group unassigned successfully!');
        location.reload();
    }
}

function viewGroupDetails(groupId) {
    alert('View group details for group ID: ' + groupId);
    // Implement group details view
}

// Update assignment counts when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateAssignmentCounts();
});

function updateAssignmentCounts() {
    // This would typically fetch data from APIs
    // For now, we'll use static data or fetch from the current table
    const rows = document.querySelectorAll('#assignmentsTableBody tr');
    let englishCount = 0;
    let statisticianCount = 0;
    let financialCount = 0;

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 5) {
            // Check if English Critique is assigned
            if (cells[2] && cells[2].textContent.trim() !== 'Not assigned') {
                englishCount++;
            }
            // Check if Statistician is assigned
            if (cells[3] && cells[3].textContent.trim() !== 'Not assigned') {
                statisticianCount++;
            }
            // Check if Financial Analyst is assigned
            if (cells[4] && cells[4].textContent.trim() !== 'Not assigned') {
                financialCount++;
            }
        }
    });

    const englishElement = document.getElementById('english-critique-count');
    const statisticianElement = document.getElementById('statistician-count');
    const financialElement = document.getElementById('financial-analyst-count');

    if (englishElement) englishElement.textContent = englishCount;
    if (statisticianElement) statisticianElement.textContent = statisticianCount;
    if (financialElement) financialElement.textContent = financialCount;
}
