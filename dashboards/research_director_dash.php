<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /THESIS/pages/Login.php');
    exit;
}

if (!in_array($_SESSION['user_role'], ['research_director', 'super_admin'])) {
    header('Location: /THESIS/pages/Login.php');
    exit;
}

require_once '../config/database.php';
$db = new Database();
$dbConn = $db->connect();

$hour = date('H');
$greeting = $hour < 12 ? 'Good morning!' : ($hour < 18 ? 'Good afternoon!' : 'Good evening!');

// Query groups from database with comprehensive information
try {
    // Get all groups with their details
    $stmt = mysqli_prepare($dbConn, "SELECT g.id, g.name, g.description, g.status, g.created_at,
                           u.name AS adviser_name, u.email AS adviser_email,
                           ec.name AS english_critique_name,
                           s.name AS statistician_name,
                           fa.name AS financial_analyst_name
                           FROM groups g
                           LEFT JOIN users u ON g.adviser_id = u.id
                           LEFT JOIN users ec ON g.english_critique_id = ec.id
                           LEFT JOIN users s ON g.statistician_id = s.id
                           LEFT JOIN users fa ON g.financial_analyst_id = fa.id
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
        $stmt2 = mysqli_prepare($dbConn, "SELECT u.name, u.email, u.course, u.year, gm.role
                               FROM group_members gm
                               JOIN users u ON gm.student_id = u.id
                               WHERE gm.group_id = ?");
        if ($stmt2 === false) {
            throw new Exception("Prepare failed: " . mysqli_error($dbConn));
        }
        mysqli_stmt_bind_param($stmt2, 'i', $group['id']);
        mysqli_stmt_execute($stmt2);
        $result2 = mysqli_stmt_get_result($stmt2);
        $members = [];
        while ($row = mysqli_fetch_assoc($result2)) {
            $members[] = $row;
        }
        $group['members'] = $members;
    }

    // Get pending submissions
    $stmt3 = mysqli_prepare($dbConn, "SELECT COUNT(*) as count FROM documents WHERE status = 'submitted'");
    mysqli_stmt_execute($stmt3);
    $result3 = mysqli_stmt_get_result($stmt3);
    $pending_submissions = mysqli_fetch_assoc($result3)['count'];

    // Get approved researches
    $stmt4 = mysqli_prepare($dbConn, "SELECT COUNT(*) as count FROM documents WHERE status = 'approved'");
    mysqli_stmt_execute($stmt4);
    $result4 = mysqli_stmt_get_result($stmt4);
    $approved_researches = mysqli_fetch_assoc($result4)['count'];

    // Get groups by year level
    $stmt5 = mysqli_prepare($dbConn, "SELECT u.year, COUNT(DISTINCT gm.group_id) as group_count
                           FROM group_members gm
                           JOIN users u ON gm.student_id = u.id
                           WHERE u.role = 'student' AND u.status = 'active'
                           GROUP BY u.year");
    mysqli_stmt_execute($stmt5);
    $result5 = mysqli_stmt_get_result($stmt5);
    $groups_by_year = [];
    while ($row = mysqli_fetch_assoc($result5)) {
        $groups_by_year[$row['year']] = $row['group_count'];
    }

    // Get groups by course
    $stmt6 = mysqli_prepare($dbConn, "SELECT u.course, COUNT(DISTINCT gm.group_id) as group_count
                           FROM group_members gm
                           JOIN users u ON gm.student_id = u.id
                           WHERE u.role = 'student' AND u.status = 'active'
                           GROUP BY u.course");
    mysqli_stmt_execute($stmt6);
    $result6 = mysqli_stmt_get_result($stmt6);
    $groups_by_course = [];
    while ($row = mysqli_fetch_assoc($result6)) {
        $groups_by_course[$row['course']] = $row['group_count'];
    }

} catch (Exception $e) {
    $groups = [];
    $pending_submissions = 0;
    $approved_researches = 0;
    $groups_by_year = [];
    $groups_by_course = [];
}

// Calculate stats dynamically
$active_groups = count(array_filter($groups, function($g) { return $g['status'] === 'active'; }));
$completed_groups = count(array_filter($groups, function($g) { return $g['status'] === 'completed'; }));
$total_groups = count($groups);

$stats = [
    'active_groups' => $active_groups,
    'pending_submissions' => $pending_submissions,
    'approved_researches' => $approved_researches,
    'completed_groups' => $completed_groups,
    'total_groups' => $total_groups,
    'groups_by_year' => $groups_by_year,
    'groups_by_course' => $groups_by_course
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Research Director Dashboard</title>
    <link href="/THESIS/src/output.css" rel="stylesheet" />
    <style>
        /* Research Director Theme - Professional Blue */
        .bg-royal-blue { background-color: #4169E1; }
        .bg-royal-blue-dark { background-color: #1E3A8A; }
        .bg-royal-blue-light { background-color: #6366F1; }
        .hover\:bg-royal-blue:hover { background-color: #4169E1; }
        .hover\:bg-royal-blue-dark:hover { background-color: #1E3A8A; }
        .hover\:bg-royal-blue-light:hover { background-color: #6366F1; }
        .text-royal-blue { color: #1e40af; }
        .border-royal-blue { border-color: #1e40af; }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e40af;
            border-radius: 4px;
        }

        /* Custom animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

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
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Header -->
    <div class="fixed inset-0 z-50 flex pointer-events-none">
        <header class="fixed top-0 left-0 right-0 bg-royal-blue py-4 shadow-lg z-20 pointer-events-auto">
            <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center">
                <button id="burger-menu" class="mr-4 text-white p-2 hover:bg-royal-blue-dark rounded-lg transition-colors duration-200">
                <svg id="burger-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                </button>
                <h1 class="text-white text-xl font-bold">Research Director Dashboard</h1>
            </div>

            <div class="flex items-center space-x-4">
                <div class="text-white text-right">
                <p class="opacity-90 mb-1"><?php echo $greeting; ?></p>
                <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></strong><br>
                <small class="text-royal-blue-light">Research Director</small><br>
                </div>
            </div>
            </div>
        </header>

    <!-- Sidebar -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-opacity-50 hidden pointer-events-auto lg:hidden"></div>

    <aside id="sidebar" class="fixed top-16 left-0 w-64 bg-royal-blue-dark text-white shadow-lg transform transition-transform duration-300 ease-in-out z-10 pointer-events-auto overflow-y-auto" style="height: calc(100vh - 4rem);">
        <div class="p-4 relative">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl font-semibold text-white">Navigation</h2>
                <button id="close-sidebar" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <h2 class="text-xl font-semibold mb-6 pt-8">Navigation</h2>
                <nav class="space-y-2" aria-label="Sidebar Navigation">
                    <a href="#" onclick="showSection('dashboard')" class="nav-item flex items-center p-3 rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                    </svg>
                    Dashboard Overview
                    </a>
                <a href="#" onclick="showSection('groups')" class="nav-item flex items-center px-4 py-3 text-white rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                    </svg>
                    Research Groups
                </a>
                <a href="#" onclick="showSection('assignments')" class="nav-item flex items-center px-4 py-3 text-white rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 110 2H8a1 1 0 01-1-1zm1 3a1 1 0 011-1h.01a1 1 0 110 2H8a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"></path>
                    </svg>
                    Group Assignments
                </a>
                <a href="#" onclick="showSection('submissions')" class="nav-item flex items-center px-4 py-3 text-white rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                    </svg>
                    Research Submissions
                </a>

                <a href="#" onclick="showSection('repository')" class="nav-item flex items-center px-4 py-3 text-white rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Research Repository
                </a>
                <a href="#" onclick="showSection('analytics')" class="nav-item flex items-center px-4 py-3 text-white rounded-lg hover:bg-royal-blue-light transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                    </svg>
                    Analytics & Reports
                </a>
            </nav>
            <div class="border-t border-royal-blue-light my-4"></div>
            <a href="#" onclick="confirmLogout()" class="nav-item flex items-center p-3 rounded-lg hover:bg-red-600 transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"></path>
                </svg>
                Logout
                </a>
        </div>
    </aside>

<div id="contentWrapper" class="min-h-screen transition-all duration-300 ease-in-out pt-16"></div>
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Dashboard Overview Section -->
        <section id="dashboard-section" class="section">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-blue-500 fade-in">
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

                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-yellow-400 fade-in">
                    <div class="p-5 flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pending Submissions</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['pending_submissions']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-green-400 fade-in">
                    <div class="p-5 flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Approved Researches</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['approved_researches']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-purple-500 fade-in">
                    <div class="p-5 flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Completed Groups</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['completed_groups']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Groups by Year Level and Course -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white shadow-lg rounded-lg fade-in">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Research Groups by Year Level</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <?php
                            $year_labels = ['1' => '1st Year', '2' => '2nd Year', '3' => '3rd Year', '4' => '4th Year'];
                            foreach ($year_labels as $year => $label) {
                                $count = $stats['groups_by_year'][$year] ?? 0;
                                $percentage = $stats['total_groups'] > 0 ? round(($count / $stats['total_groups']) * 100) : 0;
                                echo "<div class='flex items-center justify-between'>";
                                echo "<span class='text-sm font-medium text-gray-600'>{$label}</span>";
                                echo "<div class='flex items-center space-x-2'>";
                                echo "<div class='w-24 bg-gray-200 rounded-full h-2'>";
                                echo "<div class='bg-royal-blue h-2 rounded-full' style='width: {$percentage}%'></div>";
                                echo "</div>";
                                echo "<span class='text-sm font-semibold text-gray-900'>{$count}</span>";
                                echo "</div>";
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-lg rounded-lg fade-in">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Research Groups by Course</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <?php
                            foreach ($stats['groups_by_course'] as $course => $count) {
                                $percentage = $stats['total_groups'] > 0 ? round(($count / $stats['total_groups']) * 100) : 0;
                                echo "<div class='flex items-center justify-between'>";
                                echo "<span class='text-sm font-medium text-gray-600'>{$course}</span>";
                                echo "<div class='flex items-center space-x-2'>";
                                echo "<div class='w-24 bg-gray-200 rounded-full h-2'>";
                                echo "<div class='bg-royal-blue h-2 rounded-full' style='width: {$percentage}%'></div>";
                                echo "</div>";
                                echo "<span class='text-sm font-semibold text-gray-900'>{$count}</span>";
                                echo "</div>";
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white shadow-lg rounded-lg fade-in">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center space-x-3 p-3 rounded-lg bg-blue-50">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">New research submission from BSCS Group Alpha</p>
                            <p class="text-xs text-gray-500">2 hours ago</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 p-3 rounded-lg bg-green-50">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Research by BSIT Team Beta approved for repository</p>
                            <p class="text-xs text-gray-500">1 day ago</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 p-3 rounded-lg bg-amber-50">
                        <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Assignment completed for BSCS Group Gamma</p>
                            <p class="text-xs text-gray-500">2 days ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Research Groups Section -->
        <section id="groups-section" class="section hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Research Groups Management</h2>
                <button onclick="showAddGroupModal()" class="bg-royal-blue hover:bg-royal-blue-dark text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                    + Create New Group
                </button>
            </div>
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">All Research Groups</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adviser</th>
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
                                    echo '<td class="px-6 py-4 whitespace-nowrap font-semibold">#GRP' . str_pad($group['id'], 3, '0', STR_PAD_LEFT) . '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">';
                                    echo '<div class="font-semibold">' . htmlspecialchars($group['name']) . '</div>';
                                    echo '<div class="text-sm text-gray-500">' . htmlspecialchars($group['description'] ?? 'No description') . '</div>';
                                    echo '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">';
                                    if (!empty($group['members'])) {
                                        echo '<div class="text-sm">';
                                        foreach ($group['members'] as $member) {
                                            $role_badge = $member['role'] === 'leader' ? '<span class="text-xs bg-blue-100 text-blue-800 px-1 rounded">L</span>' : '';
                                            echo htmlspecialchars($member['name']) . ' ' . $role_badge . '<br>';
                                        }
                                        echo '</div>';
                                    } else {
                                        echo '<span class="text-gray-400">No members</span>';
                                    }
                                    echo '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">' . htmlspecialchars($group['adviser_name'] ?? 'Not assigned') . '</td>';
                                    $statusClass = 'status-badge status-inactive';
                                    if ($group['status'] === 'active') {
                                        $statusClass = 'status-badge status-active';
                                    } elseif ($group['status'] === 'completed') {
                                        $statusClass = 'status-badge status-completed';
                                    }
                                    echo '<td class="px-6 py-4 whitespace-nowrap">';
                                    echo '<span class="' . $statusClass . '">' . ucfirst($group['status']) . '</span>';
                                    echo '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">' . date('Y-m-d', strtotime($group['created_at'])) . '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap space-x-2 text-sm font-medium">';
                                    echo '<button class="text-royal-blue hover:text-royal-blue-dark" onclick="viewGroupDetails(' . $group['id'] . ')">View</button>';
                                    echo '<button class="text-green-600 hover:text-green-900" onclick="editGroup(' . $group['id'] . ')">Edit</button>';
                                    echo '<button class="text-red-600 hover:text-red-900" onclick="deleteGroup(' . $group['id'] . ')">Delete</button>';
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

        <!-- Research Submissions Section -->
        <section id="submissions-section" class="section hidden">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Research Submissions Management</h2>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <p class="text-gray-600 mb-4">Review and manage research submissions awaiting approval.</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submission ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Research Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">#SUB001</td>
                                <td class="px-6 py-4 whitespace-nowrap font-semibold">BSCS Alpha Team</td>
                                <td class="px-6 py-4 whitespace-nowrap">AI in Healthcare Systems</td>
                                <td class="px-6 py-4 whitespace-nowrap">2024-01-20</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap space-x-2 text-sm font-medium">
                                    <button class="text-royal-blue hover:text-royal-blue-dark">Review</button>
                                    <button class="text-green-600 hover:text-green-900">Download</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>



        <!-- Research Repository Section -->
        <section id="repository-section" class="section hidden">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Research Repository</h2>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <p class="text-gray-600 mb-4">Access approved research papers and documents.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-gray-900">AI in Healthcare Systems</h3>
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Approved</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">BSCS Alpha Team - 2024</p>
                        <button class="text-royal-blue hover:text-royal-blue-dark text-sm">Download PDF</button>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-gray-900">Machine Learning Applications</h3>
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Approved</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">BSIT Team Beta - 2024</p>
                        <button class="text-royal-blue hover:text-director-blue-dark text-sm">Download PDF</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Analytics & Reports Section -->
        <section id="analytics-section" class="section hidden">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Analytics & Reports</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Research Progress Overview</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Groups in Progress</span>
                            <span class="font-semibold"><?php echo $stats['active_groups']; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Completed Researches</span>
                            <span class="font-semibold"><?php echo $stats['completed_groups']; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Pending Reviews</span>
                            <span class="font-semibold"><?php echo $stats['pending_submissions']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Activity</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">New Groups Created</span>
                            <span class="font-semibold">12</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Submissions This Month</span>
                            <span class="font-semibold">8</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Approvals This Month</span>
                            <span class="font-semibold">15</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Group Assignments Section -->
        <section id="assignments-section" class="section hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Group Assignments Management</h2>
                <button onclick="refreshAssignments()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                    </svg>
                    Refresh
                </button>
            </div>

            <!-- Assignment Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white shadow-lg rounded-lg p-6 fade-in">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">English Critiques</h3>
                            <p class="text-sm text-gray-600">Groups assigned for English review</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div id="english-critique-count" class="text-2xl font-bold text-blue-600">0</div>
                        <p class="text-sm text-gray-500">assigned groups</p>
                    </div>
                </div>

                <div class="bg-white shadow-lg rounded-lg p-6 fade-in">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Statisticians</h3>
                            <p class="text-sm text-gray-600">Groups assigned for statistical review</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div id="statistician-count" class="text-2xl font-bold text-green-600">0</div>
                        <p class="text-sm text-gray-500">assigned groups</p>
                    </div>
                </div>

                <div class="bg-white shadow-lg rounded-lg p-6 fade-in">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Financial Analysts</h3>
                            <p class="text-sm text-gray-600">Groups assigned for financial review</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div id="financial-analyst-count" class="text-2xl font-bold text-purple-600">0</div>
                        <p class="text-sm text-gray-500">assigned groups</p>
                    </div>
                </div>
            </div>

            <!-- Groups Assignment Table -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Group Assignment Management</h3>
                    <p class="text-sm text-gray-600 mt-1">Assign groups to English critiques, statisticians, and financial analysts</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">English Critique</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statistician</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Financial Analyst</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="assignmentsTableBody">
                            <?php
                            try {
                                foreach ($groups as $group) {
                                    echo '<tr class="hover:bg-gray-50" data-group-id="' . $group['id'] . '">';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">';
                                    echo '<div class="font-semibold">' . htmlspecialchars($group['name']) . '</div>';
                                    echo '<div class="text-sm text-gray-500">' . htmlspecialchars($group['description'] ?? 'No description') . '</div>';
                                    echo '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">';
                                    if (!empty($group['members'])) {
                                        echo '<div class="text-sm">';
                                        foreach ($group['members'] as $member) {
                                            $role_badge = $member['role'] === 'leader' ? '<span class="text-xs bg-blue-100 text-blue-800 px-1 rounded ml-1">L</span>' : '';
                                            echo htmlspecialchars($member['name']) . $role_badge . '<br>';
                                        }
                                        echo '</div>';
                                    } else {
                                        echo '<span class="text-gray-400">No members</span>';
                                    }
                                    echo '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">';
                                    echo '<div class="assignment-cell" data-type="english_critique" data-group-id="' . $group['id'] . '">';
                                    if (!empty($group['english_critique_name'])) {
                                        echo '<div class="text-sm font-medium text-gray-900">' . htmlspecialchars($group['english_critique_name']) . '</div>';
                                        echo '<button class="text-xs text-red-600 hover:text-red-900 mt-1" onclick="unassignGroup(' . $group['id'] . ', \'english_critique\')">Remove</button>';
                                    } else {
                                        echo '<span class="text-gray-400">Not assigned</span>';
                                        echo '<button class="text-xs text-royal-blue hover:text-royal-blue-dark mt-1 block" onclick="showAssignmentModal(' . $group['id'] . ', \'english_critique\')">Assign</button>';
                                    }
                                    echo '</div>';
                                    echo '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">';
                                    echo '<div class="assignment-cell" data-type="statistician" data-group-id="' . $group['id'] . '">';
                                    if (!empty($group['statistician_name'])) {
                                        echo '<div class="text-sm font-medium text-gray-900">' . htmlspecialchars($group['statistician_name']) . '</div>';
                                        echo '<button class="text-xs text-red-600 hover:text-red-900 mt-1" onclick="unassignGroup(' . $group['id'] . ', \'statistician\')">Remove</button>';
                                    } else {
                                        echo '<span class="text-gray-400">Not assigned</span>';
                                        echo '<button class="text-xs text-royal-blue hover:text-royal-blue-dark mt-1 block" onclick="showAssignmentModal(' . $group['id'] . ', \'statistician\')">Assign</button>';
                                    }
                                    echo '</div>';
                                    echo '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">';
                                    echo '<div class="assignment-cell" data-type="financial_analyst" data-group-id="' . $group['id'] . '">';
                                    if (!empty($group['financial_analyst_name'])) {
                                        echo '<div class="text-sm font-medium text-gray-900">' . htmlspecialchars($group['financial_analyst_name']) . '</div>';
                                        echo '<button class="text-xs text-red-600 hover:text-red-900 mt-1" onclick="unassignGroup(' . $group['id'] . ', \'financial_analyst\')">Remove</button>';
                                    } else {
                                        echo '<span class="text-gray-400">Not assigned</span>';
                                        echo '<button class="text-xs text-royal-blue hover:text-royal-blue-dark mt-1 block" onclick="showAssignmentModal(' . $group['id'] . ', \'financial_analyst\')">Assign</button>';
                                    }
                                    echo '</div>';
                                    echo '</td>';
                                    $statusClass = 'status-badge status-inactive';
                                    if ($group['status'] === 'active') {
                                        $statusClass = 'status-badge status-active';
                                    } elseif ($group['status'] === 'completed') {
                                        $statusClass = 'status-badge status-completed';
                                    }
                                    echo '<td class="px-6 py-4 whitespace-nowrap">';
                                    echo '<span class="' . $statusClass . '">' . ucfirst($group['status']) . '</span>';
                                    echo '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap space-x-2 text-sm font-medium">';
                                    echo '<button class="text-royal-blue hover:text-royal-blue-dark" onclick="viewGroupDetails(' . $group['id'] . ')">View</button>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } catch (Exception $e) {
                                echo '<tr><td colspan="7" class="text-center text-red-600">Failed to load assignments: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>
    <script src="/THESIS/js/director_js/research_director_dashboard.js"></script>
</body>
</html>
