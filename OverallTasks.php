<?php
session_start();
include "db_conn.php";

if (isset($_SESSION["username"]) && isset($_SESSION["org_id"])) { 
    // Build the base query
    $sql = "SELECT t.task_id, t.task_title, t.deadline, t.priority, t.status, 
                   t.task_details, t.link, u.name, u.department
            FROM tasks t
            JOIN users u ON t.member_id = u.username
            WHERE u.org_id = ?";
    
    // Add filters if they exist
    $params = [$_SESSION['org_id']];
    $types = "s";
    
    if (isset($_GET['department']) && $_GET['department'] != '') {
        $sql .= " AND u.department = ?";
        $params[] = $_GET['department'];
        $types .= "s";
    }
    
    if (isset($_GET['status']) && $_GET['status'] != '') {
        $sql .= " AND t.status = ?";
        $params[] = $_GET['status'];
        $types .= "s";
    }
    
    // Get filtered tasks
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = $result->fetch_all(MYSQLI_ASSOC);

    // Get task statistics (filtered if needed)
    $statsSql = "SELECT 
                COUNT(*) AS total_tasks,
                SUM(CASE WHEN t.status = 'Completed' THEN 1 ELSE 0 END) AS completed,
                SUM(CASE WHEN t.status != 'Completed' THEN 1 ELSE 0 END) AS pending
                FROM tasks t
                JOIN users u ON t.member_id = u.username
                WHERE u.org_id = ?";
    
    if (isset($_GET['department']) && $_GET['department'] != '') {
        $statsSql .= " AND u.department = ?";
    }
    if (isset($_GET['status']) && $_GET['status'] != '') {
        $statsSql .= " AND t.status = ?";
    }
    
    $statsStmt = $conn->prepare($statsSql);
    $statsStmt->bind_param($types, ...$params);
    $statsStmt->execute();
    $statsResult = $statsStmt->get_result();
    $stats = $statsResult->fetch_assoc();
?>
<!DOCTYPE html>
<html>
    <head>  
        <title>Progress Overview - <?php echo htmlspecialchars($_SESSION['name']); ?></title>
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
                    <form method="GET" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department</label>
                            <select name="department" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <option value="">All Departments</option>
                                <?php
                                // Get unique departments from users
                                $deptStmt = $conn->prepare("SELECT DISTINCT department FROM users WHERE org_id = ?");
                                $deptStmt->bind_param("s", $_SESSION['org_id']);
                                $deptStmt->execute();
                                $deptResult = $deptStmt->get_result();
                                
                                while ($dept = $deptResult->fetch_assoc()) {
                                    $selected = (isset($_GET['department']) && $_GET['department'] == $dept['department']) ? 'selected' : '';
                                    echo "<option value=\"{$dept['department']}\" $selected>{$dept['department']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <option value="">All Status</option>
                                <option value="Not Started" <?= (isset($_GET['status']) && $_GET['status'] == 'Not Started') ? 'selected' : '' ?>>Not Started</option>
                                <option value="In Progress" <?= (isset($_GET['status']) && $_GET['status'] == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                                <option value="Pending Review" <?= (isset($_GET['status']) && $_GET['status'] == 'Pending Review') ? 'selected' : '' ?>>Pending Review</option>
                                <option value="Completed" <?= (isset($_GET['status']) && $_GET['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-bedan-red hover:bg-bedan-red-light text-white px-4 py-2 rounded-md text-sm font-medium transition-all">
                            Apply Filters
                        </button>
                        <?php if (isset($_GET['department']) || isset($_GET['status'])): ?>
                        <a href="overalltasks.php" class="block text-center text-sm text-bedan-red hover:text-bedan-red-light">
                            Clear Filters
                        </a>
                        <?php endif; ?>
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
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['total_tasks']; ?></p>
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
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['completed']; ?></p>
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
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['pending']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks Table -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">All Tasks</h2>
                    <a href="export_tasks.php<?php echo isset($_GET['department']) || isset($_GET['status']) ? '?' . http_build_query($_GET) : ''; ?>" class="bg-bedan-red hover:bg-bedan-red-light text-white px-4 py-2 rounded-md text-sm font-medium transition-all">
                        <i class="fas fa-download mr-2"></i>Export Report
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($tasks)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No tasks found
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tasks as $task): 
                                    $statusColor = match($task['status']) {
                                        'In Progress' => 'bg-blue-100 text-blue-800',
                                        'Pending Review' => 'bg-yellow-100 text-yellow-800',
                                        'Completed' => 'bg-green-100 text-green-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                ?>
                                <tr class='hover:bg-gray-50'>
                                    <td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>TASK<?php echo str_pad($task['task_id'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'><?php echo htmlspecialchars($task['name']); ?></td>
                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'><?php echo htmlspecialchars($task['department']); ?></td>
                                    <td class='px-6 py-4 text-sm text-gray-500'><?php echo htmlspecialchars($task['task_title']); ?></td>
                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'><?php echo date('M d, Y', strtotime($task['deadline'])); ?></td>
                                    <td class='px-6 py-4 whitespace-nowrap'>
                                        <span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusColor; ?>'>
                                            <?php echo $task['status']; ?>
                                        </span>
                                    </td>
                                    <td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>
                                        <a href="edittask.php?id=<?php echo $task['task_id']; ?>" class='text-blue-600 hover:text-blue-900 mr-2'><i class='fas fa-edit'></i></a>
                                        <a href="viewtask.php?id=<?php echo $task['task_id']; ?>" class='text-green-600 hover:text-green-900 mr-2'><i class='fas fa-eye'></i></a>
                                        <a href="deletetask.php?id=<?php echo $task['task_id']; ?>" class='text-red-600 hover:text-red-900' onclick="return confirm('Are you sure you want to delete this task?')"><i class='fas fa-trash'></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
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