<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="AdminStyle.css">
</head>

<body style="background-image: url('EmpRegBG.jpeg');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;"> 
        <div class="loginContainer">
            <form class="forms" action="LogProc.php" method="post">
                <br><br><br><br><br><br><br><br>
                <img src="LoginText.png" alt="Logo" style="width: 100%; height: 100%;">
                <input type="text" id="username" name="username" placeholder="Enter Username" required> 
                <input type="password" id="password" name="password" placeholder="Enter Password" required> <br>
                <button type="submit">              Login              </button>
                <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
            </form>

            <a href="AddAccount.php"> Sign up </a><br><br><br><br><br>
        </div>
    
</body>
</html> 