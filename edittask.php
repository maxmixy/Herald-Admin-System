<?php
session_start();
include "db_conn.php";
include_once "notifications.php";

// Check if user is logged in and has President role
if (!isset($_SESSION["username"]) || !isset($_SESSION["org_id"]) || $_SESSION["position"] !== "President") {
    header("Location: Login.php");
    exit();
}

// Check if task ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: Home2.php");
    exit();
}

$task_id = mysqli_real_escape_string($conn, $_GET['id']);
$error = "";
$success = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_title = trim($_POST["task_title"]);
    $member_id = trim($_POST["member_id"]);
    $deadline = trim($_POST["deadline"]);
    $priority = trim($_POST["priority"]);
    $status = trim($_POST["status"]);
    $task_details = trim($_POST["task_details"]);
    $link = trim($_POST["link"]);
    
    if (empty($task_title) || empty($member_id) || empty($deadline) || empty($priority) || empty($status)) {
        $error = "Required fields cannot be empty";
    } else {
        // Get the original task details before updating
        $originalSql = "SELECT * FROM tasks WHERE task_id = ?";
        $originalStmt = $conn->prepare($originalSql);
        $originalStmt->bind_param("i", $task_id);
        $originalStmt->execute();
        $originalResult = $originalStmt->get_result();
        $originalTask = $originalResult->fetch_assoc();
        
        // Update the task
        $sql = "UPDATE tasks SET 
                task_title = ?, 
                member_id = ?, 
                deadline = ?, 
                priority = ?, 
                status = ?, 
                task_details = ?, 
                link = ? 
                WHERE task_id = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $task_title, $member_id, $deadline, $priority, $status, $task_details, $link, $task_id);
        
        if ($stmt->execute()) {
            $success = "Task updated successfully!";
            
            // If member_id changed, notify the new assignee
            if ($originalTask['member_id'] != $member_id) {
                $message = "You have been assigned to task: " . $task_title;
                createNotification($conn, $member_id, $_SESSION['org_id'], $message, 'task_assignment', $task_id);
            }
            
            // If status changed, notify the task member
            if ($originalTask['status'] != $status) {
                $message = "Task status updated: " . $task_title . " is now " . $status;
                createNotification($conn, $originalTask['member_id'], $_SESSION['org_id'], $message, 'task_update', $task_id);
            }
            
            // Redirect back to the task view page
            header("Location: viewtask.php?id=$task_id&success=1");
            exit();
        } else {
            $error = "Error updating task: " . $conn->error;
        }
    }
}

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
    $_SESSION['error'] = "Task not found or you don't have permission to edit it";
    header("Location: Home2.php");
    exit();
}

$task = $result->fetch_assoc();

// Get all users for dropdown
$usersSql = "SELECT username, name, department FROM users WHERE org_id = ?";
$usersStmt = $conn->prepare($usersSql);
$usersStmt->bind_param("s", $_SESSION['org_id']);
$usersStmt->execute();
$usersResult = $usersStmt->get_result();
$users = [];
while ($user = $usersResult->fetch_assoc()) {
    $users[] = $user;
}
?>

<!DOCTYPE html>
<html>
    <head>  
        <title>Edit Task - The Bedan Herald</title>
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
                <a href="viewtask.php?id=<?php echo $task_id; ?>" class="inline-flex items-center text-bedan-red hover:text-bedan-red-light">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Task
                </a>
            </div>
            
            <!-- Task Edit Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Task</h1>
                
                <?php if ($error): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                        <p><?php echo $success; ?></p>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Task Title -->
                        <div>
                            <label for="task_title" class="block text-sm font-medium text-gray-700">Task Title</label>
                            <input type="text" name="task_title" id="task_title" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"
                                value="<?php echo htmlspecialchars($task['task_title']); ?>">
                        </div>

                        <!-- Assign To -->
                        <div>
                            <label for="member_id" class="block text-sm font-medium text-gray-700">Assign To</label>
                            <select name="member_id" id="member_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo htmlspecialchars($user['username']); ?>" 
                                            <?php echo ($user['username'] == $task['member_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['name']) . ' (' . htmlspecialchars($user['department']) . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Deadline -->
                        <div>
                            <label for="deadline" class="block text-sm font-medium text-gray-700">Deadline</label>
                            <input type="date" name="deadline" id="deadline" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"
                                value="<?php echo date('Y-m-d', strtotime($task['deadline'])); ?>">
                        </div>

                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                            <select name="priority" id="priority" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <option value="Low" <?php echo ($task['priority'] == 'Low') ? 'selected' : ''; ?>>Low</option>
                                <option value="Normal" <?php echo ($task['priority'] == 'Normal') ? 'selected' : ''; ?>>Normal</option>
                                <option value="Medium" <?php echo ($task['priority'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                                <option value="High" <?php echo ($task['priority'] == 'High') ? 'selected' : ''; ?>>High</option>
                            </select>
                        </div>
                        
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <option value="Not Started" <?php echo ($task['status'] == 'Not Started') ? 'selected' : ''; ?>>Not Started</option>
                                <option value="In Progress" <?php echo ($task['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                <option value="Pending Review" <?php echo ($task['status'] == 'Pending Review') ? 'selected' : ''; ?>>Pending Review</option>
                                <option value="Completed" <?php echo ($task['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                    </div>

                    <!-- Task Description -->
                    <div>
                        <label for="task_details" class="block text-sm font-medium text-gray-700">Task Description</label>
                        <textarea name="task_details" id="task_details" rows="4" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"><?php echo htmlspecialchars($task['task_details']); ?></textarea>
                    </div>

                    <!-- Task Links -->
                    <div>
                        <label for="link" class="block text-sm font-medium text-gray-700">Task Links</label>
                        <textarea name="link" id="link" rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"><?php echo htmlspecialchars($task['link']); ?></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3">
                        <a href="viewtask.php?id=<?php echo $task_id; ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition-all">
                            Cancel
                        </a>
                        <button type="submit" class="bg-bedan-red hover:bg-bedan-red-light text-white px-4 py-2 rounded-md text-sm font-medium transition-all">
                            <i class="fas fa-save mr-2"></i>Save Changes
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