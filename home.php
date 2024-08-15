<?php
session_start();
include "db_conn.php";include "db_conn.php";

if (1) { 
    ?>
<!DOCTYPE html>
<html>
<head>  
    <title>HOME</title>
    <link rel="stylesheet" href="inside.css">
</head>

<body>
    
        <div class="tab">
            <a href='EmployeeRegistration.php' target='Main'>Register Employees</a>
        </div>
        <br>

        <div class='Main'>
            <h1>Good day <?php echo $_SESSION['name'] ?></h1>
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