<?php
session_start();
include "db_conn.php";

// Check if user is logged in
if (!isset($_SESSION["username"]) || !isset($_SESSION["org_id"])) {
    header("Location: Login.php");
    exit();
}

// Check if form was submitted with task_id and status
if (isset($_POST['task_id']) && isset($_POST['status'])) {
    $task_id = mysqli_real_escape_string($conn, $_POST['task_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Validate status value
    $valid_statuses = ['Not Started', 'In Progress', 'Pending Review', 'Completed'];
    if (!in_array($status, $valid_statuses)) {
        $_SESSION['error'] = "Invalid status value";
        header("Location: Home2.php");
        exit();
    }
    
    // Prepare the SQL query based on user role
    if ($_SESSION['position'] == 'President') {
        // Presidents can update any task in their organization
        $check_query = "SELECT t.task_id FROM tasks t
                       JOIN users u ON t.member_id = u.username
                       WHERE t.task_id = '$task_id' AND u.org_id = '{$_SESSION['org_id']}'";
    } else {
        // Non-president users can only update their own tasks
        $check_query = "SELECT t.task_id FROM tasks t
                       WHERE t.task_id = '$task_id' AND t.member_id = '{$_SESSION['username']}'";
    }
    
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update the task status
        $update_query = "UPDATE tasks SET status = '$status' WHERE task_id = '$task_id'";
        if (mysqli_query($conn, $update_query)) {
            $_SESSION['success'] = "Task status updated successfully";
        } else {
            $_SESSION['error'] = "Error updating status: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "You don't have permission to update this task";
    }
}

// Redirect back to the dashboard
header("Location: Home2.php");
exit();
?> 