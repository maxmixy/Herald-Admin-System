<?php
session_start();    
include "db_conn.php";

if(isset($_POST['username']) && isset($_POST['password']) && $_SESSION['position'] == 'Human Resources'){
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
    $MemUserPasswordberID = validate($_POST['UserPassword']); 

    $sql = "INSERT into Members VALUES (null, '$MemberName', '$MemberPosition', '$UserName', '$MemUserPasswordberID');";
    $result = mysqli_query($conn, $sql);
}
else {
    header("Location: Login.php");
    exit();
}
$conn->close();
