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
            <div class="max-w-2x3 mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <?php include "tabs.php"; ?>
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
                    <?php
                    // Query to count active tasks in user's organization
                    $countQuery = "SELECT COUNT(*) as total FROM tasks t 
                                  JOIN users u ON t.member_id = u.username 
                                  WHERE u.org_id = '{$_SESSION['org_id']}' AND t.status != 'Completed'";
                    $countResult = mysqli_query($conn, $countQuery);
                    $totalTasks = ($countResult) ? mysqli_fetch_assoc($countResult)['total'] : 0;
                    ?>
                    <p class="text-3xl font-bold text-bedan-red mt-2"><?php echo $totalTasks; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 text-center transform hover:-translate-y-1 transition-all">
                    <i class="fas fa-clock text-4xl text-bedan-red mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-700">Pending Reviews</h3>
                    <?php
                    // Query to count pending review tasks in user's organization
                    $pendingQuery = "SELECT COUNT(*) as pending FROM tasks t 
                                    JOIN users u ON t.member_id = u.username 
                                    WHERE u.org_id = '{$_SESSION['org_id']}' AND t.status = 'Pending Review'";
                    $pendingResult = mysqli_query($conn, $pendingQuery);
                    $pendingTasks = ($pendingResult) ? mysqli_fetch_assoc($pendingResult)['pending'] : 0;
                    ?>
                    <p class="text-3xl font-bold text-bedan-red mt-2"><?php echo $pendingTasks; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 text-center transform hover:-translate-y-1 transition-all">
                    <i class="fas fa-check-circle text-4xl text-bedan-red mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-700">Completed Tasks</h3>
                    <?php
                    // Query to count completed tasks in user's organization
                    $completedQuery = "SELECT COUNT(*) as completed FROM tasks t 
                                      JOIN users u ON t.member_id = u.username 
                                      WHERE u.org_id = '{$_SESSION['org_id']}' AND t.status = 'Completed'";
                    $completedResult = mysqli_query($conn, $completedQuery);
                    $completedTasks = ($completedResult) ? mysqli_fetch_assoc($completedResult)['completed'] : 0;
                    ?>
                    <p class="text-3xl font-bold text-bedan-red mt-2"><?php echo $completedTasks; ?></p>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            // Query the tasks table with organization filter
                            $sql = "SELECT t.*, u.name as assigned_to, u.department 
                                   FROM tasks t
                                   JOIN users u ON t.member_id = u.username
                                   WHERE u.org_id = '{$_SESSION['org_id']}'
                                   ORDER BY t.deadline ASC";
                            $result = mysqli_query($conn, $sql);
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($task = mysqli_fetch_assoc($result)) {
                                    $priorityColor = match($task['priority']) {
                                        'High' => 'bg-red-100 text-red-800',
                                        'Medium' => 'bg-yellow-100 text-yellow-800',
                                        'Normal', 'Low' => 'bg-green-100 text-green-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };

                                    $statusColor = match($task['status']) {
                                        'In Progress' => 'bg-blue-100 text-blue-800',
                                        'Pending Review' => 'bg-yellow-100 text-yellow-800',
                                        'Completed' => 'bg-green-100 text-green-800',
                                        'Not Started' => 'bg-gray-100 text-gray-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };

                                    echo "<tr class='hover:bg-gray-50'>";
                                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>TASK" . str_pad($task['task_id'], 3, '0', STR_PAD_LEFT) . "</td>";
                                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>{$task['task_title']}</td>";
                                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>{$task['assigned_to']} ({$task['department']})</td>";
                                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>" . date('M d, Y', strtotime($task['deadline'])) . "</td>";
                                    echo "<td class='px-6 py-4 whitespace-nowrap'>
                                            <span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full {$priorityColor}'>
                                                {$task['priority']}
                                            </span>
                                        </td>";
                                    echo "<td class='px-6 py-4 whitespace-nowrap'>
                                            <span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full {$statusColor}'>
                                                {$task['status']}
                                            </span>
                                        </td>";
                                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>
                                            <a href='edittask.php?id={$task['task_id']}' class='text-blue-600 hover:text-blue-900 mr-2'><i class='fas fa-edit'></i></a>
                                            <a href='viewtask.php?id={$task['task_id']}' class='text-green-600 hover:text-green-900 mr-2'><i class='fas fa-eye'></i></a>
                                            <a href='deletetask.php?id={$task['task_id']}' class='text-red-600 hover:text-red-900' onclick=\"return confirm('Are you sure you want to delete this task?')\"><i class='fas fa-trash'></i></a>
                                        </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='px-6 py-4 text-center text-gray-500'>No tasks found</td></tr>";
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
