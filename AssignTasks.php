<?php
session_start();
include "db_conn.php";

if (isset($_SESSION["MemberID"]) && isset($_SESSION["name"])) { 
    ?>

    <!DOCTYPE html>
    <html>
        <head>  
            <title>Assigning Tasks</title>
            <link rel="stylesheet" href="AdminStyle.css">
        </head>

        

        <body>
        <br><br>
            <div class="header">
                <a href="Home2.php"><img src="The Bedan Herald.png" style="width: 30%; height: auto;"> </a>
                <?php 
                if ($_SESSION["position"] == 'Section Editor' || $_SESSION["position"] == 'Head Admin' || $_SESSION["position"] == 'Admin'){
                ?>
                <a class="active" href="Home2.php" style="text-decoration: underline;">Assignments</a>
                <a href="AssignTasks.php" style="text-decoration: underline;">Assign Tasks</a>
                <a href="OverallTasks.php" style="text-decoration: underline;">Progress Overview</a>
                <?php
                }if ($_SESSION["position"] == 'Head Admin' || $_SESSION["position"] == 'Human Resources'){
                ?>
                <a href="AddAccount.php" style="text-decoration: underline;">Create Accounts</a>
                <?php
                }
                ?>
                <a href="LogOut.php" style="text-decoration: underline;">Logout</a> 
                <br><br>
            </div> 
            <h1>Assign tasks here: </h1>
            <?php
                $sql = "SELECT * FROM ArticleAssignment WHERE Section='Features'";
                $result = mysqli_query($conn, $sql);
            
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $artID = $row["ArticleID"];
                        $name = $row["WriterName"];
                        $topic = $row["ArticleTopic"];
                        $notes = $row["Notes"];
                        $deadline = $row["Deadline"];
                        $status = $row["ArticlePreStatus"];
                        
                        echo $artID . "&emsp;" . $name . "&emsp;" . $topic . "&emsp;" . $notes . "&emsp;" . $deadline . "&emsp;" . $status . "<br>";
                    }
                } else {
                    echo "No assignments";
                }
                $conn->close();
            ?>
        </body>
    </html>
    
    <?php
}
else {  
    header("Location: Login.php");
    exit();
}
?>