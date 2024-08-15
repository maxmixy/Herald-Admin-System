
<?php
session_start();    
include "db_conn.php";

if(1){
    function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $username = validate($_POST['username']);
    $password = validate($_POST['password']);
    $name = validate($_POST['name']);
    $role = validate($_POST['role']);

    $sql = "INSERT INTO `users`(`username`, `password`, `name`, `role`) VALUES ('$username','$password','$name','$role')";
    $result = mysqli_query($conn, $sql);

    header("Location: Login.php");
    exit();
}
else {
    header("Location: Login.php");
    exit();
}
$conn->close();
