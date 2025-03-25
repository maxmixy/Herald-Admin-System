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

    if ($conn) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $pass);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            // Store user data in session
            $_SESSION['username'] = $row['username'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['org_id'] = $row['org_id'];
            $_SESSION['position'] = $row['position'];
            $_SESSION['department'] = $row['department'];
            
            header("Location: Home2.php");
            exit();
        } else {
            header("Location: Login.php?error=Incorrect Username or Password");
            exit();
        }
    } else {
        header("Location: Login.php?error=Database connection failed");
        exit();
    }
}
