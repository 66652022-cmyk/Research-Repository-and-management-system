<nav class="bg-blue-800 text-white shadow-lg">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-2">
                <i class="fas fa-book-open text-2xl"></i>
                <span class="text-xl font-bold">HCC Inc. Management and Research Repository</span>
            </div>
            <div class="hidden md:flex space-x-8">
                <a href="/THESIS/index.php" class="hover:text-blue-200">Home</a>
                <a href="#" class="hover:text-blue-200">Archives</a>
                <a href="#" class="hover:text-blue-200">Search</a>
                <a href="#" class="hover:text-blue-200">About</a>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <a href="/THESIS/pages/login.php" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg">Log-In</a>
            </div>
            <button id="burger-btn" class="md:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
        </div>
    </div>

    <div id="sidebar" class="fixed inset-0 bg-opacity-90 z-50 hidden transition-transform duration-200 ease-out">
        <div class="absolute top-0 right-0 h-full w-64 bg-blue-900 flex flex-col p-6 space-y-6">
            <button id="close-sidebar" class="self-end text-white text-2xl mb-4">&times;</button>
            <a href="../index.php" class="hover:text-blue-200">Home</a>
            <a href="#" class="hover:text-blue-200">Archives</a>
            <a href="#" class="hover:text-blue-200">Search</a>
            <a href="#" class="hover:text-blue-200">About</a>
            <a href="/THESIS/pages/Login.php" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg">Log-in</a>
        </div>
    </div>
    <script src="/THESIS/js/sidebar.js"></script>
</nav>