<?php
session_start();
include "db_conn.php";

// Check if user is logged in and is President
if (!isset($_SESSION["username"]) || $_SESSION["position"] !== "President") {
    header("Location: Login.php");
    exit();
}

// Get announcement ID from URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Delete announcement
    $query = "DELETE FROM announcements WHERE announcement_id = ? AND org_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "is", $id, $_SESSION['org_id']);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Announcement deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting announcement: " . mysqli_error($conn);
    }
}

// Redirect back to Home2.php
header("Location: Home2.php");
exit(); 