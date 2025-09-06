<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Repository</title>
    <link href="src/output.css" rel="stylesheet">
    <?php include 'src/head-assets.php'; ?>
    <style>
        .research-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .research-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .filter-btn.active {
            background-color: #3b82f6;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'components/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-900 to-blue-700 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">Contribute and Accelerate research process with our Repository</h1>
                    <p class="text-xl mb-8">Holy Cross Colleges provides access to thousands of research papers, literature reviews, to support researchers in the institution.</p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="#research-papers" class="bg-white text-blue-800 hover:bg-gray-100 px-6 py-3 rounded-lg text-center font-semibold">
                            Explore Research <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <a href="#" class="border border-white hover:bg-blue-600 px-6 py-3 rounded-lg text-center">
                            Learn More
                        </a>
                    </div>
                </div>
                <!-- logo may be here -->
            </div>
        </div>
    </section>



    <!-- Research Papers Section -->
    <section id="research-papers" class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-4">Browse Research Papers</h2>
            <p class="text-gray-600 text-center max-w-3xl mx-auto mb-8">Access thousands of research papers across various disciplines. Filter by field, year, or popularity.</p>
            
            <!-- Filter Section -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-8">
                <h3 class="text-lg font-semibold mb-4">Filter Research Papers</h3>
                <div class="flex flex-wrap gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Field of Study</label>
                        <select class="w-full md:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option>All Fields</option>
                            <option>Agriculture</option>
                            <option>Medicine & Health</option>
                            <option>Engineering</option>
                            <option>Social Studies</option>
                            <option>Environmental Science</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Publication Year</label>
                        <select class="w-full md:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option>All Years</option>
                            <option>2023</option>
                            <option>2022</option>
                            <option>2021</option>
                            <option>2020</option>
                            <option>2019</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                        <select class="w-full md:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option>Most Recent</option>
                            <option>Most Viewed</option>
                            <option>Most Liked</option>
                            <option>Alphabetical</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                            Apply Filters
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Research Papers Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Paper 1 -->
                <div class="research-card bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Computer Science</span>
                            <span class="text-gray-500 text-sm">2023</span>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Advanced Machine Learning Techniques for Natural Language Processing</h3>
                        <p class="text-gray-600 text-sm mb-4">This paper explores state-of-the-art ML techniques applied to NLP tasks, demonstrating significant improvements in accuracy and efficiency.</p>
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <span><i class="fas fa-eye mr-1"></i> 28</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">By: Zhang et al.</span>
                            <div class="flex space-x-2">
                                <button class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Paper 2 -->
                <div class="research-card bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Medicine</span>
                            <span class="text-gray-500 text-sm">2023</span>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Novel Approaches to Targeted Cancer Therapies</h3>
                        <p class="text-gray-600 text-sm mb-4">This research presents breakthrough findings in targeted cancer treatments with reduced side effects and improved patient outcomes.</p>
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <span><i class="fas fa-eye mr-1"></i> 45</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">By: Johnson et al.</span>
                            <div class="flex space-x-2">
                                <button class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Paper 3 -->
                <div class="research-card bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Energy</span>
                            <span class="text-gray-500 text-sm">2022</span>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Next-Generation Solar Cell Efficiency Improvements</h3>
                        <p class="text-gray-600 text-sm mb-4">Study demonstrates a new approach to photovoltaic cell design that increases energy conversion efficiency by 28% compared to current models.</p>
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <span><i class="fas fa-eye mr-1"></i> 36</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">By: Müller et al.</span>
                            <div class="flex space-x-2">
                                <button class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Paper 4 -->
                <div class="research-card bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Social Sciences</span>
                            <span class="text-gray-500 text-sm">2023</span>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Impact of Remote Work on Team Collaboration</h3>
                        <p class="text-gray-600 text-sm mb-4">Longitudinal study examining how remote work arrangements affect team dynamics, productivity, and innovation across industries.</p>
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <span><i class="fas fa-eye mr-1"></i> 22</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">By: Rodriguez et al.</span>
                            <div class="flex space-x-2">
                                <button class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Paper 5 -->
                <div class="research-card bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Environment</span>
                            <span class="text-gray-500 text-sm">2022</span>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Climate Change Effects on Coastal Ecosystems</h3>
                        <p class="text-gray-600 text-sm mb-4">Comprehensive analysis of how rising sea levels and changing temperatures are affecting biodiversity in coastal regions worldwide.</p>
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <span><i class="fas fa-eye mr-1"></i> 67</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">By: Tanaka et al.</span>
                            <div class="flex space-x-2">
                                <button class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Paper 6 -->
                <div class="research-card bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full">Engineering</span>
                            <span class="text-gray-500 text-sm">2023</span>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Sustainable Materials for Construction in Extreme Environments</h3>
                        <p class="text-gray-600 text-sm mb-4">Development and testing of new composite materials that maintain structural integrity in extreme temperatures and conditions.</p>
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <span><i class="fas fa-eye mr-1"></i> 19</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">By: Ivanov et al.</span>
                            <div class="flex space-x-2">
                                <button class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-8">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
                    Load More Research Papers
                </button>
            </div>
        </div>
    </section>


    <!-- CTA Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-6">Ready to Accelerate Your Research?</h2>
            <p class="text-gray-600 max-w-2xl mx-auto mb-8">...</p>
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold">
                    Login To Your Account
                </a>
                <a href="#research-papers" class="border border-blue-600 text-blue-600 hover:bg-blue-50 px-8 py-3 rounded-lg">
                    Browse Research Repository
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>

</body>
</html>