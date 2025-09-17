let users = [];
        let filteredUsers = [];
        let sortColumn = 0;
        let sortDirection = 'asc';

        async function fetchUsers() {
            try {
                const res = await fetch('queries/getuser.php');
                const data = await res.json();
                if (data.success) {
                    users = data.users;
                    filteredUsers = [...users];
                    renderUserTable();
                    updateDashboardStats();
                } else {
                    alert(data.message || 'Failed to fetch users');
                }
            } catch (error) {
                console.error('Error fetching users:', error);
            }
        }

        // Initialize all event handlers properly
        function initializeEventHandlers() {
            // Clear search input
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = '';
                
                // Add debounced search
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(filterUsers, 300);
                });
            }
            
            // Filter dropdown
            const userTypeFilter = document.getElementById('userTypeFilter');
            if (userTypeFilter) {
                userTypeFilter.addEventListener('change', filterUsers);
            }
            
            // Sort dropdown
            const sortFilter = document.getElementById('sortFilter');
            if (sortFilter) {
                sortFilter.addEventListener('change', sortUsers);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initializeEventHandlers();
            fetchUsers();

            showSection('emails');
        });

        function showSection(section) {
            document.querySelectorAll('.section').forEach(s => s.classList.add('hidden'));
            
            const targetSection = document.getElementById(section + '-section');
            if (targetSection) {
                targetSection.classList.remove('hidden');
            }
        
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('bg-royal-blue-light');
            });
            
            if (section !== 'emails') {
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.value = '';
                    filterUsers();
                }
            }
        }

        function updateDashboardStats() {
            const totalUsersEl = document.getElementById('total-users');
            const activeUsersEl = document.getElementById('active-users');
            const emailCountEl = document.getElementById('email-count');
            
            if (totalUsersEl) totalUsersEl.textContent = users.length;
            if (activeUsersEl) activeUsersEl.textContent = users.filter(u => u.status === 'Active').length;
            if (emailCountEl) emailCountEl.textContent = users.length;
        }

        function renderUserTable() {
            const tbody = document.getElementById('userTableBody');
            if (!tbody) return;
            
            tbody.innerHTML = '';
            
            filteredUsers.forEach(user => {
                const row = tbody.insertRow();
                row.innerHTML = `
                    <td class="table-cell">${user.id}</td>
                    <td class="table-cell">${user.name}</td>
                    <td class="table-cell">${user.email}</td>
                    <td class="table-cell">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">${user.type}</span>
                    </td>
                    <td class="table-cell">${user.created}</td>
                    <td class="table-cell">
                        <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-800">Delete</button>
                    </td>
                `;
            });
        }

        function filterUsers() {
            const typeFilter = document.getElementById('userTypeFilter');
            const searchInput = document.getElementById('searchInput');
            
            const typeValue = typeFilter ? typeFilter.value.toLowerCase() : '';
            const searchValue = searchInput ? searchInput.value.toLowerCase().trim() : '';
            
            filteredUsers = users.filter(user => {
                const typeMatch = !typeValue || user.type.toLowerCase() === typeValue;
                const searchMatch = !searchValue || 
                    user.name.toLowerCase().includes(searchValue) ||
                    user.email.toLowerCase().includes(searchValue);
                return typeMatch && searchMatch;
            });
            
            renderUserTable();
        }

        function sortUsers() {
            const sortFilter = document.getElementById('sortFilter');
            if (!sortFilter) return;
            
            const sortValue = sortFilter.value;
            
            // Helper function to extract last name from full name
            function getLastName(fullName) {
                if (!fullName || typeof fullName !== 'string') return '';
                
                const nameParts = fullName.trim().split(/\s+/);
                // Return the last part of the name as the last name
                return nameParts[nameParts.length - 1].toLowerCase();
            }
            
            switch(sortValue) {
                case 'az':
                    filteredUsers.sort((a, b) => {
                        const lastNameA = getLastName(a.name);
                        const lastNameB = getLastName(b.name);
                        return lastNameA.localeCompare(lastNameB);
                    });
                    break;
                case 'za':
                    filteredUsers.sort((a, b) => {
                        const lastNameA = getLastName(a.name);
                        const lastNameB = getLastName(b.name);
                        return lastNameB.localeCompare(lastNameA);
                    });
                    break;
                case 'recent':
                    filteredUsers.sort((a, b) => new Date(b.created) - new Date(a.created));
                    break;
                case 'oldest':
                    filteredUsers.sort((a, b) => new Date(a.created) - new Date(b.created));
                    break;
            }
            
            renderUserTable();
        }

        function showAddUserModal() {
            const modal = document.getElementById('addUserModal');
            const contentWrapper = document.getElementById('contentWrapper');
            
            if (modal && contentWrapper) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                contentWrapper.classList.add('blur-sm');
                
                const form = document.getElementById('addUserForm');
                if (form) form.reset();
                
                ['courseGroup', 'educAttainmentGroup', 'specializationGroup'].forEach(id => {
                    const element = document.getElementById(id);
                    if (element) element.classList.add('hidden');
                });
                
                const searchInput = document.getElementById('searchInput');
                if (searchInput) searchInput.blur();
            }
        }

        function hideAddUserModal() {
            const modal = document.getElementById('addUserModal');
            const contentWrapper = document.getElementById('contentWrapper');
            
            if (modal && contentWrapper) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                contentWrapper.classList.remove('blur-sm');
                
                const form = document.getElementById('addUserForm');
                if (form) form.reset();
            }
        }

        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                users = users.filter(u => u.id !== id);
                filteredUsers = [...users];
                renderUserTable();
                updateDashboardStats();
                alert('User deleted successfully!');
            }
        }

        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = 'classes/LogoutHandling.php';
            }
        }