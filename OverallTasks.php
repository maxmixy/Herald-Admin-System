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
                <a href="Home2.php">Assignments</a>
                <a class="active"href="FeaturesTasks.php">Assign Tasks</a> 
                <a href="LogOut.php">Logout</a>
            </div>
            <br><br>
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
                        $artID = $row["A.ArticleID"];
                        $name = $row["M.Name"];
                        $topic = $row["A.ArticleTopic"];
                        $notes = $row["A.Notes"];
                        $deadline = $row["A.Deadline"];
                        $status = $row["A.ArticlePreStatus"]; 
                        
                        echo "<tr><td>$artID</td><td>$name</td><td>$topic</td><td>$notes</td><td>$deadline</td><td>$status</td></tr>";
                    }
                    echo '</table>';
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