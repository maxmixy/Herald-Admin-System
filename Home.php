<?php
session_start();
if (isset($_SESSION["MemberID"]) && isset($_SESSION["name"])) {
    ?>

    <!DOCTYPE html>
    <html>
    <head>  
        <title>HOME</title>
    </head>

    <body>
        <h1>Welcome, <?php echo $_SESSION['name'] ?></h1>
        <a href="LogOut.php">Logout</a>
    </body>
    </html>
    
    <?php
}
else {  
    header("Location: Login.php");
    exit();
}
?>