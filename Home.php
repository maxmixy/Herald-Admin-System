<?php
session_start();
include "db_conn.php";  // $conn will be null

// For testing, if no session exists, create a mock session
if (!isset($_SESSION["MemberID"])) {
    $_SESSION['username'] = "admin";
    $_SESSION['name'] = "Admin User";
    $_SESSION['MemberID'] = "001";
    $_SESSION['position'] = "Head Admin";
}

if (isset($_SESSION["MemberID"]) && isset($_SESSION["name"])) { 
    ?>

    <!DOCTYPE html>
    <html>
        <head>  
            <title>HOME</title>
            <link rel="stylesheet" href="AdminStyle.css">
        </head>

        

        <body>
            <div class="header">
                <a href="Home.php"><img src="The Bedan Herald.png" style="width: 30%; height: auto;"> </a>
                <?php 
                if ($_SESSION["position"] == 'Section Editor' || $_SESSION["position"] == 'Head Admin' || $_SESSION["position"] == 'Admin'){
                ?>
                <a class="active" href="Home.php" style="text-decoration: underline;">Assignments</a>
                <a href="AssignTasks.php" style="text-decoration: underline;">Assign Tasks</a>
                <a href="OverallTasks.php" style="text-decoration: underline;">Progress Overview</a>
                <?php
                }
                if ($_SESSION["position"] == 'Head Admin' || $_SESSION["position"] == 'Human Resources'){
                ?>
                <a href="AddAccount.php" style="text-decoration: underline;">Create Accounts</a>
                <?php
                }
                ?>
                <a href="LogOut.php" style="text-decoration: underline;">Logout</a> 
                <br><br>
            </div>
            
            <div class="content">
                <h1>Welcome, <?php echo $_SESSION['name']; ?>!</h1>
                
                <?php if ($conn) {
                    // Original database code here
                    $sql = "SELECT * FROM ArticleAssignment WHERE WriterID='{$_SESSION['MemberID']}'";
                    $result = mysqli_query($conn, $sql);
                    
                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                        $section = $row["Section"];
                        $topic = $row["ArticleTopic"];  
                        $notes = $row["Notes"];  
                        $deadline = $row["Deadline"];  
                        $status = $row["ArticlePreStatus"];  
                        
                        echo "<div class='article-item'>";
                        echo "<h3>{$section} - {$topic}</h3>";
                        echo "<p>Notes: {$notes}</p>";
                        echo "<p>Deadline: {$deadline}</p>";
                        echo "<p>Status: {$status}</p>";
                        echo "</div>";
                        }
                    } else {
                        echo "No assignments";
                    }
                    $conn->close();
                } else {
                    // Mock data when no database connection
                    $mockArticles = [
                        ['title' => 'Sample Article 1', 'deadline' => '2024-04-01', 'status' => 'In Progress'],
                        ['title' => 'Sample Article 2', 'deadline' => '2024-04-15', 'status' => 'Pending']
                    ];
                    
                    echo "<h2>Current Assignments</h2>";
                    echo "<div class='article-list'>";
                    foreach ($mockArticles as $article) {
                        echo "<div class='article-item'>";
                        echo "<h3>{$article['title']}</h3>";
                        echo "<p>Deadline: {$article['deadline']}</p>";
                        echo "<p>Status: {$article['status']}</p>";
                        echo "</div>";
                    }
                    echo "</div>";
                }
                ?>
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