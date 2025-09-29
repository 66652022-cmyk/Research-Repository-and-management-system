<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups Overview - Google Classroom Style</title>
    <link rel="stylesheet" href="../src/output.css">
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

        /* Card hover effects */
        .group-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .group-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-3xl font-bold text-gray-900">Groups Overview</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Welcome back!</p>
                        <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></p>
                    </div>
                    <button class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Your Research Groups</h2>
            <p class="text-gray-600">Click on a group card to view details and manage the thesis progress.</p>
        </div>

        <!-- Groups Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Group Card 1 -->
            <div class="group-card bg-white rounded-lg shadow-md overflow-hidden cursor-pointer" onclick="window.location.href='group_details.php?group_id=1'">
                <div class="bg-royal-blue p-4">
                    <h3 class="text-white text-xl font-semibold">Alpha Research Team</h3>
                    <p class="text-royal-blue-light text-sm">AI in Healthcare Systems</p>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                        </svg>
                        <span class="text-sm text-gray-600">3 members</span>
                    </div>
                    <div class="space-y-1 mb-4">
                        <p class="text-sm text-gray-700">John Doe</p>
                        <p class="text-sm text-gray-700">Jane Smith</p>
                        <p class="text-sm text-gray-700">Mike Johnson</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        <span class="text-sm text-gray-500">Created: Jan 2024</span>
                    </div>
                </div>
            </div>

            <!-- Group Card 2 -->
            <div class="group-card bg-white rounded-lg shadow-md overflow-hidden cursor-pointer" onclick="window.location.href='group_details.php?group_id=2'">
                <div class="bg-royal-blue p-4">
                    <h3 class="text-white text-xl font-semibold">Beta Innovators</h3>
                    <p class="text-royal-blue-light text-sm">Machine Learning Applications</p>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                        </svg>
                        <span class="text-sm text-gray-600">4 members</span>
                    </div>
                    <div class="space-y-1 mb-4">
                        <p class="text-sm text-gray-700">Alice Brown</p>
                        <p class="text-sm text-gray-700">Bob Wilson</p>
                        <p class="text-sm text-gray-700">Carol Davis</p>
                        <p class="text-sm text-gray-700">David Lee</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        <span class="text-sm text-gray-500">Created: Feb 2024</span>
                    </div>
                </div>
            </div>

            <!-- Group Card 3 -->
            <div class="group-card bg-white rounded-lg shadow-md overflow-hidden cursor-pointer" onclick="window.location.href='group_details.php?group_id=3'">
                <div class="bg-royal-blue p-4">
                    <h3 class="text-white text-xl font-semibold">Gamma Solutions</h3>
                    <p class="text-royal-blue-light text-sm">Data Analytics for Business</p>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                        </svg>
                        <span class="text-sm text-gray-600">2 members</span>
                    </div>
                    <div class="space-y-1 mb-4">
                        <p class="text-sm text-gray-700">Eva Green</p>
                        <p class="text-sm text-gray-700">Frank White</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">In Progress</span>
                        <span class="text-sm text-gray-500">Created: Mar 2024</span>
                    </div>
                </div>
            </div>

            <!-- Group Card 4 -->
            <div class="group-card bg-white rounded-lg shadow-md overflow-hidden cursor-pointer" onclick="window.location.href='group_details.php?group_id=4'">
                <div class="bg-royal-blue p-4">
                    <h3 class="text-white text-xl font-semibold">Delta Explorers</h3>
                    <p class="text-royal-blue-light text-sm">Sustainable Energy Solutions</p>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                        </svg>
                        <span class="text-sm text-gray-600">3 members</span>
                    </div>
                    <div class="space-y-1 mb-4">
                        <p class="text-sm text-gray-700">Grace Taylor</p>
                        <p class="text-sm text-gray-700">Henry Miller</p>
                        <p class="text-sm text-gray-700">Ivy Clark</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        <span class="text-sm text-gray-500">Created: Apr 2024</span>
                    </div>
                </div>
            </div>

            <!-- Group Card 5 -->
            <div class="group-card bg-white rounded-lg shadow-md overflow-hidden cursor-pointer" onclick="window.location.href='group_details.php?group_id=5'">
                <div class="bg-royal-blue p-4">
                    <h3 class="text-white text-xl font-semibold">Epsilon Creators</h3>
                    <p class="text-royal-blue-light text-sm">Mobile App Development</p>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                        </svg>
                        <span class="text-sm text-gray-600">5 members</span>
                    </div>
                    <div class="space-y-1 mb-4">
                        <p class="text-sm text-gray-700">Jack Robinson</p>
                        <p class="text-sm text-gray-700">Kate Lewis</p>
                        <p class="text-sm text-gray-700">Liam Walker</p>
                        <p class="text-sm text-gray-700">Mia Hall</p>
                        <p class="text-sm text-gray-700">Noah Young</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Revision</span>
                        <span class="text-sm text-gray-500">Created: May 2024</span>
                    </div>
                </div>
            </div>

            <!-- Group Card 6 -->
            <div class="group-card bg-white rounded-lg shadow-md overflow-hidden cursor-pointer" onclick="window.location.href='group_details.php?group_id=6'">
                <div class="bg-royal-blue p-4">
                    <h3 class="text-white text-xl font-semibold">Zeta Pioneers</h3>
                    <p class="text-royal-blue-light text-sm">Cybersecurity Research</p>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                        </svg>
                        <span class="text-sm text-gray-600">3 members</span>
                    </div>
                    <div class="space-y-1 mb-4">
                        <p class="text-sm text-gray-700">Oliver King</p>
                        <p class="text-sm text-gray-700">Penny Wright</p>
                        <p class="text-sm text-gray-700">Quinn Lopez</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        <span class="text-sm text-gray-500">Created: Jun 2024</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
