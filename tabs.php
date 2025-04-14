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
                        <?php
                        include_once "notifications.php";
                        $unreadCount = getUnreadNotifications($conn, $_SESSION['username'], $_SESSION['org_id']);
                        if ($unreadCount > 0):
                        ?>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"><?php echo $unreadCount; ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="ml-2">Notifications</span>
                </div>
            </button>
            <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50">
                <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-800">Notifications</h3>
                    <?php if ($unreadCount > 0): ?>
                    <button onclick="markAllAsRead()" class="text-sm text-bedan-red hover:underline">Mark all as read</button>
                    <?php endif; ?>
                </div>
                <div class="max-h-64 overflow-y-auto">
                    <?php
                    $notifications = getNotifications($conn, $_SESSION['username'], $_SESSION['org_id']);
                    if ($notifications && mysqli_num_rows($notifications) > 0):
                        while ($notification = mysqli_fetch_assoc($notifications)):
                            $isUnread = $notification['is_read'] == 0;
                            $taskUrl = "#";
                            if ($notification['type'] == 'task' || $notification['type'] == 'task_update' || $notification['type'] == 'task_assignment') {
                                $taskUrl = "viewtask.php?id=" . $notification['related_id'];
                            }
                    ?>
                    <a href="<?php echo $taskUrl; ?>" class="block p-4 border-b border-gray-200 hover:bg-gray-50 <?php echo $isUnread ? 'bg-gray-50' : ''; ?>"
                       data-notification-id="<?php echo $notification['notification_id']; ?>">
                        <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($notification['message']); ?></p>
                        <p class="text-xs text-gray-500"><?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?></p>
                    </a>
                    <?php
                        endwhile;
                    else:
                    ?>
                    <div class="p-4 text-center text-gray-500">
                        No notifications yet
                    </div>
                    <?php endif; ?>
                </div>
                <div class="p-2 text-center border-t border-gray-200">
                    <a href="view_all_notifications.php" class="text-sm text-bedan-red hover:underline">View all notifications</a>
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

<script>
// Remove the duplicate notification dropdown code and keep only these functions
function markAsRead(notificationId, event) {
    // Don't interfere with the link click if it's a task notification
    if (!event.currentTarget.href.includes('viewtask.php')) {
        event.preventDefault();
    }
    
    fetch('mark_notification_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'notification_id=' + notificationId
    }).then(response => {
        // Only reload if not navigating to a task
        if (!event.currentTarget.href.includes('viewtask.php')) {
            location.reload();
        }
    });
}

function markAllAsRead() {
    fetch('mark_all_notifications_read.php', {
        method: 'POST'
    }).then(() => {
        location.reload();
    });
}
</script>