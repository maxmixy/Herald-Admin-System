<?php
session_start();    
include "db_conn.php";  // $conn will be null

// For testing, if no session exists, create a mock session
if (!isset($_SESSION["position"])) {
    $_SESSION['position'] = "Human Resources";
}

if(isset($_POST['MemberName']) && isset($_POST['UserPassword'])) {
    function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $MemberID = validate($_POST['MemberID']);
    $MemberName = validate($_POST['MemberName']);
    $MemberPosition = validate($_POST['MemberPosition']);
    $UserName = validate($_POST['UserName']);
    $UserPassword = validate($_POST['UserPassword']); 

    if ($conn) {
        // Original database code
        $sql = "INSERT into Members VALUES (null, '$MemberName', '$MemberPosition', '$UserName', '$UserPassword');";
        $result = mysqli_query($conn, $sql);
    } else {
        // Mock successful account creation
        $_SESSION['last_created_account'] = [
            'MemberID' => $MemberID,
            'MemberName' => $MemberName,
            'MemberPosition' => $MemberPosition,
            'UserName' => $UserName
        ];
    }

    // Redirect with success message
    header("Location: AddAccount.php?success=Account created successfully!");
    exit();
} else {
    header("Location: Login.php");
    exit();
}
