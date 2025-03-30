<div class="flex-shrink-0">
    <a href="Home2.php">
        <div class="flex items-center">
            <img class="h-12 w-auto" src="imgs/logo.png" alt="Logo"> 
            <h1 class="ml-3 text-white text-xl font-bold">SBCA Organizational Management System</h1>
        </div>
    </a>
</div>
<div class="hidden md:block">
    <div class="ml-10 flex items-center space-x-2">
        <?php 
        // Get current page filename
        $current_page = basename($_SERVER['PHP_SELF']);
        ?>
        
        <a href="Home2.php" class="text-white <?php echo ($current_page == 'Home2.php') ? 'bg-white/20' : 'hover:bg-white/20'; ?> px-3 py-2 rounded-md text-sm font-medium transition-all">
            <div class="flex items-center">
                <i class="fas fa-home w-5 text-center"></i>
                <span class="ml-2">Assignments</span>
            </div>
        </a>
        <?php if ($_SESSION["position"] == 'President' || $_SESSION["position"] == 'Head Admin' || $_SESSION["position"] == 'OSA'){ ?>
            
            <a href="AssignTasks.php" class="text-white <?php echo ($current_page == 'AssignTasks.php') ? 'bg-white/20' : 'hover:bg-white/20'; ?> px-3 py-2 rounded-md text-sm font-medium transition-all">
                <div class="flex items-center">
                    <i class="fas fa-tasks w-5 text-center"></i>
                    <span class="ml-2">Assign Tasks</span>
                </div>
            </a>
            
            <a href="OverallTasks.php" class="text-white <?php echo ($current_page == 'OverallTasks.php') ? 'bg-white/20' : 'hover:bg-white/20'; ?> px-2 py-2 rounded-md text-sm font-medium transition-all">
                <div class="flex items-center">
                    <i class="fas fa-chart-line w-5 text-center"></i>
                    <span class="ml-2">Progress Overview</span>
                </div>
            </a>
            <a href="AddAccount.php" class="text-white <?php echo ($current_page == 'AddAccount.php') ? 'bg-white/20' : 'hover:bg-white/20'; ?> px-3 py-2 rounded-md text-sm font-medium transition-all">
                <div class="flex items-center">
                    <i class="fas fa-user-plus w-5 text-center"></i>
                    <span class="ml-2">Create Accounts</span>
                </div>
            </a>
        <?php } ?>
        
        <!-- Notification Button -->
        <div class="relative">
            <button id="notificationButton" class="text-white hover:bg-white/20 px-3 py-2 rounded-md text-sm font-medium transition-all">
                <div class="flex items-center">
                    <div class="relative">
                        <i class="fas fa-bell w-5 text-center"></i>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                    </div>
                    <span class="ml-2">Notifications</span>
                </div>
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
            <div class="flex items-center">
                <i class="fas fa-sign-out-alt w-5 text-center"></i>
                <span class="ml-2">Logout</span>
            </div>
        </a>
    </div>
</div>