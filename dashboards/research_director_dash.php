<?php
include '../config/database.php';
$db = new Database();
$dbConn = $db->connect();

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

$hour = date('H');
$greeting = $hour < 12 ? 'Good morning!' : ($hour < 18 ? 'Good afternoon!' : 'Good evening!');


include "../director_api/get_groups.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Research Director Dashboard</title>
    <link href="/THESIS/src/output.css" rel="stylesheet" />
    <link rel="stylesheet" href="/THESIS/src/director-designs.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" class="sidebar-overlay"></div>

    <!-- Header -->
    <header class="header">
        <div class="flex items-center justify-between h-full px-4">
            <div class="flex items-center">
                <button id="burger-menu" class="mr-4 text-white p-2 hover:bg-royal-blue-dark rounded-lg transition-colors duration-200 lg:hidden">
                    <svg id="burger-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-white text-xl font-bold">Research Director Dashboard</h1>
            </div>

            <div class="flex items-center space-x-4">
                <div class="text-white text-right">
                    <?php if ($_SESSION['user_role'] === 'super_admin'): ?>
                    <style>
                    #profileButton {
                        pointer-events: none;
                        cursor: not-allowed;
                    }
                    </style>
                <?php endif; ?>    

                <div class="flex items-center space-x-4">
                    <button id="profileButton" onclick="toggleProfileSidebar()" class="flex items-center space-x-2 text-white hover:opacity-80 transition">
                    <div class="text-right">
                        <p class="opacity-90 mb-1"><?php echo $greeting; ?></p>
                        <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong><br>
                        <small class="text-sm">Research Adviser</small>
                    </div>
                    </button>
                </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-6 text-white">Navigation</h2>
            <nav class="space-y-1" aria-label="Sidebar Navigation">
                <a href="#" onclick="showSection('dashboard')" class="nav-item active">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                    </svg>
                    Dashboard Overview
                </a>
                <a href="#" onclick="showSection('groups')" class="nav-item">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                    </svg>
                    Research Groups
                </a>
                <a href="#" onclick="showSection('assignments')" class="nav-item">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 110 2H8a1 1 0 01-1-1zm1 3a1 1 0 011-1h.01a1 1 0 110 2H8a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"></path>
                    </svg>
                    Group Assignments
                </a>
                <a href="#" onclick="showSection('publish')" class="nav-item">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                    </svg>
                    Publish Research
                </a>
                <a href="#" onclick="showSection('repository')" class="nav-item">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Research Repository
                </a>
                <a href="#" onclick="showSection('analytics')" class="nav-item">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                    </svg>
                    Analytics & Reports
                </a>
            </nav>
        </div>
    </aside>
    <!-- profile Sidebar -->
    <aside 
    id="profile-sidebar" 
    class="fixed top-0 right-0 w-80 bg-royal-blue-dark text-white h-full shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-50 pointer-events-auto">
    
        <div class="p-6 flex flex-col h-full">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold">Profile</h2>
            <button id="close-profile" class="text-white hover:text-gray-400 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            </div>

            <!-- Profile Info -->
            <div class="flex flex-col items-center text-center mb-6">
            <div class="w-20 h-20 bg-royal-blue text-white flex items-center justify-center rounded-full text-2xl font-bold mb-3">
                <?php
                $initials = strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1));
                echo $initials;
                ?>
            </div>
            <p class="font-bold text-lg">
                <?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>
            </p>
            <p class="text-sm text-white opacity-80">
                <?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>
            </p>
            </div>

            <!-- Actions -->
            <div class="mt-auto space-y-2">
            <button 
                class="w-full p-3 bg-royal-blue text-white rounded-lg hover:bg-royal-blue-dark transition-colors">
                View Profile
            </button>

            <a href="#" 
                onclick="confirmLogout()" 
                class="nav-item flex items-center p-3 rounded-lg hover:bg-red-600 transition-colors duration-200 text-white">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"></path>
                </svg>
                Logout
            </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-wrapper">
            <!-- Dashboard Overview Section -->
            <section id="dashboard-section" class="section active">
                <!-- Cards -->
                <div class="stats-grid">
                    <div class="stats-card fade-in" style="border-left-color: #3b82f6;">
                        <div class="p-6 flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 flex-1">
                                <div class="text-sm font-medium text-gray-500">Active Groups</div>
                                <div class="text-3xl font-semibold text-gray-900" id="active-groups"></div>
                            </div>
                        </div>
                    </div>

                    <div class="stats-card fade-in" style="border-left-color: #10b981;">
                        <div class="p-6 flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 flex-1">
                                <div class="text-sm font-medium text-gray-500">Active Advisory Panels</div>
                                <div class="text-3xl font-semibold text-gray-900" id="active-panels"></div>
                            </div>
                        </div>
                    </div>

                    <div class="stats-card fade-in" style="border-left-color: #f59e0b;">
                        <div class="p-6 flex items-center">
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-5 flex-1">
                                <div class="text-sm font-medium text-gray-500">Pending for Publishion</div>
                                <div class="text-3xl font-semibold text-gray-900" id="pending-publish"></div>
                            </div>
                        </div>
                    </div>

                    <div class="stats-card fade-in" style="border-left-color: #8b5cf6;">
                        <div class="p-6 flex items-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-5 flex-1">
                                <div class="text-sm font-medium text-gray-500">Published Researches</div>
                                <div class="text-3xl font-semibold text-gray-900" id="completed-groups"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Groups by Year Level and Course -->
                <div class="chart-grid">
                    <div class="table-container fade-in">
                        <div class="table-header">
                            <h3 class="text-lg font-semibold text-gray-900">Research Groups by Year Level</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-600">1st Year</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-royal-blue h-2 rounded-full" id="year-1-bar"></div>
                                        </div>
                                        <span id="year-1-count">0</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-600">2nd Year</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-royal-blue h-2 rounded-full" id="year-2-bar"></div>
                                        </div>
                                        <span id="year-2-count">0</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-600">3rd Year</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-royal-blue h-2 rounded-full" id="year-3-bar"></div>
                                        </div>
                                        <span id="year-3-count">0</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-600">4th Year</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-royal-blue h-2 rounded-full" id="year-4-bar"></div>
                                        </div>
                                        <span id="year-4-count">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-container fade-in">
                        <div class="table-header">
                            <h3 class="text-lg font-semibold text-gray-900">Research Groups by Course</h3>
                        </div>
                        <div class="p-6">
                            <div id="course-list" class="space-y-4">
                                <!-- nasa js na -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="table-container fade-in">
                    <div class="table-header">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                    </div>
                    <div id="recent-activity" class="p-6 space-y-4">
                        <!-- nasa js na -->
                    </div>
                </div>
            </section>

            <!-- Research Groups Section -->
            <section id="groups-section" class="section">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-900">Research Groups Management</h2>
                </div>
                
                <div class="table-container">
                    <div class="table-header">
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
                                    foreach ($groups as $group):
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        if ($group['status'] === 'active') {
                                            $statusClass = 'bg-blue-100 text-blue-800';
                                        } elseif ($group['status'] === 'inactive') {
                                            $statusClass = 'bg-red-100 text-red-800';
                                        } elseif ($group['status'] === 'completed') {
                                            $statusClass = 'bg-green-100 text-green-800';
                                        }
                                ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">#GRP<?= str_pad($group['id'], 3, '0', STR_PAD_LEFT) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap font-semibold"><?= htmlspecialchars($group['name']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars(implode(', ', $group['members'])) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($group['description']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                                    <?= ucfirst($group['status']) ?>
                                                </span>
                                            </td>

                                            <!-- Adviser -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?= htmlspecialchars($group['adviser_name'] ?? '—') ?>
                                            </td>

                                            <!-- English Critique -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?= htmlspecialchars($group['english_critique_name'] ?? '—') ?>
                                            </td>

                                            <!-- Statistician -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?= htmlspecialchars($group['statistician_name'] ?? '—') ?>
                                            </td>

                                            <!-- Financial Analyst -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?= htmlspecialchars($group['financial_analyst_name'] ?? '—') ?>
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap"><?= date('Y-m-d', strtotime($group['created_at'])) ?></td>

                                            <td class="px-6 py-4 whitespace-nowrap space-x-2 text-sm font-medium">
                                                <button class="text-green-600 hover:text-green-900" data-id="<?= htmlspecialchars($group['id']) ?>">Edit</button>
                                                <button class="text-red-600 hover:text-red-900" data-id="<?= htmlspecialchars($group['id']) ?>">Delete</button>
                                            </td>
                                        </tr>
                                <?php 
                                    endforeach;
                                } catch (Exception $e) {
                                ?>
                                    <tr>
                                        <td colspan="11" class="text-center text-red-600">Failed to load groups: <?= htmlspecialchars($e->getMessage()) ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Group Assignments Section -->
            <section id="assignments-section" class="section">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-900">Group Assignments Management</h2>
                </div>

                <!-- Assignment Summary Cards -->
                <div class="stats-grid">
                    <div class="stats-card fade-in">
                        <div class="p-6 flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">English Critiques</h3>
                                <p class="text-sm text-gray-600">Currently assigned English Critiques</p>
                            </div>
                        </div>
                        <div class="mt-4 ml-7">
                            <div id="english-critique-count" class="text-2xl font-bold text-blue-600"></div>
                        </div>
                    </div>

                    <div class="stats-card fade-in">
                        <div class="p-6 flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Statisticians</h3>
                                <p class="text-sm text-gray-600">Currently assigned Statisticians</p>
                            </div>
                        </div>
                        <div class="mt-4 ml-7">
                            <div id="statistician-count" class="text-2xl font-bold text-green-600"></div>
                        </div>
                    </div>

                    <div class="stats-card fade-in">
                        <div class="p-6 flex items-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Financial Analysts</h3>
                                <p class="text-sm text-gray-600">Currently assigned Financial Analysts</p>
                            </div>
                        </div>
                        <div class="mt-4 ml-7">
                            <div id="financial-analyst-count" class="text-2xl font-bold text-purple-600"></div>
                        </div>
                    </div>
                </div>

                <!-- Groups Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h3 class="text-lg font-semibold text-gray-900">Group Assignment Management</h3>
                        <p class="text-sm text-gray-600 mt-1">Assign groups to English critiques, statisticians, and financial analysts</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adviser</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">English Critique</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statistician</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Financial Analyst</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="assignmentsTableBody">
                            <?php
                                try {
                                    foreach ($groups as $group):
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        if ($group['status'] === 'active') {
                                            $statusClass = 'bg-blue-100 text-blue-800';
                                        } elseif ($group['status'] === 'inactive') {
                                            $statusClass = 'bg-red-100 text-red-800';
                                        } elseif ($group['status'] === 'completed') {
                                            $statusClass = 'bg-green-100 text-green-800';
                                        }
                                ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">#GRP<?= str_pad($group['id'], 3, '0', STR_PAD_LEFT) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap font-semibold"><?= htmlspecialchars($group['name']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars(implode(', ', $group['members'])) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($group['description']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                                    <?= ucfirst($group['status']) ?>
                                                </span>
                                            </td>

                                            <!-- Adviser -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?= htmlspecialchars($group['adviser_name'] ?? '—') ?>
                                            </td>

                                            <!-- English Critique -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?= htmlspecialchars($group['english_critique_name'] ?? '—') ?>
                                            </td>

                                            <!-- Statistician -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?= htmlspecialchars($group['statistician_name'] ?? '—') ?>
                                            </td>

                                            <!-- Financial Analyst -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?= htmlspecialchars($group['financial_analyst_name'] ?? '—') ?>
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap"><?= date('Y-m-d', strtotime($group['created_at'])) ?></td>

                                            <td class="px-6 py-4 whitespace-nowrap space-x-2 text-sm font-medium">
                                                <button class="assign-btn text-royal-blue hover:text-royal-blue-dark" 
                                                    data-group-id="<?= $group['id'] ?>" 
                                                    data-group-name="<?= htmlspecialchars($group['name']) ?>" 
                                                    data-members='<?= json_encode($group['members']) ?>'
                                                    data-adviser-id="<?= htmlspecialchars($group['adviser_id'] ?? '') ?>"
                                                    data-english-id="<?= htmlspecialchars($group['english_critique_id'] ?? '') ?>"
                                                    data-statistician-id="<?= htmlspecialchars($group['statistician_id'] ?? '') ?>"
                                                    data-financial-id="<?= htmlspecialchars($group['financial_analyst_id'] ?? '') ?>">
                                                    Assign Advisory Panel
                                                </button>
                                                <button class="assign-btn text-green-600 hover:text-green-900" 
                                                    data-group-id="<?= $group['id'] ?>" 
                                                    data-group-name="<?= htmlspecialchars($group['name']) ?>" 
                                                    data-members='<?= json_encode($group['members']) ?>'
                                                    data-adviser-id="<?= htmlspecialchars($group['adviser_id'] ?? '') ?>"
                                                    data-english-id="<?= htmlspecialchars($group['english_critique_id'] ?? '') ?>"
                                                    data-statistician-id="<?= htmlspecialchars($group['statistician_id'] ?? '') ?>"
                                                    data-financial-id="<?= htmlspecialchars($group['financial_analyst_id'] ?? '') ?>">
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>
                                <?php 
                                    endforeach;
                                } catch (Exception $e) {
                                ?>
                                    <tr>
                                        <td colspan="11" class="text-center text-red-600">Failed to load groups: <?= htmlspecialchars($e->getMessage()) ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Research publish Section -->
            <section id="publish-section" class="section">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Research Publishion Management</h2>
                <div class="table-container">
                    <div class="table-header">
                        <h3 class="text-lg font-semibold text-gray-900">Publish Research</h3>
                        <p class="text-sm text-gray-600 mt-1">Review and manage researches awaiting approval.</p>
                    </div>
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
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        No research found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Research Repository Section -->
            <section id="repository-section" class="section">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Research Repository</h2>
                <div class="table-container">
                    <div class="table-header">
                        <h3 class="text-lg font-semibold text-gray-900">Approved Research Papers</h3>
                        <p class="text-sm text-gray-600 mt-1">Access approved research papers and documents.</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                                <button class="text-royal-blue hover:text-royal-blue-dark text-sm">Download PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Analytics & Reports Section -->
            <section id="analytics-section" class="section">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Analytics & Reports</h2>
                <div class="chart-grid">
                    <div class="table-container">
                        <div class="table-header">
                            <h3 class="text-lg font-semibold text-gray-900">Research Progress Overview</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Groups in Progress</span>
                                    <span class="font-semibold">0</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Completed Researches</span>
                                    <span class="font-semibold">0</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Pending Reviews</span>
                                    <span class="font-semibold">0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-container">
                        <div class="table-header">
                            <h3 class="text-lg font-semibold text-gray-900">Monthly Activity</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">New Groups Created</span>
                                    <span class="font-semibold">0</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">published This Month</span>
                                    <span class="font-semibold">0</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Approvals This Month</span>
                                    <span class="font-semibold">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <script src="../js/right-sidebar.js"></script>
    <script src="../js/director_js/dashboard-data.js"></script>
    <script>
        // Global variables
        let currentSection = 'dashboard';
        
        // Toggle sidebar
        const burgerMenu = document.getElementById('burger-menu');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        burgerMenu.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            sidebarOverlay.classList.toggle('active');
        });

        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('active');
        });

        // Show section function
        function showSection(sectionName) {
            // Hide all sections
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
            });

            // Show selected section
            const targetSection = document.getElementById(sectionName + '-section');
            if (targetSection) {
                targetSection.classList.add('active');
            }

            // Update navigation
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });

            // Find and activate the corresponding nav item
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                if (item.getAttribute('onclick') && item.getAttribute('onclick').includes(sectionName)) {
                    item.classList.add('active');
                }
            });

            currentSection = sectionName;

            // Close sidebar on mobile after selection
            if (window.innerWidth < 1024) {
                sidebar.classList.remove('open');
                sidebarOverlay.classList.remove('active');
            }
        }

        async function fetchRoleUsers() {
            try {
                const res = await fetch('../director_api/get_role_user.php');
                const data = await res.json();
                return data.success ? data.data : [];
            } catch (e) {
                console.error(e);
                return [];
            }
        }
        // Enhanced SweetAlert2 Role Assignment
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.assign-btn');
                if (btn) {
                    const groupId = btn.getAttribute('data-group-id');
                    const groupName = btn.getAttribute('data-group-name');
                    const members = JSON.parse(btn.getAttribute('data-members'));
                    const currentAdviser = btn.getAttribute('data-adviser-id') || '';
                    const currentEnglish = btn.getAttribute('data-english-id') || '';
                    const currentStatistician = btn.getAttribute('data-statistician-id') || '';
                    const currentFinancial = btn.getAttribute('data-financial-id') || '';

                    showRoleAssignmentModal(groupId, groupName, members, {
                        adviser: currentAdviser,
                        english: currentEnglish,
                        statistician: currentStatistician,
                        financial: currentFinancial
                    });
                }
            });
        });

        async function showRoleAssignmentModal(groupId, groupName, members, currentAssignments = {}) {
            const roleUsers = await fetchRoleUsers();

            const adviserOptions = roleUsers
            .filter(u => u.role === 'adviser')
            .map(u => {
                const isSelected = String(u.id) === String(currentAssignments.adviser);
                return `<option value="${u.id}" ${isSelected ? 'selected' : ''}>${u.name}</option>`;
            })
            .join('');

            const englishOptions = roleUsers
            .filter(u => u.role === 'critique_english')
            .map(u => {
                const isSelected = String(u.id) === String(currentAssignments.english);
                return `<option value="${u.id}" ${isSelected ? 'selected' : ''}>${u.name}</option>`;
            })
            .join('');

            const statisticianOptions = roleUsers
            .filter(u => u.role === 'critique_statistician')
            .map(u => {
                const isSelected = String(u.id) === String(currentAssignments.statistician);
                return `<option value="${u.id}" ${isSelected ? 'selected' : ''}>${u.name}</option>`;
            })
            .join('');

            const financialOptions = roleUsers
            .filter(u => u.role === 'financial_critique')
            .map(u => {
                const isSelected = String(u.id) === String(currentAssignments.financial);
                return `<option value="${u.id}" ${isSelected ? 'selected' : ''}>${u.name}</option>`;
            })
            .join('');

            Swal.fire({
                title: `Assign Roles - ${groupName}`,
                html: `
                    <div class="text-left space-y-4">
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Group ID: <strong>#GRP${String(groupId).padStart(3, '0')}</strong></p>
                            <p class="text-sm text-gray-600">Members: <strong>${members.join(', ')}</strong></p>
                        </div>
                        
                        <div class="grid gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Adviser</label>
                                <select id="adviser" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Adviser</option>
                                    ${adviserOptions}
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">English Critique</label>
                                <select id="englishCritique" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select English Critique</option>
                                    ${englishOptions}
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Statistician</label>
                                <select id="statistician" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Statistician</option>
                                    ${statisticianOptions}
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Financial Analyst (If Applicable)</label>
                                <select id="financialAnalyst" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Financial Analyst (If applicable)</option>
                                    ${financialOptions}
                                </select>
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Save Assignments',
                preConfirm: () => {
                    const adviser = document.getElementById('adviser').value;
                    const englishCritique = document.getElementById('englishCritique').value;
                    const statistician = document.getElementById('statistician').value;
                    const financialAnalyst = document.getElementById('financialAnalyst').value;

                    if (!adviser || !englishCritique || !statistician || !financialAnalyst) {
                        Swal.showValidationMessage('Please assign all roles');
                        return false;
                    }

                    const assignments = [adviser, englishCritique, statistician, financialAnalyst];
                    if (new Set(assignments).size !== assignments.length) {
                        Swal.showValidationMessage('Each member can only be assigned to one role');
                        return false;
                    }

                    return {
                        groupId,
                        adviser,
                        englishCritique,
                        statistician,
                        financialAnalyst
                    };
                }
            }).then(result => {
                if (result.isConfirmed) {
                    saveRoleAssignments(result.value);
                }
            });
        }
        function saveRoleAssignments(assignments) {
        fetch('../director_api/save_role_assignment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(assignments)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Role assignments have been saved successfully.',
                    icon: 'success',
                    confirmButtonColor: '#10b981',
                    timer: 3000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to save role assignments.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while saving the assignments.',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        });
    }

        function viewGroupDetails(groupId) {
            alert('View Group Details for ID: ' + groupId);
        }

        function refreshAssignments() {
            alert('Refreshing assignments...');
        }

        function showAssignmentModal(groupId, type) {
            alert('Show assignment modal for group ' + groupId + ' and type ' + type);
        }

        function unassignGroup(groupId, type) {
            if (confirm('Are you sure you want to unassign this group?')) {
                alert('Group ' + groupId + ' unassigned from ' + type);
            }
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            showSection('dashboard');
        });

        // Handle responsive behavior
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('open');
                sidebarOverlay.classList.remove('active');
            }
        });
    </script>
</body>
</html>