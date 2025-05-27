<?php
session_start();
include "db_conn.php";
include_once "notifications.php";

// Check if user is logged in and is President
if (!isset($_SESSION["username"]) || $_SESSION["position"] !== "President") {
    header("Location: Login.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $category = trim($_POST["category"]);
    
    if (empty($title) || empty($content) || empty($category)) {
        $error = "All fields are required";
    } else {
        $sql = "INSERT INTO announcements (title, content, created_by, org_id, category) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $title, $content, $_SESSION['username'], $_SESSION['org_id'], $category);
        
        if (mysqli_stmt_execute($stmt)) {
            // Get all users in the organization
            $usersQuery = "SELECT username FROM users WHERE org_id = ?";
            $usersStmt = mysqli_prepare($conn, $usersQuery);
            mysqli_stmt_bind_param($usersStmt, "s", $_SESSION['org_id']);
            mysqli_stmt_execute($usersStmt);
            $usersResult = mysqli_stmt_get_result($usersStmt);
            
            // Create notification for each user
            while ($user = mysqli_fetch_assoc($usersResult)) {
                $message = "New announcement: " . $title;
                createNotification($conn, $user['username'], $_SESSION['org_id'], $message, 'announcement', mysqli_insert_id($conn));
            }
            
            $_SESSION['success'] = "Announcement posted successfully!";
            header("Location: Home2.php");
            exit();
        } else {
            $error = "Error posting announcement: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Announcement</title>
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
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Create New Announcement</h1>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                </div>
                
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category" id="category" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                        <option value="Important">Important</option>
                        <option value="Update">Update</option>
                        <option value="Reminder">Reminder</option>
                        <option value="Policy">Policy</option>
                        <option value="Training">Training</option>
                    </select>
                </div>
                
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                    <textarea name="content" id="content" rows="6" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"></textarea>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <a href="Home2.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="bg-bedan-red hover:bg-bedan-red-light text-white px-4 py-2 rounded-md text-sm font-medium transition-all">
                        Create Announcement
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html> 