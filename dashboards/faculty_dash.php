<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: /THESIS/pages/Login.php');
    exit();
}

$allowed_roles = ['research_faculty','adviser', 'research_director', 'super_admin'];
if (!in_array($_SESSION['user_role'], $allowed_roles)) {
    header('Location: /THESIS/pages/Login.php');
    exit();
}

$hour = date('H');
$greeting = $hour < 12 ? 'Good morning!' : ($hour < 18 ? 'Good afternoon!' : 'Good evening!');

// Sample data for demonstration
$dashboard_stats = [
    'active_groups' => 8,
    'pending_submissions' => 3,
    'approved_papers' => 15,
    'total_students' => 24
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Teacher Dashboard</title>
    <link href="/THESIS/src/output.css" rel="stylesheet">
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
        .status-pending { background-color: #fbbf24; color: #92400e; }
        .status-approved { background-color: #10b981; color: #065f46; }
        .status-revision { background-color: #f87171; color: #991b1b; }
        .status-active { background-color: #3b82f6; color: #1e40af; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Top Navigation -->
    <?php include '../components/admin_nav.php'; ?>
    
    <!-- Main Layout Container -->
    <div id="contentWrapper" class="transition duration-300">
        <div class="flex min-h-screen">
            <!-- Sidebar -->
            <aside class="w-64 bg-royal-blue-dark text-white shadow-lg">
                <div class="p-4">
                    <h2 class="text-xl font-semibold mb-6">Research Teacher Panel</h2>
                    <nav class="space-y-2" aria-label="Sidebar Navigation">
                        <a href="#" onclick="showSection('dashboard')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors bg-royal-blue-light">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                            </svg>
                            Dashboard
                        </a>
                        <a href="#" onclick="showSection('groups')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                            </svg>
                            Student Groups
                        </a>
                        <a href="#" onclick="showSection('submissions')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                            </svg>
                            Thesis Submissions
                        </a>
                        <a href="#" onclick="showSection('reviews')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            Reviews & Feedback
                        </a>
                        <a href="#" onclick="confirmLogout()" class="nav-item flex items-center p-3 rounded-lg hover:bg-red-600 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"></path>
                            </svg>
                            Logout
                        </a>
                    </nav>
                </div>
            </aside>
            
            <!-- Main Content -->
            <main class="flex-1 p-6 overflow-y-auto bg-gray-50">
                <!-- Dashboard Section -->
                <div id="dashboard-section" class="section">
                    <h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo $greeting; ?> Research Teacher Dashboard</h1>
                    
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Active Groups</p>
                                    <p class="text-2xl font-semibold text-gray-900"><?php echo $dashboard_stats['active_groups']; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="flex items-center">
                                <div class="p-3 bg-yellow-100 rounded-full">
                                    <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Pending Reviews</p>
                                    <p class="text-2xl font-semibold text-gray-900"><?php echo $dashboard_stats['pending_submissions']; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-100 rounded-full">
                                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Approved Papers</p>
                                    <p class="text-2xl font-semibold text-gray-900"><?php echo $dashboard_stats['approved_papers']; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Total Students</p>
                                    <p class="text-2xl font-semibold text-gray-900"><?php echo $dashboard_stats['total_students']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Activity</h2>
                        <div class="space-y-4">
                            <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">New thesis submission from Group Alpha</p>
                                    <p class="text-sm text-gray-500">2 hours ago</p>
                                </div>
                            </div>
                            <div class="flex items-center p-3 bg-green-50 rounded-lg">
                                <svg class="w-6 h-6 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">Group Beta's thesis approved</p>
                                    <p class="text-sm text-gray-500">1 day ago</p>
                                </div>
                            </div>
                            <div class="flex items-center p-3 bg-yellow-50 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">Group Gamma requires revision</p>
                                    <p class="text-sm text-gray-500">2 days ago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Groups Section -->
                <div id="groups-section" class="section hidden">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold text-gray-800">Student Groups Management</h1>
                        <button onclick="showAddGroupModal()" class="bg-royal-blue hover:bg-royal-blue-dark text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                            + Register New Group
                        </button>
                    </div>

                    <!-- Filter Controls -->
                    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                        <div class="flex flex-wrap gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Status:</label>
                                <select id="groupStatusFilter" onchange="filterGroups()" class="border border-gray-300 rounded-md px-3 py-2">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="completed">Completed</option>
                                    <option value="on-hold">On Hold</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search:</label>
                                <input type="text" id="groupSearchInput" onkeyup="filterGroups()" placeholder="Search by group name or members..." class="border border-gray-300 rounded-md px-3 py-2">
                            </div>
                        </div>
                    </div>

                    <!-- Groups Table -->
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="table-container">
                            <table class="w-full" id="groupsTable">
                                <thead>
                                    <tr>
                                        <th class="table-header">Group ID</th>
                                        <th class="table-header">Group Name</th>
                                        <th class="table-header">Members</th>
                                        <th class="table-header">Thesis Title</th>
                                        <th class="table-header">Status</th>
                                        <th class="table-header">Created Date</th>
                                        <th class="table-header">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="groupsTableBody">
                                    <!-- Groups will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Thesis Submissions Section -->
                <div id="submissions-section" class="section hidden">
                    <h1 class="text-3xl font-bold text-gray-800 mb-6">Thesis Submissions</h1>
                    
                    <!-- Filter Controls -->
                    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                        <div class="flex flex-wrap gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Status:</label>
                                <select id="submissionStatusFilter" onchange="filterSubmissions()" class="border border-gray-300 rounded-md px-3 py-2">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending Review</option>
                                    <option value="approved">Approved</option>
                                    <option value="revision">Needs Revision</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search:</label>
                                <input type="text" id="submissionSearchInput" onkeyup="filterSubmissions()" placeholder="Search by title or group..." class="border border-gray-300 rounded-md px-3 py-2">
                            </div>
                        </div>
                    </div>

                    <!-- Submissions Table -->
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="table-container">
                            <table class="w-full" id="submissionsTable">
                                <thead>
                                    <tr>
                                        <th class="table-header">Submission ID</th>
                                        <th class="table-header">Group Name</th>
                                        <th class="table-header">Thesis Title</th>
                                        <th class="table-header">Submitted Date</th>
                                        <th class="table-header">Status</th>
                                        <th class="table-header">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="submissionsTableBody">
                                    <!-- Submissions will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Reviews Section -->
                <div id="reviews-section" class="section hidden">
                    <h1 class="text-3xl font-bold text-gray-800 mb-6">Reviews & Feedback</h1>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Pending Reviews -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">Pending Reviews</h2>
                            <div class="space-y-4" id="pendingReviewsList">
                                <!-- Pending reviews will be populated here -->
                            </div>
                        </div>
                        
                        <!-- Recent Feedback -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Feedback</h2>
                            <div class="space-y-4" id="recentFeedbackList">
                                <!-- Recent feedback will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Group Modal -->
    <div id="addGroupModal" class="fixed inset-0 hidden items-center justify-center z-50 bg-black/40" onclick="if(event.target === this) hideAddGroupModal()">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            <h2 class="text-2xl font-bold mb-6">Register New Student Group</h2>
            <form id="addGroupForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Group Name:</label>
                    <input type="text" id="groupName" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thesis Title:</label>
                    <input type="text" id="thesisTitle" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Group Members:</label>
                    <div id="membersContainer">
                        <div class="flex gap-2 mb-2 member-input">
                            <input type="text" placeholder="Student Name" required class="flex-1 border border-gray-300 rounded-md px-3 py-2">
                            <input type="email" placeholder="Student Email" required class="flex-1 border border-gray-300 rounded-md px-3 py-2">
                            <button type="button" onclick="removeMember(this)" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">Remove</button>
                        </div>
                    </div>
                    <button type="button" onclick="addMemberField()" class="mt-2 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Add Member</button>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description:</label>
                    <textarea id="groupDescription" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="Brief description of the thesis project..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="hideAddGroupModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-royal-blue text-white rounded-md hover:bg-royal-blue-dark">
                        Register Group
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
<script src="/THESIS/js/faculty_js/faculry_dashboard.js"></script>
<script>
    function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = '/THESIS/classes/LogoutHandling.php';
            }
        }
</script>
</html>
