<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel with Dynamic Content</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .royal-blue-light {
            background-color: #4169E1;
            opacity: 0.1;
        }
        .nav-item.active {
            background-color: #4169E1;
            color: white;
        }
        .nav-item:hover {
            background-color: #4169E1;
            opacity: 0.8;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg">
            <div class="p-4">
                <h2 class="text-xl font-semibold mb-6">Admin Panel</h2>
                <nav class="space-y-2" aria-label="Sidebar Navigation">
                    <!-- Internal sections that change main content -->
                    <a href="#" onclick="showSection('dashboard')" class="nav-item flex items-center p-3 rounded-lg transition-colors active">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="#" onclick="showSection('emails')" class="nav-item flex items-center p-3 rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        User Emails
                    </a>
                    <a href="#" onclick="showSection('reset-password')" class="nav-item flex items-center p-3 rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"/>
                        </svg>
                        Reset Passwords
                    </a>
                    
                    <!-- Separator -->
                    <hr class="my-4 border-gray-200">
                    <p class="text-sm text-gray-500 px-3 mb-2">External Dashboards</p>
                    
                    <!-- External dashboard links -->
                    <a href="/THESIS/dashboards/adviser_dash.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-blue-600 hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                        </svg>
                        Adviser
                    </a>
                    <a href="/THESIS/dashboards/critique_dash.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-blue-600 hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z"/>
                        </svg>
                        English Critique
                    </a>
                    <a href="/THESIS/dashboards/finance_dash.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-blue-600 hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"/>
                        </svg>
                        Financial Analyst
                    </a>
                    <a href="/THESIS/dashboards/faculty_dash.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-blue-600 hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                        </svg>
                        Research Faculty
                    </a>
                    <a href="/THESIS/dashboards/research_director_dash.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-blue-600 hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z"/>
                        </svg>
                        Research Director
                    </a>
                    <a href="/THESIS/dashboards/statistician_dash.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-blue-600 hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                        </svg>
                        Statistician
                    </a>
                    <a href="/THESIS/dashboards/student_dash.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-blue-600 hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.75 2.524z"/>
                        </svg>
                        Student
                    </a>

                    <!-- Logout -->
                    <hr class="my-4 border-gray-200">
                    <a href="#" onclick="confirmLogout()" class="nav-item flex items-center p-3 rounded-lg hover:bg-red-600 hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 01-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"/>
                        </svg>
                        Logout
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 p-8">
            <!-- Dashboard Section -->
            <div id="dashboard" class="content-section">
                <div class="bg-white rounded-lg shadow p-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-4">Dashboard</h1>
                    <p class="text-gray-600">Welcome to the admin dashboard. Here you can manage your system.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-blue-800">Total Users</h3>
                            <p class="text-2xl font-bold text-blue-600">1,234</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-green-800">Active Sessions</h3>
                            <p class="text-2xl font-bold text-green-600">856</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-yellow-800">Pending Tasks</h3>
                            <p class="text-2xl font-bold text-yellow-600">23</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emails Section -->
            <div id="emails" class="content-section hidden">
                <div class="bg-white rounded-lg shadow p-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-4">User Emails</h1>
                    <p class="text-gray-600 mb-4">Manage user email addresses and communication.</p>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold">Email Management</h3>
                            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add New Email</button>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between items-center p-3 bg-white rounded">
                                <span>john.doe@example.com</span>
                                <div class="space-x-2">
                                    <button class="text-blue-600 hover:underline">Edit</button>
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </div>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-white rounded">
                                <span>jane.smith@example.com</span>
                                <div class="space-x-2">
                                    <button class="text-blue-600 hover:underline">Edit</button>
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reset Password Section -->
            <div id="reset-password" class="content-section hidden">
                <div class="bg-white rounded-lg shadow p-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-4">Reset Passwords</h1>
                    <p class="text-gray-600 mb-4">Manage password reset requests and user authentication.</p>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold mb-4">Password Reset Tools</h3>
                        
                        <div class="space-y-4">
                            <div class="bg-white p-4 rounded">
                                <h4 class="font-medium mb-2">Reset User Password</h4>
                                <div class="flex gap-2">
                                    <input type="email" placeholder="Enter user email" class="flex-1 border rounded px-3 py-2">
                                    <button class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">Send Reset Link</button>
                                </div>
                            </div>
                            
                            <div class="bg-white p-4 rounded">
                                <h4 class="font-medium mb-2">Pending Reset Requests</h4>
                                <div class="text-sm text-gray-600">
                                    <p>• user1@example.com - Requested 2 hours ago</p>
                                    <p>• user2@example.com - Requested 5 hours ago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showSection(sectionName) {
            // Hide all content sections
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => {
                section.classList.add('hidden');
            });
            
            // Show the selected section
            const selectedSection = document.getElementById(sectionName);
            if (selectedSection) {
                selectedSection.classList.remove('hidden');
            }
            
            // Update navigation active state
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to clicked item (only for internal sections)
            event.target.closest('a').classList.add('active');
        }

        function confirmLogout() {
            if (confirm('Are you sure you want to logout?')) {
                // Add your logout logic here
                alert('Logout functionality would be implemented here');
                // window.location.href = '/logout.php';
            }
        }

        // Initialize dashboard as active on page load
        document.addEventListener('DOMContentLoaded', function() {
            showSection('dashboard');
        });
    </script>
</body>
</html>