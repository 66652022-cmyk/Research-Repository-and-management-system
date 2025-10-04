<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /THESIS/pages/Login.php');
    exit;
}

if (!in_array($_SESSION['user_role'], ['research_faculty', 'super_admin'])) {
    header('Location: /THESIS/pages/Login.php');
    exit;
}

require_once '../config/database.php';
$db = new Database();
$dbConn = $db->connect();

$hour = date('H');
$greeting = $hour < 12 ? 'Good morning!' : ($hour < 18 ? 'Good afternoon!' : 'Good evening!');

// Query groups from database
try {
    $stmt = mysqli_prepare($dbConn, "SELECT g.id, g.name, g.description, g.status, g.created_at, u.name AS adviser_name
                           FROM groups g
                           LEFT JOIN users u ON g.adviser_id = u.id
                           ORDER BY g.created_at DESC");
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . mysqli_error($dbConn));
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $groups = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $groups[] = $row;
    }

    // Get members for each group
    foreach ($groups as &$group) {
        $stmt2 = mysqli_prepare($dbConn, "SELECT u.name FROM group_members gm JOIN users u ON gm.student_id = u.id WHERE gm.group_id = ?");
        if ($stmt2 === false) {
            throw new Exception("Prepare failed: " . mysqli_error($dbConn));
        }
        mysqli_stmt_bind_param($stmt2, 'i', $group['id']);
        mysqli_stmt_execute($stmt2);
        $result2 = mysqli_stmt_get_result($stmt2);
        $members = [];
        while ($row = mysqli_fetch_assoc($result2)) {
            $members[] = $row['name'];
        }
        $group['members'] = $members;
    }
} catch (Exception $e) {
    $groups = [];
}

// Calculate stats dynamically
$active_groups = count(array_filter($groups, function($g) { return $g['status'] === 'active'; }));
$pending_submissions = 3; // This would need a proper query from submissions table
$approved_papers = 15; // This would need a proper query from submissions table
$result = mysqli_query($dbConn, "SELECT COUNT(*) FROM users WHERE role = 'student' AND status = 'active'");
$total_students = mysqli_fetch_row($result)[0];

