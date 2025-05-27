<?php
ob_start();
session_start();
include "db_conn.php";
include_once "notifications.php";

if (isset($_SESSION["username"]) && isset($_SESSION["org_id"])) { 
    // Process form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $task_title = isset($_POST["topic"]) ? trim($_POST["topic"]) : '';
        $member_id = isset($_POST["writer"]) ? trim($_POST["writer"]) : '';
        $deadline = isset($_POST["deadline"]) ? trim($_POST["deadline"]) : '';
        $priority = isset($_POST["priority"]) ? trim($_POST["priority"]) : '';
        $status = isset($_POST["status"]) ? trim($_POST["status"]) : 'Not Started';
        $task_details = isset($_POST["notes"]) ? trim($_POST["notes"]) : '';
        $link = isset($_POST["links"]) ? trim($_POST["links"]) : '';
        
        if (empty($task_title) || empty($member_id) || empty($deadline) || empty($priority)) {
            $error = "All fields are required";
        } else {
            $sql = "INSERT INTO tasks (task_title, member_id, deadline, priority, status, task_details, link) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssssss", $task_title, $member_id, $deadline, $priority, $status, $task_details, $link);
            
            if (mysqli_stmt_execute($stmt)) {
                // Create notification for the assigned user
                $message = "New task assigned: " . $task_title;
                createNotification($conn, $member_id, $_SESSION['org_id'], $message, 'task', mysqli_insert_id($conn));
                
                $_SESSION['success'] = "Task assigned successfully!";
                header("Location: Home2.php");
                exit();
            } else {
                $error = "Error assigning task: " . mysqli_error($conn);
            }
        }
    }
    
    // Fetch users for dropdown (only from the same organization)
    $users = [];
    $sql = "SELECT username, name, department FROM users WHERE org_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['org_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();
?>
<!DOCTYPE html>
<html>
    <head>  
        <title>Assign Tasks</title>
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
            
            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = `fixed top-20 right-4 px-6 py-3 rounded-md shadow-lg text-white ${
                    type === 'success' ? 'bg-green-500' : 'bg-red-500'
                }`;
                notification.textContent = message;
                document.body.appendChild(notification);
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            // Automatically set department when user is selected
            document.addEventListener('DOMContentLoaded', function() {
                const userSelect = document.getElementById('writer');
                const departmentSelect = document.getElementById('section');
                const users = <?php echo json_encode($users); ?>;
                
                userSelect.addEventListener('change', function() {
                    const selectedUser = this.value;
                    const user = users.find(u => u.username === selectedUser);
                    if (user) {
                        departmentSelect.value = user.department;
                        // Make department field read-only after auto-setting
                        departmentSelect.readOnly = true;
                    }
                });
            });
        </script>
    </head>

    <body class="bg-gray-50 min-h-screen">
        <!-- Header Navigation -->
        <nav class="bg-gradient-to-r from-bedan-red to-bedan-red-light fixed w-full top-0 z-50 shadow-lg">
            <div class="max-w-5x7 mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16"> 
                    <?php include "tabs.php"; ?>
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
                <form action="AssignTasks.php" method="post" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Task Title -->
                        <div>
                            <label for="topic" class="block text-sm font-medium text-gray-700">Task Title</label>
                            <input type="text" name="topic" id="topic" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"
                                placeholder="Enter task title">
                        </div>

                        <!-- Department (auto-filled based on user selection) -->
                        <div>
                            <label for="section" class="block text-sm font-medium text-gray-700">Department</label>
                            <select name="section" id="section" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm bg-gray-100"
                                readonly>
                                <option value="">Select team member first</option>
                                <?php
                                // Include default department options
                                $defaultDepartments = ["Creatives", "Finance", "Logistics"];
                                
                                // Get unique departments from users
                                $userDepartments = array_unique(array_column($users, 'department'));
                                $allDepartments = array_unique(array_merge($defaultDepartments, $userDepartments));
                                sort($allDepartments);
                                
                                foreach ($allDepartments as $dept) {
                                    echo "<option value=\"$dept\">$dept</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Assign To -->
                        <div>
                            <label for="writer" class="block text-sm font-medium text-gray-700">Assign To</label>
                            <select name="writer" id="writer" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <option value="">Select team member</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= htmlspecialchars($user['username']) ?>" data-department="<?= htmlspecialchars($user['department']) ?>">
                                        <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['department']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Deadline -->
                        <div>
                            <label for="deadline" class="block text-sm font-medium text-gray-700">Deadline</label>
                            <input type="date" name="deadline" id="deadline" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"
                                min="<?= date('Y-m-d') ?>">
                        </div>

                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                            <select name="priority" id="priority" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <option value="Low">Low</option>
                                <option value="Normal" selected>Normal</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Initial Status</label>
                            <select name="status" id="status" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <option value="Not Started" selected>Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Pending Review">Pending Review</option>
                            </select>
                        </div>
                    </div>

                    <!-- Task Details -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Task Description</label>
                        <textarea name="notes" id="notes" rows="4" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"
                            placeholder="Enter task details"></textarea>
                    </div>

                    <!-- Task Links -->
                    <div>
                        <label for="links" class="block text-sm font-medium text-gray-700">Task Links</label>
                        <textarea name="links" id="links" rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"
                            placeholder="Include relevant links"></textarea>
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