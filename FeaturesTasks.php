<?php
session_start();
include "db_conn.php";

if (isset($_SESSION["MemberID"]) && isset($_SESSION["name"])) { 
    ?>

    <!DOCTYPE html>
    <html>
        <head>  
            <title>HOME</title>
            <link rel="stylesheet" href="AdminStyle.css">
        </head>

        

        <body>
        <br><br>
            <div class="header">
                <a href="Home.php"><img src="The Bedan Herald.png" style="width: 30%; height: auto;"> </a>
                <?php 
                if ($_SESSION["position"] == 'Section Editor' || $_SESSION["position"] == 'Head Admin' || $_SESSION["position"] == 'Admin'){
                ?>
                <a class="active" href="Home2.php">Assignments</a>
                <a href="FeaturesTasks.php">Assign Tasks</a> 
                <a href="OverallTasks.php">Task Overview</a>
                <?php
                }
                ?>
                <a href="LogOut.php">Logout</a> 
            </div>
            <br><br>
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