if ($_SESSION['user_role'] === 'research_faculty') {
    $stats = [
        'active_groups' => $active_groups,
        'pending_submissions' => $pending_submissions,
        'approved_papers' => $approved_papers,
        'total_students' => $total_students
    ];
} elseif ($_SESSION['user_role'] === 'super_admin') {
    $stats = [
        'active_groups' => $active_groups,
        'pending_submissions' => $pending_submissions,
        'approved_papers' => $approved_papers,
        'total_students' => $total_students
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Research Faculty Dashboard</title>
    <link href="/THESIS/src/output.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <style>
        /* Royal Blue Theme */
        .bg-royal-blue { background-color: #4169E1; }
        .bg-royal-blue-dark { background-color: #1E3A8A; }
        .bg-royal-blue-light { background-color: #6366F1; }
        .hover\:bg-royal-blue:hover { background-color: #4169E1; }
        .hover\:bg-royal-blue-dark:hover { background-color: #1E3A8A; }
        .hover\:bg-royal-blue-light:hover { background-color: #6366F1; }
        .text-royal-blue { color: #4169E1; }
        .border-royal-blue { border-color: #4169E1; }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb {
            background: #4169E1;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Header -->
    <header class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <button id="menu-button" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-royal-blue mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-3xl font-bold text-gray-900 flex-1">Research Faculty Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-600"><?php echo $greeting; ?></p>
                        <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                        <p class="text-sm text-royal-blue">Research Faculty</p>
                    </div>
                </div>

            </div>
        </div>
    </header>


    <!-- Sidebar -->
    <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-opacity-50 hidden"></div>

    <aside id="sidebar" 
        class="fixed left-0 top-0 h-full w-80 bg-royal-blue-dark 
                transform -translate-x-full transition-transform duration-300 ease-in-out 
                z-50 shadow-xl">
        <div class="p-6 flex flex-col h-full">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl font-semibold text-white">Menu</h2>
                <button id="close-sidebar" class="text-white hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <nav class="space-y-3 flex-grow">
                <a href="#" onclick="showSection('dashboard')" class="nav-item flex items-center px-4 py-3 text-white rounded-lg hover:bg-royal-blue-light transition-colors duration-200 active">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                    </svg>
                    Dashboard
                </a>
                <a href="#" onclick="showSection('students')" class="nav-item flex items-center px-4 py-3 text-white rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                    </svg>
                    Students
                </a>
                <a href="#" onclick="showSection('groups')" class="nav-item flex items-center px-4 py-3 text-white rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                    </svg>
                    Groups
                </a>
                <a href="#" onclick="showSection('submission')" class="nav-item flex items-center px-4 py-3 text-white rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                    </svg>
                    Thesis Submissions
                </a>
                <a href="#" onclick="showSection('reviews')" class="nav-item flex items-center px-4 py-3 text-white rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Reviews & Feedback
                </a>
            </nav>
            <div class="border-t border-royal-blue-light my-4"></div>
            <button onclick="confirmLogout()" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-red-600 transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"></path>
                </svg>
                Logout
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Dashboard Section -->
        <section id="dashboard-section" class="section">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-blue-500">
                    <div class="p-5 flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Groups</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['active_groups']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-yellow-400">
                    <div class="p-5 flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pending Reviews</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['pending_submissions']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-green-400">
                    <div class="p-5 flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Approved Papers</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['approved_papers']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-purple-500">
                    <div class="p-5 flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['total_students']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center space-x-3 p-3 rounded-lg bg-blue-50">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">New thesis submission from Group Alpha</p>
                            <p class="text-xs text-gray-500">2 hours ago</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 p-3 rounded-lg bg-green-50">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Group Beta's thesis approved</p>
                            <p class="text-xs text-gray-500">1 day ago</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 p-3 rounded-lg bg-amber-50">
                        <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Group Gamma requires revision</p>
                            <p class="text-xs text-gray-500">2 days ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Students crud Section -->
        <section id="students-section" class="section hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Manage Students</h2>
                <button onclick="showAddStudentModal()" class="bg-royal-blue hover:bg-royal-blue-dark text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                    + Add New Student
                </button>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <p class="text-gray-600 mb-4">Register and manage student accounts here.</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="studentsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="studentsTableBody">
                            <?php
                            try {
                                $stmt = mysqli_prepare($dbConn, "SELECT id, name, email, course, status FROM users WHERE role = 'student' AND status = 'active' ORDER BY name");
                                if ($stmt === false) {
                                    throw new Exception("Prepare failed: " . mysqli_error($dbConn));
                                }
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                while ($student = mysqli_fetch_assoc($result)) {
                                    echo '<tr class="hover:bg-gray-50">';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">' . htmlspecialchars($student['id']) . '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">' . htmlspecialchars($student['name']) . '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">' . htmlspecialchars($student['email']) . '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">' . htmlspecialchars($student['course']) . '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">' . htmlspecialchars($student['status']) . '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap space-x-2 text-sm font-medium">';
                                    echo '<button class="text-royal-blue hover:text-royal-blue-dark" onclick="editStudent(' . htmlspecialchars($student['id']) . ')">Edit</button>';
                                    echo '<button class="text-red-600 hover:text-red-900" onclick="deleteStudent(' . htmlspecialchars($student['id']) . ')">Delete</button>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } catch (Exception $e) {
                                echo '<tr><td colspan="6" class="text-center text-red-600">Failed to load students: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Student Groups Section -->
         <section id="groups-section" class="section hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Student Groups</h2>
                <button onclick="showAddGroupModal()" class="bg-royal-blue hover:bg-royal-blue-dark text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                    + Create New Group
                </button>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <p class="text-gray-600 mb-4">Manage your student groups here.</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thesis Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="groupsTableBody">
                            <?php
                            try {
                                foreach ($groups as $group) {
                                    echo '<tr class="hover:bg-gray-50">';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">#GRP' . str_pad($group['id'], 3, '0', STR_PAD_LEFT) . '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap font-semibold">' . htmlspecialchars($group['name']) . '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">' . htmlspecialchars(implode(', ', $group['members'])) . '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">' . htmlspecialchars($group['description']) . '</td>';
                                    $statusClass = 'bg-gray-100 text-gray-800';
                                    if ($group['status'] === 'active') {
                                        $statusClass = 'bg-blue-100 text-blue-800';
                                    } elseif ($group['status'] === 'inactive') {
                                        $statusClass = 'bg-red-100 text-red-800';
                                    } elseif ($group['status'] === 'completed') {
                                        $statusClass = 'bg-green-100 text-green-800';
                                    }
                                    echo '<td class="px-6 py-4 whitespace-nowrap">';
                                    echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $statusClass . '">' . ucfirst($group['status']) . '</span>';
                                    echo '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">' . date('Y-m-d', strtotime($group['created_at'])) . '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap space-x-2 text-sm font-medium">';
                                    echo '<button class="text-royal-blue hover:text-royal-blue-dark">View</button>';
                                    echo '<button class="text-green-600 hover:text-green-900">Edit</button>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } catch (Exception $e) {
                                echo '<tr><td colspan="7" class="text-center text-red-600">Failed to load groups: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Thesis Submissions Section -->
        <section id="submission-section" class="section hidden">
            <iframe src="/THESIS/pages/group_details.php"
                    width="100%" height="100%" 
                    style="border:none; min-height:90vh;">
            </iframe>
        </section>

        <!-- Reviews & Feedback Section -->
        <section id="reviews-section" class="section hidden">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Reviews & Feedback</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pending Reviews</h3>
                    <div class="space-y-4">
                        <div class="border border-yellow-400 rounded-lg p-4 bg-yellow-50">
                            <h4 class="font-semibold text-gray-900">AI Healthcare System - Alpha Team</h4>
                            <p class="text-sm text-gray-600 mt-1">Submitted 2 days ago</p>
                            <button class="mt-3 text-royal-blue hover:text-royal-blue-dark font-semibold">Review Now</button>
                        </div>
                    </div>
                </div>

                <!-- Recent Feedback -->
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Feedback</h3>
                    <div class="space-y-4">
                        <div class="border border-green-400 rounded-lg p-4 bg-green-50">
                            <h4 class="font-semibold text-gray-900">Machine Learning App - Beta Team</h4>
                            <p class="text-sm text-gray-600 mt-1">Feedback provided 1 day ago</p>
                            <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    
    </main>

    <!-- Add Student Modal -->
    <div id="addStudentModal" class="fixed inset-0 hidden items-center justify-center z-50 bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md" onclick="event.stopPropagation()">
            <h3 class="text-xl font-bold mb-6">Add New Student</h3>
            <!-- nasa faculty api yung function nato -->
            <form id="addStudentForm" method="post" onsubmit="submitNewStudent(event)">
                <div class="mb-4">
                    <label for="studentName" class="block font-semibold mb-1">Name</label>
                    <input type="text" id="studentName" name="studentName" required class="w-full border border-gray-300 rounded px-3 py-2" />
                </div>
                <div class="mb-4">
                    <label for="studentCourse" class="block font-semibold mb-1">Course</label>
                    <input type="text" id="studentCourse" name="studentCourse" required class="w-full border border-gray-300 rounded px-3 py-2" />
                </div>
                <div class="mb-4">
                    <label for="studentEmail" class="block font-semibold mb-1">Email</label>
                    <input type="email" id="studentEmail" name="studentEmail" required class="w-full border border-gray-300 rounded px-3 py-2" />
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="hideAddStudentModal()" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
                    <button type="submit" class="bg-royal-blue text-white px-4 py-2 rounded hover:bg-royal-blue-dark">Add Student</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Group Modal -->
    <div id="addGroupModal" 
        class="fixed inset-0 hidden items-center justify-center z-50 bg-opacity-50" 
        onclick="hideAddGroupModal()">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl p-6 relative" 
            onclick="event.stopPropagation()">

            <!-- Header -->
            <div class="flex justify-between items-center border-b pb-3 mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Create New Group</h3>
            <button onclick="hideAddGroupModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>

            <!-- Form -->
            <form id="addGroupForm" method="post" onsubmit="submitNewGroup(event)" class="space-y-6">

            <!-- Group Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                <label for="groupName" class="block font-semibold mb-1">Group Name</label>
                <input type="text" id="groupName" name="groupName" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                <label for="thesisTitle" class="block font-semibold mb-1">Thesis Title</label>
                <input type="text" id="thesisTitle" name="thesisTitle" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" />
                </div>
                <!-- Research Topic -->
                <div>
                    <label for="researchTopic" class="block font-semibold mb-1">Research Topic</label>
                    <input type="text" id="researchTopic" name="researchTopic" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" />
                </div>
            </div>

            <!-- Members -->
            <div class="mb-4">
                <label for="groupMembers" class="block font-semibold mb-1">Select Members</label>
                <select id="groupMembers" name="groupMembers[]" multiple required class="w-full border border-gray-300 rounded px-3 py-2">
                </select>
                <p class="text-sm text-gray-500 mt-1">You can search and select multiple students.</p>
            </div>
            <!-- Footer -->
            <div class="flex justify-end space-x-3 border-t pt-4">
                <button type="button" onclick="hideAddGroupModal()" 
                        class="px-4 py-2 rounded-lg border hover:bg-gray-100">Cancel</button>
                <button type="submit" 
                        class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">
                Create Group
                </button>
            </div>
            </form>
        </div>
        </div>


    <script src="/THESIS/js/faculty_js/faculty_dashboard.js"></script>
</body>
</html>