<?php
session_start();
include "db_conn.php";

// Check if user is logged in and has permission to delete accounts
if (!isset($_SESSION["username"]) || !isset($_SESSION["org_id"]) || $_SESSION["position"] != "President") {
    header("Location: Login.php");
    exit();
}

// Check if username parameter exists
if (!isset($_GET['username']) || empty($_GET['username'])) {
    header("Location: AddAccount.php?error=" . urlencode("No username specified for deletion"));
    exit();
}

$username = trim($_GET['username']);

// Check if we're receiving a POST confirmation to actually perform the deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure the account being deleted belongs to the same organization
    $checkStmt = $conn->prepare("SELECT username FROM users WHERE username = ? AND org_id = ?");
    $checkStmt->bind_param("ss", $username, $_SESSION['org_id']);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows === 0) {
        // Account doesn't exist or doesn't belong to this organization
        header("Location: AddAccount.php?error=" . urlencode("Account not found or you don't have permission to delete it"));
        exit();
    }

    // Don't allow users to delete their own account
    if ($username === $_SESSION['username']) {
        header("Location: AddAccount.php?error=" . urlencode("You cannot delete your own account"));
        exit();
    }

    // First, check if there are any tasks assigned to this user and handle them
    $tasksExist = false;
    $checkTasksStmt = $conn->prepare("SELECT COUNT(*) as count FROM tasks WHERE member_id = ?");
    $checkTasksStmt->bind_param("s", $username);
    $checkTasksStmt->execute();
    $taskResult = $checkTasksStmt->get_result();
    if ($taskResult && $taskResult->fetch_assoc()['count'] > 0) {
        $tasksExist = true;
        // Update the tasks to be assigned to the current user
        $updateTasksStmt = $conn->prepare("UPDATE tasks SET member_id = ? WHERE member_id = ?");
        $updateTasksStmt->bind_param("ss", $_SESSION['username'], $username);
        $updateTasksStmt->execute();
    }

    // Delete the account
    $deleteStmt = $conn->prepare("DELETE FROM users WHERE username = ? AND org_id = ?");
    $deleteStmt->bind_param("ss", $username, $_SESSION['org_id']);

    if ($deleteStmt->execute()) {
        $successMessage = "Account successfully deleted.";
        if ($tasksExist) {
            $successMessage .= " Any tasks assigned to this user have been reassigned to you.";
        }
        
        // Use a JavaScript redirect to ensure the success message is shown
        echo "<script>window.location='AddAccount.php?success=" . urlencode($successMessage) . "';</script>";
        exit();
    } else {
        header("Location: AddAccount.php?error=" . urlencode("Failed to delete account: " . $conn->error));
        exit();
    }
} else {
    // We're just getting the confirmation page
    // Ensure the account exists and belongs to the same organization
    $checkStmt = $conn->prepare("SELECT name FROM users WHERE username = ? AND org_id = ?");
    $checkStmt->bind_param("ss", $username, $_SESSION['org_id']);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: AddAccount.php?error=" . urlencode("Account not found or you don't have permission to delete it"));
        exit();
    }
    
    $userData = $result->fetch_assoc();
    $userName = $userData['name'];
    
    // Display confirmation page
    ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>  
            <title>Confirm Delete - The Bedan Herald</title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <script src="https://cdn.tailwindcss.com"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <script>
                tailwind.config = {
                    theme: {
                        extend: {
                            colors: {
                                'bedan-red': '#9B1919',
                                'bedan-red-light': '#cc2424',
                            }
                        }
                    }
                }
            </script>
        </head>
        <body class="bg-gray-50 min-h-screen">
            <!-- Header Navigation -->
            <nav class="bg-gradient-to-r from-bedan-red to-bedan-red-light fixed w-full top-0 z-50 shadow-lg">
                <div class="max-w-2x3 mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <?php include "tabs.php"; ?>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-6">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Delete Account</h1>
                    <p class="text-gray-600 mt-2">Confirm account deletion</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle text-5xl text-yellow-500 mb-4"></i>
                        <h2 class="text-xl font-bold mb-4">Are you sure you want to delete this account?</h2>
                        <p class="mb-2"><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                        <p class="mb-6"><strong>Name:</strong> <?php echo htmlspecialchars($userName); ?></p>
                        <p class="text-red-600 mb-6">This action cannot be undone. Any tasks assigned to this user will be reassigned to you.</p>
                        
                        <div class="flex justify-center space-x-4">
                            <a href="AddAccount.php" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-6 py-2 rounded-md text-sm font-medium transition-all">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                            <form method="POST" action="delete_account.php?username=<?php echo urlencode($username); ?>">
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md text-sm font-medium transition-all">
                                    <i class="fas fa-trash mr-2"></i>Delete Account
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 mt-8">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
                    &copy; All rights reserved.
                </div>
            </footer>
            <script src="notifications.js"></script>
        </body>
    </html>
    <?php
}
?> 