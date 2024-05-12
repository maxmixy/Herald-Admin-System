
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="AdminStyle.css">
</head>
<body> 
    <br><br><br>
    <div class="loginMain">
        <div class="loginLogo">
            <img src="TBHLogo.png" alt="Logo" style="width: 80%; height: 80%; filter= drop-shadow(0, 0, rgba(0, 0, 7px, 0.4));">
        </div>
        <div class="loginContainer">
            <form action="LogProc.php" method="post">
                <br><br><br><br><br><br><br><br>
                <img src="The Bedan Herald.png" style="width: 100%; height: auto;"><br><br><br><br><br>
                <input type="text" id="username" name="username" placeholder="Enter Username" required> <br><br>
                <input type="password" id="password" name="password" placeholder="Enter Password" required> <br><br>
                <button type="submit">     Login     </button>
                <br><br><br><br><br><br><br><br>
            </form>
        </div>
    </div> 
</body>
</html> 

