<?php
session_start();    
include "db_conn.php";

// Check if user is logged in and has permission (President or above)
if (!isset($_SESSION['username']) || !isset($_SESSION['org_id']) || $_SESSION['position'] != 'President') {
    header("Location: Login.php");
    exit();
}

if(isset($_POST['MemberName']) && isset($_POST['UserPassword'])) {
    function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $name = validate($_POST['MemberName']);
    $position = validate($_POST['MemberPosition']); 
    $department = validate($_POST['Department']);
    $username = validate($_POST['UserName']);
    $password = validate($_POST['UserPassword']);
    
    // Handle the associate position - with more space now available
    if ($position === 'Associate' && isset($_POST['AssociateTo']) && !empty($_POST['AssociateTo'])) {
        $associateTo = validate($_POST['AssociateTo']);
        $position = "Associate - " . $associateTo;
    }
    
    // Check position length - we now have 50 characters
    if (strlen($position) > 50) {
        $position = substr($position, 0, 47) . '...';
    }
    
    // Use the org_id from the current user's session to ensure users are created within the same organization
    $org_id = $_SESSION['org_id'];

    // Check if username already exists
    $checkStmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        header("Location: AddAccount.php?error=Username already exists");
        exit();
    }
    
    // Insert the new user
    $stmt = $conn->prepare("INSERT INTO users (username, password, name, position, department, org_id, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssss", $username, $password, $name, $position, $department, $org_id);
    
    if ($stmt->execute()) {
        header("Location: AddAccount.php?success=Account created successfully!");
        exit();
    } else {
        $errorMsg = "Failed to create account: " . $conn->error;
        // Add debugging information for database column limitations
        if (strpos($conn->error, "Data too long") !== false) {
            $errorMsg .= "<br>Error details:<br>";
            $errorMsg .= "Position length: " . strlen($position) . " characters<br>";
            $errorMsg .= "Department length: " . strlen($department) . " characters<br>";
            $errorMsg .= "Please use shorter values for these fields.";
        }
        header("Location: AddAccount.php?error=" . urlencode($errorMsg));
        exit();
    }
} else {
    header("Location: AddAccount.php");
    exit();
}
