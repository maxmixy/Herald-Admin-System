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

// Fetch the task to check if it exists and get member_id for notification
$fetchSql = "SELECT t.task_id, t.task_title, t.member_id 
             FROM tasks t 
             JOIN users u ON t.member_id = u.username
             WHERE t.task_id = ? AND u.org_id = ?";
$fetchStmt = $conn->prepare($fetchSql);
$fetchStmt->bind_param("is", $task_id, $_SESSION['org_id']);
$fetchStmt->execute();
$result = $fetchStmt->get_result();

// Check if task exists and belongs to user's organization
if ($result->num_rows === 0) {
    $_SESSION['error'] = "Task not found or you don't have permission to delete it";
    header("Location: Home2.php");
    exit();
}

$task = $result->fetch_assoc();

// Delete the task
$deleteSql = "DELETE FROM tasks WHERE task_id = ?";
$deleteStmt = $conn->prepare($deleteSql);
$deleteStmt->bind_param("i", $task_id);

if ($deleteStmt->execute()) {
    // Notify the task owner that their task has been deleted
    if ($task['member_id'] != $_SESSION['username']) {
        $message = "Task has been deleted: " . $task['task_title'];
        createNotification($conn, $task['member_id'], $_SESSION['org_id'], $message, 'task_deleted', null);
    }
    
    $_SESSION['success'] = "Task deleted successfully";
} else {
    $_SESSION['error'] = "Error deleting task: " . $conn->error;
}

// Redirect back to the dashboard
header("Location: Home2.php");
exit();
?> 