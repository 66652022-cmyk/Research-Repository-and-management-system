<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: /THESIS/pages/Login.php');
    exit();
}

//restrict to only super_admin
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
     
    <div id="contentWrapper" class="transition-all duration-300 ease-in-out">
        <main class="min-h-screen bg-gray-50">
            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 mt-5 ml-1">
                <!-- Dashboard Section -->
                <div id="dashboard-section" class="section pt-25">
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
                <div id="emails-section" class="section pt-25 hidden">
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
                                <select id="userTypeFilter" class="border border-gray-300 rounded-md px-3 py-2">
                                    <option value="">All Types</option>
                                    <option value="super_admin">Admin</option>
                                    <option value="research_director">Research Director</option>
                                    <option value="research_faculty">Research Faculty</option>
                                    <option value="adviser">Research Adviser</option>
                                    <option value="critique_english">English Critique</option>
                                    <option value="critique_statistician">Statistician</option>
                                    <option value="financial_critique">Financial Critique</option>
                                    <option value="student">Students</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sort:</label>
                                <select id="sortFilter" class="border border-gray-300 rounded-md px-3 py-2">
                                    <option value="az">A-Z</option>
                                    <option value="za">Z-A</option>
                                    <option value="recent">Most Recent</option>
                                    <option value="oldest">Oldest</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search:</label>
                                <input type="text" id="searchInput" placeholder="Search by name or email..." class="border border-gray-300 rounded-md px-3 py-2"autocomplete="off"spellcheck="false">
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
                <div id="reset-password-section" class="section pt-25 hidden">
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
                <!-- adviser page -->
                <div id="adviser-section" class="section pt-20 hidden">
                    <iframe src="/THESIS/dashboards/adviser_dash.php" 
                            width="100%" height="100%" 
                            style="border:none; min-height:90vh;">
                    </iframe>
                </div>
                <!-- english critique page -->
                <div id="english-critique-section" class="section pt-20 hidden">
                    <iframe src="/THESIS/dashboards/english_critique_dash.php" 
                            width="100%" height="100%" 
                            style="border:none; min-height:90vh;">
                    </iframe>
                </div>
                <!-- research faculty page -->
                <div id="faculty-section" class="section pt-20 hidden">
                    <iframe src="/THESIS/dashboards/faculty_dash.php" 
                            width="100%" height="100%" 
                            style="border:none; min-height:90vh;">
                    </iframe>
                </div>
                <!-- director dashboard -->
                <div id="research-director-section" class="section pt-20 hidden">
                    <iframe src="/THESIS/dashboards/research_director_dash.php" 
                            width="100%" height="100%" 
                            style="border:none; min-height:90vh;">
                    </iframe>
                </div>
                <!-- student dashboard -->
                <div id="student-section" class="section pt-20 hidden">
                    <iframe src="/THESIS/dashboards/student_dash.php" 
                            width="100%" height="100%" 
                            style="border:none; min-height:90vh;">
                    </iframe>
                </div>
            </main>
        </main>
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

    <script src="js/SuperAdminJS/dashboard.js"></script>
</body>
</html>