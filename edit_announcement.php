<?php
session_start();
include "db_conn.php";

// Check if user is logged in and is President
if (!isset($_SESSION["username"]) || $_SESSION["position"] !== "President") {
    header("Location: Login.php");
    exit();
}

$error = "";
$success = "";
$announcement = null;

// Get announcement ID from URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch announcement details
    $query = "SELECT * FROM announcements WHERE announcement_id = ? AND org_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "is", $id, $_SESSION['org_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $announcement = mysqli_fetch_assoc($result);
    } else {
        header("Location: Home2.php");
        exit();
    }
} else {
    header("Location: Home2.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $category = trim($_POST["category"]);
    
    if (empty($title) || empty($content) || empty($category)) {
        $error = "All fields are required";
    } else {
        $sql = "UPDATE announcements SET title = ?, content = ?, category = ? 
                WHERE announcement_id = ? AND org_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssis", $title, $content, $category, $id, $_SESSION['org_id']);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Announcement updated successfully!";
            header("Location: Home2.php");
            exit();
        } else {
            $error = "Error updating announcement: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Announcement</title>
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
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Announcement</h1>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" required
                           value="<?php echo htmlspecialchars($announcement['title']); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                </div>
                
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category" id="category" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                        <option value="Important" <?php echo $announcement['category'] == 'Important' ? 'selected' : ''; ?>>Important</option>
                        <option value="Update" <?php echo $announcement['category'] == 'Update' ? 'selected' : ''; ?>>Update</option>
                        <option value="Reminder" <?php echo $announcement['category'] == 'Reminder' ? 'selected' : ''; ?>>Reminder</option>
                        <option value="Policy" <?php echo $announcement['category'] == 'Policy' ? 'selected' : ''; ?>>Policy</option>
                        <option value="Training" <?php echo $announcement['category'] == 'Training' ? 'selected' : ''; ?>>Training</option>
                    </select>
                </div>
                
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                    <textarea name="content" id="content" rows="6" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"><?php echo htmlspecialchars($announcement['content']); ?></textarea>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <a href="Home2.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="bg-bedan-red hover:bg-bedan-red-light text-white px-4 py-2 rounded-md text-sm font-medium transition-all">
                        Update Announcement
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html> 