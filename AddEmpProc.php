
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
    $birthday = validate($_POST['birthday']);
    $address = validate($_POST['address']);
    $image = validate($_POST['image']);

    $sql = "INSERT INTO `employees`(`Username`, `Password`, `Name`, `Role`, `Birthday`, `Address`, `EmployeeNumber`, `Image`) VALUES ('$username','$password','$name','$role', '$birthday','$address', null ,'$image')";
    $result = mysqli_query($conn, $sql);

    header("Location: Home.php");
    exit();
}
else {
    header("Location: Login.php");
    exit();
}
$conn->close();
