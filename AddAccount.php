<?php
session_start();
include "db_conn.php";

if (isset($_SESSION["MemberID"]) && isset($_SESSION["name"])) { 
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Adding User Accounts</title>
        <link rel="stylesheet" href="AdminStyle.css">
    </head>
    <body> 
        <div class="header">
            <a href="Home.php"><img src="The Bedan Herald.png" style="width: 30%; height: auto;"> </a>
            <a href="Home.php">Assignments</a>
            <a class="active"href="FeaturesTasks.php">Assign Tasks</a>
            <a href="#about">Prorgess Overview</a>
            <a href="LogOut.php">Logout</a>
        </div>
        <br><br><br>
        <div class="loginMain">
            <div class="loginLogo">
                <img src="TBHLogo.png" alt="Logo" style="width: 70%; height: 70%;">
            </div>
            <div class="loginContainer">
                <form action="AddAccProc.php" method="post">
                    <br><br><br><br><br><br><br><br>
                    <img src="The Bedan Herald.png" style="width: 100%; height: auto;"><br><br><br><br><br>
                    <input type="text" id="MemberID" name="MemberID" placeholder="Enter MemberID" required> <br><br>
                    <input type="text" id="MemberName" name="MemberName" placeholder="Enter Member Name" required> <br><br>
                    <input type="text" id="MemberPosition" name="MemberPosition" placeholder="Enter Member Position" required> <br><br>
                    <input type="text" id="UserName" name="UserName" placeholder="Enter UserName" required> <br><br>
                    <input type="password" id="UserPassword" name="UserPassword" placeholder="Enter User Password" required> <br><br>
                    <button type="submit">     Create Account     </button>
                    <br><br><br><br><br><br><br><br>
                </form>
            </div>
        </div> 
    </body>
    </html> 


    <?php
}
else {  
    header("Location: Login.php");
    exit();
}
?>
