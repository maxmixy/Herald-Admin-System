<?php
// Include database connection
include "db_conn.php";

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: Unable to connect to the database");
}

// Get the current column type
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'position'");
if (!$result) {
    die("Error checking column: " . $conn->error);
}

$row = $result->fetch_assoc();
echo "Current position column: " . $row['Field'] . " - " . $row['Type'] . "<br>";

// Alter the column to increase its size
$alterQuery = "ALTER TABLE users MODIFY position VARCHAR(50)";
if ($conn->query($alterQuery) === TRUE) {
    echo "Success! The position column has been changed to VARCHAR(50)<br>";
    
    // Verify the change
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'position'");
    $row = $result->fetch_assoc();
    echo "Updated position column: " . $row['Field'] . " - " . $row['Type'] . "<br>";
} else {
    echo "Error updating column: " . $conn->error . "<br>";
}

// Close the connection
$conn->close();
echo "Done.";
?> 
 