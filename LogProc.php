<?php
session_start();    
include "db_conn.php";

    if(isset($_POST['username']) && isset($_POST['password'])){
        function validate($data){
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
    }

	$username = validate($_POST['username']);
	$pass = validate($_POST['password']);

    if(empty($username)){
        header("Location: Login.php?error=Username Required");
        exit();
    }
    else if(empty($pass)){
        header("Location: Login.php?error=Password Required");
        exit();
    }

	$sql = "SELECT * FROM users WHERE username='$username'";
	$result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_array($result)){
            if($row['username'] === $username && $row['password'] === $pass){
                echo "Logged in!";
                $_SESSION['name'] = $row['name'];
                header("Location: home.php");
                exit();
            }
            else {
                header("Location: Login.php?error=Incorrect Username or ID");
            }
        } 
	}
	else {
        header("Location: Login.php");
        exit();
	}
	$conn->close();