<?php
session_start();
include "db_conn.php";  // $conn will be null

 
if (isset($_SESSION["username"]) && isset($_SESSION["org_id"])) { 
?>
<!DOCTYPE html>
<html>
    <head>  
        <title>Dashboard - The Bedan Herald</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            'bedan-red': '#9B1919',
                            'bedan-red-light': '#cc2424',
                        }
                    }
                }
            }
        </script>
    </head>

    <body class="bg-gray-50 min-h-screen">
        <!-- Header Navigation -->
        <nav class="bg-gradient-to-r from-bedan-red to-bedan-red-light fixed w-full top-0 z-50 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex-shrink-0">
                        <a href="Home2.php">
                            <img class="h-12 w-auto" src="The Bedan Herald.png" alt="Logo">
                        </a>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-center space-x-4">
                            <?php if ($_SESSION["position"] == 'President' || $_SESSION["position"] == 'Head Admin' || $_SESSION["position"] == 'Admin'){ ?>
                                <a href="Home2.php" class="text-white bg-white/20 px-3 py-2 rounded-md text-sm font-medium transition-all">
                                    <i class="fas fa-home mr-2"></i>Assignments
                                </a>
                                <a href="AssignTasks.php" class="text-white hover:bg-white/20 px-3 py-2 rounded-md text-sm font-medium transition-all">
                                    <i class="fas fa-tasks mr-2"></i>Assign Tasks
                                </a>
                                <a href="OverallTasks.php" class="text-white hover:bg-white/20 px-3 py-2 rounded-md text-sm font-medium transition-all">
                                    <i class="fas fa-chart-line mr-2"></i>Progress Overview
                                </a>
                            <?php } 
                            if ($_SESSION["position"] == 'Head Admin' || $_SESSION["position"] == 'Human Resources'){ ?>
                                <a href="AddAccount.php" class="text-white hover:bg-white/20 px-3 py-2 rounded-md text-sm font-medium transition-all">
                                    <i class="fas fa-user-plus mr-2"></i>Create Accounts
                                </a>
                            <?php } ?>
                            
                            <!-- Notification Button -->
                            <div class="relative">
                                <button id="notificationButton" class="text-white hover:bg-white/20 px-3 py-2 rounded-md text-sm font-medium transition-all">
                                    <i class="fas fa-bell mr-2"></i>Notifications
                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                                </button>
                                <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50">
                                    <div class="p-3 border-b border-gray-200">
                                        <h3 class="text-lg font-medium text-gray-800">Notifications</h3>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto">
                                        <a href="#" class="block p-4 border-b border-gray-200 hover:bg-gray-50">
                                            <p class="text-sm font-medium text-gray-800">New article assigned</p>
                                            <p class="text-xs text-gray-500">10 minutes ago</p>
                                        </a>
                                        <a href="#" class="block p-4 border-b border-gray-200 hover:bg-gray-50">
                                            <p class="text-sm font-medium text-gray-800">Your article has been reviewed</p>
                                            <p class="text-xs text-gray-500">2 hours ago</p>
                                        </a>
                                        <a href="#" class="block p-4 border-b border-gray-200 hover:bg-gray-50">
                                            <p class="text-sm font-medium text-gray-800">Team meeting reminder</p>
                                            <p class="text-xs text-gray-500">1 day ago</p>
                                        </a>
                                    </div>
                                    <div class="p-2 text-center border-t border-gray-200">
                                        <a href="#" class="text-sm text-bedan-red hover:underline">View all notifications</a>
                                    </div>
                                </div>
                            </div>
                            
                            <a href="LogOut.php" class="text-white bg-red-700 hover:bg-red-800 px-4 py-2 rounded-md text-sm font-medium transition-all">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-6">
            <!-- Welcome Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
                <span class="inline-block bg-bedan-red text-white text-sm px-3 py-1 rounded-full mt-2">
                    <?php echo htmlspecialchars($_SESSION['position']); ?>
                </span>
            </div>

            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-md p-6 text-center transform hover:-translate-y-1 transition-all">
                    <i class="fas fa-newspaper text-4xl text-bedan-red mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-700">Active Assignments</h3>
                    <p class="text-3xl font-bold text-bedan-red mt-2">12</p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 text-center transform hover:-translate-y-1 transition-all">
                    <i class="fas fa-clock text-4xl text-bedan-red mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-700">Pending Reviews</h3>
                    <p class="text-3xl font-bold text-bedan-red mt-2">5</p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 text-center transform hover:-translate-y-1 transition-all">
                    <i class="fas fa-check-circle text-4xl text-bedan-red mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-700">Completed Tasks</h3>
                    <p class="text-3xl font-bold text-bedan-red mt-2">8</p>
                </div>
            </div>

            <!-- Assignments Table -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-list mr-2"></i>Current Assignments
                    </h2>
                    <button class="bg-bedan-red hover:bg-bedan-red-light text-white px-4 py-2 rounded-md transition-all">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Article Topic</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Writer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            $mockArticles = [
                                [
                                    'ArticleTopic' => 'Campus Life Post-Pandemic',
                                    'Writer' => 'John Doe',
                                    'Deadline' => '2024-04-01',
                                    'Priority' => 'High',
                                    'Status' => 'In Progress'
                                ],
                                [
                                    'ArticleTopic' => 'Student Achievement Spotlight',
                                    'Writer' => 'Jane Smith',
                                    'Deadline' => '2024-04-15',
                                    'Priority' => 'Medium',
                                    'Status' => 'Pending Review'
                                ],
                                [
                                    'ArticleTopic' => 'Faculty Interview Series',
                                    'Writer' => 'Mike Johnson',
                                    'Deadline' => '2024-04-20',
                                    'Priority' => 'Normal',
                                    'Status' => 'Not Started'
                                ]
                            ];

                            foreach ($mockArticles as $article) {
                                $priorityColor = match($article['Priority']) {
                                    'High' => 'bg-red-100 text-red-800',
                                    'Medium' => 'bg-yellow-100 text-yellow-800',
                                    'Normal' => 'bg-green-100 text-green-800',
                                };

                                $statusColor = match($article['Status']) {
                                    'In Progress' => 'bg-blue-100 text-blue-800',
                                    'Pending Review' => 'bg-yellow-100 text-yellow-800',
                                    'Not Started' => 'bg-gray-100 text-gray-800',
                                };

                                echo "<tr class='hover:bg-gray-50'>";
                                echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>{$article['ArticleTopic']}</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>{$article['Writer']}</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>{$article['Deadline']}</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>
                                        <span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full {$priorityColor}'>
                                            {$article['Priority']}
                                        </span>
                                    </td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>
                                        <span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full {$statusColor}'>
                                            {$article['Status']}
                                        </span>
                                    </td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>
                                        <button class='text-blue-600 hover:text-blue-900 mr-2'><i class='fas fa-edit'></i></button>
                                        <button class='text-green-600 hover:text-green-900 mr-2'><i class='fas fa-eye'></i></button>
                                        <button class='text-red-600 hover:text-red-900'><i class='fas fa-trash'></i></button>
                                    </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-8">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
                &copy; All rights reserved.
            </div>
        </footer>
        <script src="notifications.js"></script>
    </body>
</html>
<?php
} else {  
    header("Location: Login.php");
    exit();
}
?>
