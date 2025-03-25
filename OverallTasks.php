<?php
session_start();
include "db_conn.php";

if (!isset($_SESSION["MemberID"])) {
    $_SESSION['username'] = "admin";
    $_SESSION['name'] = "Admin User";
    $_SESSION['MemberID'] = "001";
    $_SESSION['position'] = "Head Admin";
}

if (isset($_SESSION["MemberID"]) && isset($_SESSION["name"])) { 
?>
<!DOCTYPE html>
<html>
    <head>  
        <title>Progress Overview - The Bedan Herald</title>
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
                            <?php if ($_SESSION["position"] == 'Section Editor' || $_SESSION["position"] == 'Head Admin' || $_SESSION["position"] == 'Admin'){ ?>
                                <a href="Home2.php" class="text-white hover:bg-white/20 px-3 py-2 rounded-md text-sm font-medium transition-all">
                                    <i class="fas fa-home mr-2"></i>Assignments
                                </a>
                                <a href="AssignTasks.php" class="text-white hover:bg-white/20 px-3 py-2 rounded-md text-sm font-medium transition-all">
                                    <i class="fas fa-tasks mr-2"></i>Assign Tasks
                                </a>
                                <a href="OverallTasks.php" class="text-white bg-white/20 px-3 py-2 rounded-md text-sm font-medium transition-all">
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
            <!-- Page Title -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Progress Overview</h1>
                <p class="text-gray-600 mt-2">Track all assignments and their current status</p>
            </div>

            <!-- Filters and Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Filter Section -->
                <div class="md:col-span-1 bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Filters</h2>
                    <form class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Section</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <option value="">All Sections</option>
                                <option value="News">News</option>
                                <option value="Features">Features</option>
                                <option value="Sports">Sports</option>
                                <option value="Editorial">Editorial</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <option value="">All Status</option>
                                <option value="Not Started">Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Pending Review">Pending Review</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-bedan-red hover:bg-bedan-red-light text-white px-4 py-2 rounded-md text-sm font-medium transition-all">
                            Apply Filters
                        </button>
                    </form>
                </div>

                <!-- Statistics Cards -->
                <div class="md:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                                <i class="fas fa-tasks text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-sm font-medium text-gray-500">Total Tasks</h3>
                                <p class="text-2xl font-semibold text-gray-900">25</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-sm font-medium text-gray-500">Completed</h3>
                                <p class="text-2xl font-semibold text-gray-900">8</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-sm font-medium text-gray-500">Pending</h3>
                                <p class="text-2xl font-semibold text-gray-900">17</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks Table -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">All Tasks</h2>
                    <button class="bg-bedan-red hover:bg-bedan-red-light text-white px-4 py-2 rounded-md text-sm font-medium transition-all">
                        <i class="fas fa-download mr-2"></i>Export Report
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Article ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Writer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Topic</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            // Mock data for tasks
                            $mockTasks = [
                                [
                                    'id' => 'ART001',
                                    'writer' => 'John Doe',
                                    'section' => 'News',
                                    'topic' => 'Campus Events Coverage',
                                    'deadline' => '2024-04-01',
                                    'status' => 'In Progress'
                                ],
                                [
                                    'id' => 'ART002',
                                    'writer' => 'Jane Smith',
                                    'section' => 'Features',
                                    'topic' => 'Student Life Series',
                                    'deadline' => '2024-04-15',
                                    'status' => 'Pending Review'
                                ],
                                [
                                    'id' => 'ART003',
                                    'writer' => 'Mike Johnson',
                                    'section' => 'Sports',
                                    'topic' => 'Basketball Tournament',
                                    'deadline' => '2024-04-20',
                                    'status' => 'Completed'
                                ]
                            ];

                            foreach ($mockTasks as $task) {
                                $statusColor = match($task['status']) {
                                    'In Progress' => 'bg-blue-100 text-blue-800',
                                    'Pending Review' => 'bg-yellow-100 text-yellow-800',
                                    'Completed' => 'bg-green-100 text-green-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };

                                echo "<tr class='hover:bg-gray-50'>";
                                echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>{$task['id']}</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$task['writer']}</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$task['section']}</td>";
                                echo "<td class='px-6 py-4 text-sm text-gray-500'>{$task['topic']}</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$task['deadline']}</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>
                                        <span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full {$statusColor}'>
                                            {$task['status']}
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