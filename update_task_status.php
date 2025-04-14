<?php
session_start();
include "db_conn.php";
include_once "notifications.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_id']) && isset($_POST['status'])) {
    $task_id = $_POST['task_id'];
    $new_status = $_POST['status'];
    
    // Get task details
    $taskQuery = "SELECT t.*, u.position 
                  FROM tasks t 
                  JOIN users u ON t.member_id = u.username 
                  WHERE t.task_id = ? AND t.org_id = ?";
    $taskStmt = mysqli_prepare($conn, $taskQuery);
    mysqli_stmt_bind_param($taskStmt, "is", $task_id, $_SESSION['org_id']);
    mysqli_stmt_execute($taskStmt);
    $taskResult = mysqli_stmt_get_result($taskStmt);
    
    if ($taskResult && mysqli_num_rows($taskResult) > 0) {
        $task = mysqli_fetch_assoc($taskResult);
        
        // Update task status
        $updateQuery = "UPDATE tasks SET status = ? WHERE task_id = ? AND org_id = ?";
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "sis", $new_status, $task_id, $_SESSION['org_id']);
        
        if (mysqli_stmt_execute($updateStmt)) {
            // If the user is not President and status is changed, notify the President
            if ($_SESSION['position'] !== 'President') {
                // Get President's username
                $presidentQuery = "SELECT username FROM users WHERE org_id = ? AND position = 'President'";
                $presidentStmt = mysqli_prepare($conn, $presidentQuery);
                mysqli_stmt_bind_param($presidentStmt, "s", $_SESSION['org_id']);
                mysqli_stmt_execute($presidentStmt);
                $presidentResult = mysqli_stmt_get_result($presidentStmt);
                
                if ($presidentResult && mysqli_num_rows($presidentResult) > 0) {
                    $president = mysqli_fetch_assoc($presidentResult);
                    $message = "Task status updated: " . $task['task_title'] . " is now " . $new_status;
                    createNotification($conn, $president['username'], $_SESSION['org_id'], $message, 'task_update', $task_id);
                }
            }
            
            $_SESSION['success'] = "Task status updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating task status: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Task not found";
    }
}

header("Location: Home2.php");
exit();
?> 