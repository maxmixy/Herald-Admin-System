<?php
session_start();
include "db_conn.php";

// Check if user is logged in
if (!isset($_SESSION["username"]) || !isset($_SESSION["org_id"])) {
    header("Location: Login.php");
    exit();
}

// Set headers for Excel file download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=tasks_report_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Build the query with the same filter logic as in OverallTasks.php
$sql = "SELECT t.task_id, t.task_title, u.name as assigned_to, u.department, 
               t.deadline, t.priority, t.status, t.task_details, t.link
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

$sql .= " ORDER BY t.deadline ASC";

// Get filtered tasks
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Start Excel file content
echo "<table border='1'>";
echo "<tr>";
echo "<th>Task ID</th>";
echo "<th>Task Title</th>";
echo "<th>Assigned To</th>";
echo "<th>Department</th>";
echo "<th>Deadline</th>";
echo "<th>Priority</th>";
echo "<th>Status</th>";
echo "<th>Task Description</th>";
echo "</tr>";

// Add task data rows
if ($result && mysqli_num_rows($result) > 0) {
    while ($task = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>TASK" . str_pad($task['task_id'], 3, '0', STR_PAD_LEFT) . "</td>";
        echo "<td>" . htmlspecialchars($task['task_title']) . "</td>";
        echo "<td>" . htmlspecialchars($task['assigned_to']) . "</td>";
        echo "<td>" . htmlspecialchars($task['department']) . "</td>";
        echo "<td>" . date('Y-m-d', strtotime($task['deadline'])) . "</td>";
        echo "<td>" . htmlspecialchars($task['priority']) . "</td>";
        echo "<td>" . htmlspecialchars($task['status']) . "</td>";
        echo "<td>" . htmlspecialchars($task['task_details']) . "</td>";
        echo "</tr>";
    }
} else {
    // No results row
    echo "<tr><td colspan='8'>No tasks found</td></tr>";
}

echo "</table>";
exit();
?> 