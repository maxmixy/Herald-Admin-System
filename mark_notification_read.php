<?php
session_start();
include "db_conn.php";
include_once "notifications.php";

if (isset($_POST['notification_id'])) {
    $notification_id = $_POST['notification_id'];
    
    // Get notification details before marking as read
    $query = "SELECT type, related_id FROM notifications WHERE notification_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $notification_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $notification = mysqli_fetch_assoc($result);
    
    // Mark as read
    markNotificationAsRead($conn, $notification_id);
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'notification' => $notification
    ]);
    exit;
}

// If we get here, something went wrong
header('Content-Type: application/json');
echo json_encode(['success' => false]);
?> 