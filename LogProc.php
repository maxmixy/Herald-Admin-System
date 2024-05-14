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

	$sql = "SELECT * FROM Members WHERE UserID='$username' AND Password='$pass'";
	$result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) === 1){
        $row = mysqli_fetch_array($result);
        if($row['UserID'] === $username && $row['Password'] === $pass){
            echo "Logged in!";
            $_SESSION['username'] = $row['UserID'];
            $_SESSION['name'] = $row['Name'];
            $_SESSION['MemberID'] = $row['MemberID'];
            $_SESSION['position'] = $row['Position'];
            header("Location: Home.php");
            exit();
        }
        else {
            header("Location: Login.php?error=Incorrect Username or ID");
        }
	}
	else {
        header("Location: Login.php");
        exit();
	}
	$conn->close();
