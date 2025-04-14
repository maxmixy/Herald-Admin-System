<?php
session_start();
include "db_conn.php";

if (!isset($_SESSION["username"]) || !isset($_SESSION["org_id"])) {
    header("Location: Login.php");
    exit();
}

// Check if task ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: Home2.php");
    exit();
}

$task_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch the task details
$sql = "SELECT t.*, u.name as assigned_to, u.department 
        FROM tasks t
        JOIN users u ON t.member_id = u.username
        WHERE t.task_id = ? AND u.org_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $task_id, $_SESSION['org_id']);
$stmt->execute();
$result = $stmt->get_result();

// Check if task exists and belongs to user's organization
if ($result->num_rows === 0) {
    $_SESSION['error'] = "Task not found or you don't have permission to view it";
    header("Location: Home2.php");
    exit();
}

$task = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
    <head>  
        <title>View Task - The Bedan Herald</title>
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
            <!-- Back Button -->
            <div class="mb-6">
                <a href="Home2.php" class="inline-flex items-center text-bedan-red hover:text-bedan-red-light">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>
            
            <!-- Task Details Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Task Header -->
                <div class="bg-gradient-to-r from-bedan-red to-bedan-red-light p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold text-white">
                                <?php echo htmlspecialchars($task['task_title']); ?>
                            </h1>
                            <p class="text-white opacity-90 mt-1">
                                Task ID: TASK<?php echo str_pad($task['task_id'], 3, '0', STR_PAD_LEFT); ?>
                            </p>
                        </div>
                        
                        <?php
                        // Define status color
                        $statusColor = match($task['status']) {
                            'In Progress' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'Pending Review' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'Completed' => 'bg-green-100 text-green-800 border-green-200',
                            'Not Started' => 'bg-gray-100 text-gray-800 border-gray-200',
                            default => 'bg-gray-100 text-gray-800 border-gray-200',
                        };
                        
                        // Define priority color
                        $priorityColor = match($task['priority']) {
                            'High' => 'bg-red-100 text-red-800 border-red-200',
                            'Medium' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'Normal', 'Low' => 'bg-green-100 text-green-800 border-green-200',
                            default => 'bg-gray-100 text-gray-800 border-gray-200',
                        };
                        ?>
                        
                        <div class="flex flex-col items-end gap-2">
                            <span class="px-3 py-1 rounded-full text-sm font-medium border <?php echo $priorityColor; ?>">
                                Priority: <?php echo htmlspecialchars($task['priority']); ?>
                            </span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium border <?php echo $statusColor; ?>">
                                Status: <?php echo htmlspecialchars($task['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Task Content -->
                <div class="p-6">
                    <!-- Assignment Info -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border-l-4 border-bedan-red pl-4">
                            <h3 class="text-sm font-medium text-gray-500">Assigned To</h3>
                            <p class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($task['assigned_to']); ?></p>
                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($task['department']); ?> Department</p>
                        </div>
                        <div class="border-l-4 border-bedan-red pl-4">
                            <h3 class="text-sm font-medium text-gray-500">Deadline</h3>
                            <p class="text-lg font-medium text-gray-900"><?php echo date('F d, Y', strtotime($task['deadline'])); ?></p>
                            <?php
                            // Calculate days remaining/overdue
                            $deadline = new DateTime($task['deadline']);
                            $today = new DateTime();
                            $interval = $today->diff($deadline);
                            $daysRemaining = $deadline >= $today ? $interval->days : -$interval->days;
                            
                            $dateClass = $daysRemaining > 3 ? 'text-green-600' : ($daysRemaining >= 0 ? 'text-yellow-600' : 'text-red-600');
                            $dateText = $daysRemaining > 0 ? "$daysRemaining days remaining" : ($daysRemaining == 0 ? "Due today" : abs($daysRemaining) . " days overdue");
                            ?>
                            <p class="text-sm <?php echo $dateClass; ?>"><?php echo $dateText; ?></p>
                        </div>
                    </div>
                    
                    <!-- Task Description -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Task Description</h3>
                        <div class="bg-gray-50 rounded-md p-4 text-gray-700 whitespace-pre-line">
                            <?php echo nl2br(htmlspecialchars($task['task_details'])); ?>
                        </div>
                    </div>
                    
                    <!-- Task Links -->
                    <?php if (!empty($task['link'])): ?>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Relevant Links</h3>
                        <div class="bg-gray-50 rounded-md p-4 text-gray-700">
                            <?php
                            $links = explode("\n", $task['link']);
                            foreach ($links as $link) {
                                $link = trim($link);
                                if (!empty($link)) {
                                    // Make links clickable if they're URLs
                                    if (filter_var($link, FILTER_VALIDATE_URL)) {
                                        echo '<a href="' . htmlspecialchars($link) . '" target="_blank" class="text-blue-600 hover:underline block mb-1"><i class="fas fa-external-link-alt mr-2"></i>' . htmlspecialchars($link) . '</a>';
                                    } else {
                                        echo '<p class="mb-1">' . htmlspecialchars($link) . '</p>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                        <?php if ($_SESSION['position'] == 'President'): ?>
                        <a href="edittask.php?id=<?php echo $task['task_id']; ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-all">
                            <i class="fas fa-edit mr-2"></i>Edit Task
                        </a>
                        <a href="deletetask.php?id=<?php echo $task['task_id']; ?>" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-all" onclick="return confirm('Are you sure you want to delete this task?')">
                            <i class="fas fa-trash mr-2"></i>Delete Task
                        </a>
                        <?php elseif ($task['member_id'] == $_SESSION['username']): ?>
                        <form method="POST" action="update_status.php" class="inline-block">
                            <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                            <div class="flex items-center gap-2">
                                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red text-sm">
                                    <option value="Not Started" <?php echo $task['status'] == 'Not Started' ? 'selected' : ''; ?>>Not Started</option>
                                    <option value="In Progress" <?php echo $task['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="Pending Review" <?php echo $task['status'] == 'Pending Review' ? 'selected' : ''; ?>>Pending Review</option>
                                    <option value="Completed" <?php echo $task['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                </select>
                                <button type="submit" class="bg-bedan-red hover:bg-bedan-red-light text-white px-4 py-2 rounded-md text-sm font-medium transition-all">
                                    <i class="fas fa-save mr-2"></i>Update Status
                                </button>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
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