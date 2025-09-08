<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: /THESIS/pages/Login.php');
    exit();
}

// Optional: restrict to only super_admin
if ($_SESSION['user_role'] !== 'super_admin') {
    header('Location: /THESIS/pages/Login.php');
    exit();
}


$hour = date('H');
$greeting = $hour < 12 ? 'Good morning!' : ($hour < 18 ? 'Good afternoon!' : 'Good evening!');

$stats = [
    'total_users' => 150,
    'active_users' => 80,
    'total_students' => 100,
    'total_admins' => 10
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link href="/THESIS/src/output.css" rel="stylesheet">
    <link rel="stylesheet" href="src/output.css">
    <style>
        /* Royal Blue Theme Colors */
        .bg-royal-blue { background-color: #4169E1; }
        .bg-royal-blue-dark { background-color: #1E3A8A; }
        .bg-royal-blue-light { background-color: #6366F1; }
        .hover\:bg-royal-blue:hover { background-color: #4169E1; }
        .hover\:bg-royal-blue-dark:hover { background-color: #1E3A8A; }
        .hover\:bg-royal-blue-light:hover { background-color: #6366F1; }
        
        .table-cell {
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            background: white;
        }
        .table-header {
            background: #f3f4f6;
            font-weight: 600;
            border: 1px solid #d1d5db;
            padding: 8px 12px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .table-container {
            max-height: 70vh;
            overflow: auto;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }
        .sort-icon {
            display: inline-block;
            margin-left: 4px;
            transition: transform 0.2s;
        }
        .sort-asc .sort-icon {
            transform: rotate(180deg);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Top Navigation -->
    <?php include 'components/admin_nav.php'; ?>
    
    <!-- Main Layout Container -->
    <div id="contentWrapper" class="transition duration-300"></div>
        <div class="flex min-h-screen">
            <!-- Sidebar -->
            <aside class="w-64 bg-royal-blue-dark text-white shadow-lg">
                <?php include 'components/admin_sidebar.php'; ?>
            </aside>
            
            <!-- Main Content -->
            <main class="flex-1 p-6 overflow-y-auto bg-gray-50">
                <!-- Dashboard Section -->
                <div id="dashboard-section" class="section hidden">
                    <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h1>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                                    <p class="text-2xl font-semibold text-gray-900" id="total-users"><?php echo $stats['total_users']; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-100 rounded-full">
                                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Active Users</p>
                                    <p class="text-2xl font-semibold text-gray-900" id="active-users">0</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="flex items-center">
                                <div class="p-3 bg-yellow-100 rounded-full">
                                    <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Email Accounts</p>
                                    <p class="text-2xl font-semibold text-gray-900" id="email-count">0</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Admins</p>
                                    <p class="text-2xl font-semibold text-gray-900">1</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Emails Section -->
                <div id="emails-section" class="section">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold text-gray-800">User Email Management</h1>
                        <button onclick="showAddUserModal()" class="bg-royal-blue hover:bg-royal-blue-dark text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                            + Add New User
                        </button>
                    </div>

                    <!-- Filter Controls -->
                    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                        <div class="flex flex-wrap gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by User Type:</label>
                                <select id="userTypeFilter" onchange="filterUsers()" class="border border-gray-300 rounded-md px-3 py-2">
                                    <option value="">All Types</option>
                                    <option value="research director">Research Director</option>
                                    <option value="research faculty">Research Faculty</option>
                                    <option value="research adviser">Research Adviser</option>
                                    <option value="english critique">English Critique</option>
                                    <option value="statistician">Statistician</option>
                                    <option value="financial critique">Financial Critique</option>
                                    <option value="students">Students</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sort:</label>
                                <select id="sortFilter" onchange="sortUsers()" class="border border-gray-300 rounded-md px-3 py-2">
                                    <option value="az">A-Z</option>
                                    <option value="za">Z-A</option>
                                    <option value="recent">Most Recent</option>
                                    <option value="oldest">Oldest</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search:</label>
                                <input type="text" id="searchInput" onkeyup="filterUsers()" placeholder="Search by name or email..." class="border border-gray-300 rounded-md px-3 py-2">
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="table-container overflow-x-auto overflow-y-auto max-h-96">
                            <table class="w-full" id="userTable">
                                <thead>
                                    <tr>
                                        <th class="table-header">ID</th>
                                        <th class="table-header">Name</th>
                                        <th class="table-header">Email</th>
                                        <th class="table-header">User Type</th>
                                        <th class="table-header">Created Date</th>
                                        <th class="table-header">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="userTableBody">
                                    <!-- Users will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Reset Password Section -->
                <div id="reset-password-section" class="section hidden">
                    <h1 class="text-3xl font-bold text-gray-800 mb-6">Reset User Passwords</h1>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select User:</label>
                            <select id="resetPasswordUser" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="">Select a user...</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Password:</label>
                            <input type="password" id="newPassword" class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="Enter new password">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password:</label>
                            <input type="password" id="confirmPassword" class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="Confirm new password">
                        </div>
                        <button onclick="resetPassword()" class="bg-royal-blue hover:bg-royal-blue-dark text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">
                            Reset Password
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <!-- Add User Modal -->
    <div id="addUserModal"
        class="fixed inset-0 hidden items-center justify-center z-50 bg-black/40"
        onclick="if(event.target === this) hideAddUserModal()">

    <!-- Modal Box -->
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md"
            onclick="event.stopPropagation()">
            <h2 class="text-2xl font-bold mb-6">Add New User</h2>
            <form id="addUserForm">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Name:</label>
                <input type="text" id="userName" required class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Email:</label>
                <input type="email" id="userEmail" required class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">User Type:</label>
                <select id="userType" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                <option value="">Select user type...</option>
                <option value="research director">Research Director</option>
                <option value="research faculty">Research Faculty</option>
                <option value="research adviser">Research Adviser</option>
                <option value="english critique">English Critique</option>
                <option value="statistician">Statistician</option>
                <option value="financial critique">Financial Critique</option>
                <option value="students">Students</option>
                </select>
            </div>
            <div id="educAttainmentGroup" class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Educational Attainment:</label>
            <select id="educationalAttainment" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                <option value="">Select attainment...</option>
                <option value="Bachelors">Bachelors</option>
                <option value="Masters">Masters</option>
                <option value="Doctorate">Doctorate</option>
                <option value="Other">Other</option>
            </select>
            </div>

            <div id="specializationGroup" class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Specialization:</label>
            <input type="text" id="specialization" required class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="e.g. Computer Science, Education">
            </div>
            <div id="courseGroup" class="mb-4 hidden">
            <label class="block text-sm font-medium text-gray-700 mb-2">Course:</label>
            <input type="text" id="course" class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="hideAddUserModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-royal-blue text-white rounded-md hover:bg-royal-blue-dark">
                Add User
                </button>
            </div>
            </form>
        </div>
    </div>


    <script>
        // Sample data
        let users = [
            { id: 1, name: "John Smith", email: "john.smith@university.edu", type: "research director", created: "2024-01-15" },
            { id: 2, name: "Sarah Johnson", email: "sarah.johnson@university.edu", type: "research faculty", created: "2024-01-20" },
            { id: 3, name: "Michael Brown", email: "michael.brown@university.edu", type: "statistician", created: "2024-02-01" },
            { id: 4, name: "Emily Davis", email: "emily.davis@university.edu", type: "english critique", created: "2024-02-10" },
            { id: 5, name: "James Wilson", email: "james.wilson@university.edu", type: "students", created: "2024-02-15" },
            { id: 6, name: "Lisa Anderson", email: "lisa.anderson@university.edu", type: "research adviser", created: "2024-02-20" },
            { id: 7, name: "David Martinez", email: "david.martinez@university.edu", type: "financial critique", created: "2024-02-25" },
            { id: 8, name: "Jennifer Taylor", email: "jennifer.taylor@university.edu", type: "students", created: "2024-03-01" },
            { id: 9, name: "Robert Garcia", email: "robert.garcia@university.edu", type: "research faculty", created: "2024-03-05" },
            { id: 10, name: "Amanda Thompson", email: "amanda.thompson@university.edu", type: "english critique", created: "2024-03-10" },
            { id: 11, name: "Christopher Lee", email: "christopher.lee@university.edu", type: "statistician", created: "2024-03-15" },
            { id: 12, name: "Michelle Rodriguez", email: "michelle.rodriguez@university.edu", type: "students", created: "2024-03-20" },
            { id: 13, name: "Daniel White", email: "daniel.white@university.edu", type: "research director", created: "2024-03-25" },
            { id: 14, name: "Jessica Moore", email: "jessica.moore@university.edu", type: "research adviser", created: "2024-03-30" },
            { id: 15, name: "Kevin Clark", email: "kevin.clark@university.edu", type: "financial critique", created: "2024-04-01" }
        ];
        
        let filteredUsers = [...users];
        let sortColumn = 0;
        let sortDirection = 'asc';

        // Navigation
        function showSection(section) {
            document.querySelectorAll('.section').forEach(s => s.classList.add('hidden'));
            document.getElementById(section + '-section').classList.remove('hidden');
            
            // Update active nav item
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('bg-royal-blue-light');
            });
            event.target.closest('.nav-item').classList.add('bg-royal-blue-light');
            
            if (section === 'dashboard') {
                updateDashboardStats();
            } else if (section === 'reset-password') {
                populateResetPasswordUsers();
            }
        }

        // Dashboard functions
        function updateDashboardStats() {
            document.getElementById('total-users').textContent = users.length;
            document.getElementById('active-users').textContent = users.filter(u => u.status === 'Active').length;
            document.getElementById('email-count').textContent = users.length;
        }

        // Table functions
        function renderUserTable() {
            const tbody = document.getElementById('userTableBody');
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
            const typeFilter = document.getElementById('userTypeFilter').value.toLowerCase();
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            
            filteredUsers = users.filter(user => {
                const typeMatch = !typeFilter || user.type.toLowerCase() === typeFilter;
                const searchMatch = !searchTerm || 
                    user.name.toLowerCase().includes(searchTerm) ||
                    user.email.toLowerCase().includes(searchTerm);
                return typeMatch && searchMatch;
            });
            
            renderUserTable();
        }

        function sortUsers() {
            const sortValue = document.getElementById('sortFilter').value;
            
            switch(sortValue) {
                case 'az':
                    filteredUsers.sort((a, b) => a.name.localeCompare(b.name));
                    break;
                case 'za':
                    filteredUsers.sort((a, b) => b.name.localeCompare(a.name));
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

        // Modal functions
        function showAddUserModal() {
            document.getElementById('addUserModal').classList.remove('hidden');
            document.getElementById('addUserModal').classList.add('flex');
            document.getElementById('contentWrapper').classList.add('blur-sm');
            
            // Reset form state
            document.getElementById('addUserForm').reset();
            
            // Hide all conditional fields initially
            document.getElementById('courseGroup').classList.add('hidden');
            document.getElementById('educAttainmentGroup').classList.add('hidden');
            document.getElementById('specializationGroup').classList.add('hidden');
        }

        function hideAddUserModal() {
            document.getElementById('addUserModal').classList.add('hidden');
            document.getElementById('addUserModal').classList.remove('flex');
            document.getElementById('addUserForm').reset();
            document.getElementById('contentWrapper').classList.remove('blur-sm');
            
            // Reset all form fields and remove required attributes
            const inputs = ['course', 'educationalAttainment', 'specialization'];
            inputs.forEach(id => {
                document.getElementById(id).removeAttribute('required');
            });
        }

        // REPLACE THE OLD FORM SUBMISSION CODE WITH THIS NEW VERSION:
        
        // Dynamic form fields handler
        document.getElementById('userType').addEventListener('change', function() {
            const role = this.value;
            const courseGroup = document.getElementById('courseGroup');
            const educGroup = document.getElementById('educAttainmentGroup');
            const specGroup = document.getElementById('specializationGroup');
            
            // Get the required attribute elements
            const courseInput = document.getElementById('course');
            const educInput = document.getElementById('educationalAttainment');
            const specInput = document.getElementById('specialization');

            if (role === 'students') {
                // Students - show only course
                courseGroup.classList.remove('hidden');
                educGroup.classList.add('hidden');
                specGroup.classList.add('hidden');
                
                // Set required attributes
                courseInput.setAttribute('required', 'required');
                educInput.removeAttribute('required');
                specInput.removeAttribute('required');
            } else if (role) {
                // Faculty/Staff - show course, education, and specialization
                courseGroup.classList.remove('hidden');
                educGroup.classList.remove('hidden');
                specGroup.classList.remove('hidden');
                
                // Set required attributes
                courseInput.removeAttribute('required'); // Course is optional for faculty
                educInput.setAttribute('required', 'required');
                specInput.setAttribute('required', 'required');
            } else {
                // Default - hide all
                courseGroup.classList.add('hidden');
                educGroup.classList.add('hidden');
                specGroup.classList.add('hidden');
                
                // Remove required attributes
                courseInput.removeAttribute('required');
                educInput.removeAttribute('required');
                specInput.removeAttribute('required');
            }
        });

document.getElementById('addUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const name = document.getElementById('userName').value.trim();
    const email = document.getElementById('userEmail').value.trim();
    const userType = document.getElementById('userType').value;
    const course = document.getElementById('course').value.trim();
    const educationalAttainment = document.getElementById('educationalAttainment').value;
    const specialization = document.getElementById('specialization').value.trim();

    console.log('Form data collected:', { name, email, userType, course, educationalAttainment, specialization });

    if (!name || !email || !userType) {
        alert('Please fill in all required fields.');
        return;
    }

    if (userType === 'students' && !course) {
        alert('Course is required for students.');
        return;
    }

    if (userType !== 'students') {
        if (!educationalAttainment) {
            alert('Educational attainment is required for this role.');
            return;
        }
        if (!specialization) {
            alert('Specialization is required for this role.');
            return;
        }
    }

    const roleMap = {
        "research director": "research_director",
        "research faculty": "adviser",
        "research adviser": "adviser", 
        "english critique": "critique_english",
        "statistician": "critique_statistician",
        "financial critique": "critique_statistician",
        "students": "student"
    };

    const dbRole = roleMap[userType];
    console.log('Role mapping:', userType, '->', dbRole);
    
    if (!dbRole) {
        alert('Invalid role selected.');
        return;
    }

    // Prepare user data
    const userData = {
        name: name,
        email: email,
        role: dbRole,
        course: userType === 'students' ? course : (course || null),
        educational_attainment: userType !== 'students' ? educationalAttainment : null,
        specialization: userType !== 'students' ? specialization : null
    };

    console.log('Sending user data:', userData);

    try {
        const response = await fetch('classes/process_add_user.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(userData)
        });

        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);

        const responseText = await response.text();
        console.log('Raw response:', responseText);

        let result;
        try {
            result = JSON.parse(responseText);
            console.log('Parsed JSON result:', result);
        } catch (jsonError) {
            console.error('JSON parsing error:', jsonError);
            console.error('Response was:', responseText);
            alert('Server returned invalid response. Check console for details.');
            return;
        }

        if (result.success) {
            const newUser = {
                id: result.user_id,
                name: userData.name,
                email: userData.email,
                type: userType,
                status: 'Active',
                created: new Date().toISOString().split('T')[0]
            };

            console.log('Adding new user to table:', newUser);

            users.push(newUser);
            filteredUsers = [...users];
            renderUserTable();
            updateDashboardStats();

            hideAddUserModal();
            alert('User added successfully! Default password: Password123!');
        } else {
            console.error('Server error:', result);
            alert('Error: ' + (result.message || 'Unknown error occurred'));
        }
    } catch (error) {
        console.error('Network error details:', error);
        alert('Network error occurred. Check console for details.');
    }
});

        // User management functions
        function editUser(id) {
            const user = users.find(u => u.id === id);
            if (user) {
                alert(`Edit user: ${user.name} (${user.email})`);
                // Add edit functionality here
            }
        }

        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                users = users.filter(u => u.id !== id);
                filteredUsers = [...users];
                renderUserTable();
                alert('User deleted successfully!');
            }
        }

        // Reset password functions
        function populateResetPasswordUsers() {
            const select = document.getElementById('resetPasswordUser');
            select.innerHTML = '<option value="">Select a user...</option>';
            
            users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.name} (${user.email})`;
                select.appendChild(option);
            });
        }

        function resetPassword() {
            const userId = document.getElementById('resetPasswordUser').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (!userId) {
                alert('Please select a user.');
                return;
            }
            
            if (!newPassword) {
                alert('Please enter a new password.');
                return;
            }
            
            if (newPassword !== confirmPassword) {
                alert('Passwords do not match.');
                return;
            }
            
            const user = users.find(u => u.id === parseInt(userId));
            if (user) {
                alert(`Password reset successfully for ${user.name}`);
                document.getElementById('newPassword').value = '';
                document.getElementById('confirmPassword').value = '';
                document.getElementById('resetPasswordUser').value = '';
            }
        }

        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = 'classes/adminLogout.php';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderUserTable();
            updateDashboardStats();
        });
    </script>
</body>
</html>