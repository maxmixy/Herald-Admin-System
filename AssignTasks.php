<?php
ob_start();
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
        <title>Assign Tasks - The Bedan Herald</title>
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
                                <a href="AssignTasks.php" class="text-white bg-white/20 px-3 py-2 rounded-md text-sm font-medium transition-all">
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
            <!-- Page Title -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Assign New Task</h1>
                <p class="text-gray-600 mt-2">Create and assign new tasks to team members</p>
            </div>

            <!-- Assignment Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="#" method="post" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Article Topic -->
                        <div>
                            <label for="topic" class="block text-sm font-medium text-gray-700">Article Topic</label>
                            <input type="text" name="topic" id="topic" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"
                                placeholder="Enter article topic">
                        </div>

                        <!-- Section -->
                        <div>
                            <label for="section" class="block text-sm font-medium text-gray-700">Section</label>
                            <select name="section" id="section" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <option value="">Select section</option>
                                <option value="News">News</option>
                                <option value="Features">Features</option>
                                <option value="Sports">Sports</option>
                                <option value="Editorial">Editorial</option>
                            </select>
                        </div>

                        <!-- Assign To -->
                        <div>
                            <label for="writer" class="block text-sm font-medium text-gray-700">Assign To</label>
                            <select name="writer" id="writer" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <option value="">Select writer</option>
                                <option value="1">John Doe</option>
                                <option value="2">Jane Smith</option>
                                <option value="3">Mike Johnson</option>
                            </select>
                        </div>

                        <!-- Deadline -->
                        <div>
                            <label for="deadline" class="block text-sm font-medium text-gray-700">Deadline</label>
                            <input type="date" name="deadline" id="deadline" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                        </div>

                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                            <select name="priority" id="priority" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <option value="Normal">Normal</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Initial Status</label>
                            <select name="status" id="status" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <option value="Not Started">Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Pending Review">Pending Review</option>
                            </select>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="4" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"
                            placeholder="Enter any additional notes or instructions"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" 
                            class="bg-bedan-red hover:bg-bedan-red-light text-white px-6 py-2 rounded-md text-sm font-medium transition-all">
                            <i class="fas fa-plus mr-2"></i>Assign Task
                        </button>
                    </div>
                </form>
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