<?php
session_start();
include "db_conn.php";

// Check if user is logged in
if (!isset($_SESSION["username"]) || !isset($_SESSION["org_id"])) {
    echo '<div class="text-center py-6 text-red-600">Session expired. Please log in again.</div>';
    exit;
}

// Start building the table HTML
echo '<table class="min-w-full">
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
        <tbody class="bg-white divide-y divide-gray-200">';

// Query the tasks table with organization filter
$sql = "SELECT t.*, u.name as assigned_to, u.department 
       FROM tasks t
       JOIN users u ON t.member_id = u.username
       WHERE u.org_id = '{$_SESSION['org_id']}'";

// Add "My Tasks" filter if selected
if (isset($_GET['view']) && $_GET['view'] == 'my') {
    $sql .= " AND t.member_id = '{$_SESSION['username']}'";
}

$sql .= " ORDER BY t.deadline ASC";
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
        echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>";
        
        // For President - show full edit capabilities for all tasks
        if ($_SESSION['position'] == 'President') {
            echo "<a href='edittask.php?id={$task['task_id']}' class='text-blue-600 hover:text-blue-900 mr-2'><i class='fas fa-edit'></i></a>";
        } 
        // For non-President users - show status dropdown only for their own tasks
        else if ($task['member_id'] == $_SESSION['username']) {
            echo "<form method='POST' action='update_status.php' class='inline-block mr-2'>
                <input type='hidden' name='task_id' value='{$task['task_id']}'>
                <select name='status' onchange='this.form.submit()' class='text-sm rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red'>
                    <option value='Not Started' ".($task['status'] == 'Not Started' ? 'selected' : '').">Not Started</option>
                    <option value='In Progress' ".($task['status'] == 'In Progress' ? 'selected' : '').">In Progress</option>
                    <option value='Pending Review' ".($task['status'] == 'Pending Review' ? 'selected' : '').">Pending Review</option>
                    <option value='Completed' ".($task['status'] == 'Completed' ? 'selected' : '').">Completed</option>
                </select>
            </form>";
        }
        
        // View button for all users
        echo "<a href='viewtask.php?id={$task['task_id']}' class='text-green-600 hover:text-green-900 ml-2'><i class='fas fa-eye'></i></a>";
        
        // Only show delete button for users with President position
        if ($_SESSION['position'] == 'President') {
            echo "<a href='deletetask.php?id={$task['task_id']}' class='text-red-600 hover:text-red-900 ml-2' onclick=\"return confirm('Are you sure you want to delete this task?')\"><i class='fas fa-trash'></i></a>";
        }
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7' class='px-6 py-4 text-center text-gray-500'>No tasks found</td></tr>";
}

echo '</tbody></table>';
?> 