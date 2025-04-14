<?php
function createNotification($conn, $user_id, $org_id, $message, $type, $related_id = null) {
    $sql = "INSERT INTO notifications (user_id, org_id, message, type, related_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $user_id, $org_id, $message, $type, $related_id);
    return mysqli_stmt_execute($stmt);
}

function getUnreadNotifications($conn, $user_id, $org_id) {
    $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND org_id = ? AND is_read = 0";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $user_id, $org_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

function getNotifications($conn, $user_id, $org_id, $limit = 5) {
    $sql = "SELECT * FROM notifications WHERE user_id = ? AND org_id = ? ORDER BY created_at DESC LIMIT ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $user_id, $org_id, $limit);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function markNotificationAsRead($conn, $notification_id) {
    $sql = "UPDATE notifications SET is_read = 1 WHERE notification_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $notification_id);
    return mysqli_stmt_execute($stmt);
}

function markAllNotificationsAsRead($conn, $user_id, $org_id) {
    $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND org_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $user_id, $org_id);
    return mysqli_stmt_execute($stmt);
}
?> 