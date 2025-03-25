<div class="flex-shrink-0">
                        <a href="Home2.php">
                            <div class="grid grid-cols-7 align-bottom">
                                <img class="h-12 w-auto" src="imgs/logo.png" alt="Logo"> 
                                <h1 class="col-span-6 align-middle text-white text-xl font-bold">SBCA Organizational Management System</h1>
                            </div>
                        </a>
                    </div>
                    <div class="hidden md:block">
    <div class="ml-10 flex items-center space-x-2">
        <a href="Home2.php" class="text-white bg-white/20 px-3 py-2 rounded-md text-sm font-medium transition-all">
            <div class="grid grid-cols-4">
                <i class="fas fa-home mr-2"></i>
                <div class="col-span-3"> Assignments </div>
            </div>
        </a>
        <?php if ($_SESSION["position"] == 'President' || $_SESSION["position"] == 'Head Admin' || $_SESSION["position"] == 'OSA'){ ?>
            
            <a href="AssignTasks.php" class="col-span-3 text-white hover:bg-white/20 px-3 py-2 rounded-md text-sm font-medium transition-all">
                <div class="grid grid-cols-4">
                    <i class="fas fa-tasks mr-2"></i>
                    <div class="col-span-3"> Assign Tasks </div>
                </div>
            </a>
            
            <a href="OverallTasks.php" class="text-white hover:bg-white/20 px-2 py-2 rounded-md text-sm font-medium transition-all">
                <div class="grid grid-cols-4">
                <i class="fas fa-chart-line mr-2"></i>
                <div class="col-span-3"> Progress Overview </div>
                </div>
            </a>
            <a href="AddAccount.php" class="text-white hover:bg-white/20 px-3 py-2 rounded-md text-sm font-medium transition-all">
                <div class="grid grid-cols-4">
                    <i class="fas fa-user-plus mr-2"></i>
                    <div class="col-span-3"> Create Accounts </div>
                </div>
                
            </a>
        <?php } ?>
        
        <!-- Notification Button -->
        <div class="relative">
            <button id="notificationButton" class="text-white hover:bg-white/20 px-3 py-2 rounded-md text-sm font-medium transition-all">
                <i class="fas fa-bell mr-2"></i>Notifications
                <span class="absolute top-1 right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
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