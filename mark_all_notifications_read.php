<?php
session_start();
include "db_conn.php";
include_once "notifications.php";

markAllNotificationsAsRead($conn, $_SESSION['username'], $_SESSION['org_id']);
?> 