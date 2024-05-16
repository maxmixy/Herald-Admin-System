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
            <h1>Task Assignments: </h1>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <select name="category">
                    <option value="News">News</option>
                    <option value="Features">Features</option>
                    <option value="Sports">Sports</option>
                    <option value="Opinion">Opinion</option>
                    <option value="Creatives">Creatives</option>
                </select>
                <input type="submit" value="Submit">
                </form>

                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $selectedCategory = $_POST["category"]; 
                }
                ?>
            <?php
                $sql = "SELECT A.ArticleID, M.Name, A.ArticleTopic, A.Notes, A.Deadline, A.ArticlePreStatus FROM ArticleAssignment A INNER JOIN Members M ON A.WriterID = M.MemberID WHERE A.Section='$selectedCategory'";
                $result = mysqli_query($conn, $sql);
            
                if ($result) {
                    echo '<table>';
                    echo '<tr><th>Article ID</th><th>Writer</th><th>Article Topic</th><th>Notes</th><th>Deadline</th><th>Status</th></tr>';
                    while ($row = mysqli_fetch_assoc($result)) {
                        $artID = $row["ArticleID"];
                        $name = $row["Name"];
                        $topic = $row["ArticleTopic"];
                        $notes = $row["Notes"];
                        $deadline = $row["Deadline"];
                        $status = $row["ArticlePreStatus"]; 
                        
                        echo "<tr><td>$artID</td><td>$name</td><td>$topic</td><td>$notes</td><td>$deadline</td><td>$status</td></tr>";
                    }
                    echo '</table>';
                } else {
                    echo "No assignments";
                    echo "Error: Unable to execute query. " . mysqli_error($conn);
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