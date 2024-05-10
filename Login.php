
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="AdminStyle.css">
</head>
<body>
  <div class="bigBox">
        
        <div class="loginMain">
            <div class="loginLogo">
                <img src="TBHLogo.png" alt="Logo" style="width: 90%; height: 90%;">
            </div>
            <div class="loginContainer">
                <form action="LogProc.php" method="post">
                    <img src="The Bedan Herald.png" style="width: 100%; height: auto;">
                    <input type="text" id="username" name="username" placeholder="Enter Username" required> <br>
                    <input type="password" id="password" name="password" placeholder="Enter Password" required> <br>
                    <button type="submit">     Login     </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 

