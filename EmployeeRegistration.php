<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="inside.css">
</head>
<body> 
    <div>
    <div class="loginContainer">
        <form class="forms" action="AddEmpProc.php" method="post">
            <br><br>
            <img src="EmloyeeRegText.png" alt="Logo" style="width: 100%; height: 100%;">
            <input type="text" id="username" name="username" placeholder="Enter Username" required> 
            <input type="password" id="password" name="password" placeholder="Enter Password" required>
            <input type="text" id="name" name="name" placeholder="Enter Name" required> 
            <input type="text" id="role" name="role" placeholder="Enter Role" required> 
            <input type="date" id="birthday" name="birthday" placeholder="Enter birthday" required> 
            <input type="text" id="address" name="address" placeholder="Enter address" required> 
            <input type="file" id="image" name="image" placeholder="Enter image"> 
            <br>
            <button type="submit"> Register </button>
            <br><br><br>
        </form>

    </div>
    <div>
    
</body>
</html> 