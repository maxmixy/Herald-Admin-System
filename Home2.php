<?php
session_start();
include "db_conn.php";

if (1) { 
    ?>

    <!DOCTYPE html>
    <html>
        <head>  
            <title>HOME</title>
            <link rel="stylesheet" href="AdminStyle.css">
        </head>

        <body>
                
                <br><br>
                <h1>Good day <?php echo $_SESSION['name'] ?></h1>
                
        </body>
    </html>
    
    <?php
}
else {  
    header("Location: Login.php");
    exit();
}
?>