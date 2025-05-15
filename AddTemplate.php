<?php
ob_start();
session_start();
include "db_conn.php";
include_once "notifications.php";

if (!isset($_SESSION["username"]) && !isset($_SESSION["org_id"])) { 
    header("Location: Login.php");
    exit();
}

// Handle template upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['template_file'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $date_updated = date('Y-m-d');
    $file = $_FILES['template_file'];
    $upload_dir = 'uploads/templates/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $file_name = basename($file['name']);
    $target_path = $upload_dir . time() . '_' . $file_name;
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // Check if updating existing template (by name & category)
        $check = mysqli_query($conn, "SELECT * FROM templates WHERE Name='$name' AND Category='$category'");
        if ($check && mysqli_num_rows($check) > 0) {
            // Update existing
            mysqli_query($conn, "UPDATE templates SET Path='$target_path', Date_updated='$date_updated' WHERE Name='$name' AND Category='$category'");
            echo '<script>window.onload = function() { showNotification(\'Template updated successfully!\', \'success\'); };</script>';
        } else {
            // Insert new
            mysqli_query($conn, "INSERT INTO templates (Name, Category, Path, Date_updated) VALUES ('$name', '$category', '$target_path', '$date_updated')");
            echo '<script>window.onload = function() { showNotification(\'Template uploaded successfully!\', \'success\'); };</script>';
        }
    } else {
        echo '<script>window.onload = function() { showNotification(\'File upload failed.\', \'error\'); };</script>';
    }
}
?>


<!DOCTYPE html>
<html>
    <head>  
        <title>Adding Templates</title>
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
            <div class="max-w-5x7 mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16"> 
                    <?php include "tabs.php"; ?>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-6">
            <!-- Page Title -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Upload Templates</h1>
                <p class="text-gray-600 mt-2">Upload or update document templates here</p>
            </div>

            <!-- Upload Form -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Upload or Update Template</h2>
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label class="block text-gray-700">Template Name</label>
                        <input type="text" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-bedan-red-light">
                    </div>
                    <div>
                        <label class="block text-gray-700">Category</label>
                        <select name="category" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-bedan-red-light">
                            <option value="Organization Documents">Organization Documents</option>
                            <option value="Project Documents">Project Documents</option>
                            <option value="Finance Documents">Finance Documents</option>
                            <option value="Physical Arrangement Documents">Physical Arrangement Documents</option>
                            <option value="Miscellaneous">Miscellaneous</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700">Template File</label>
                        <input type="file" name="template_file" accept=".doc,.docx,.pdf,.xls,.xlsx,.ppt,.pptx,.txt" required class="mt-1 block w-full">
                    </div>
                    <button type="submit" class="bg-bedan-red text-white px-4 py-2 rounded hover:bg-bedan-red-light">Upload/Update</button>
                </form>
            </div>

            <!-- Document Category Template -->
            <?php
            $docs_query = mysqli_query($conn, "SELECT * FROM templates ORDER BY Category, Name");
            $categories = array();
            if ($docs_query && mysqli_num_rows($docs_query) > 0) {
                while ($doc = mysqli_fetch_assoc($docs_query)) {
                    $categories[$doc['Category']][] = $doc;
                }
                foreach ($categories as $category => $docs) {
                    echo '<div class="bg-white rounded-lg shadow-md p-6 mb-6">';
                    echo '<h1 class="text-2xl font-bold text-gray-800">' . htmlspecialchars($category) . '</h1>';
                    echo '<div class="mt-4">';
                    foreach ($docs as $doc) {
                        echo '<div class="flex items-center justify-between">';
                        echo '<div>';
                        echo '<p class="text-gray-600 mt-2">' . htmlspecialchars($doc['Name']) . '</p>';
                        echo '<a href="' . htmlspecialchars($doc['Path']) . '" class="text-bedan-red hover:underline flex items-center" download>';
                        echo '<i class="fas fa-download mr-2"></i> Download Template';
                        echo '</a>';
                        echo '</div>';
                        // Replace button
                        echo '<form method="POST" enctype="multipart/form-data" style="display:inline-block;" class="ml-4">';
                        echo '<input type="hidden" name="name" value="' . htmlspecialchars($doc['Name']) . '">';
                        echo '<input type="hidden" name="category" value="' . htmlspecialchars($doc['Category']) . '">';
                        echo '<input type="file" name="template_file" accept=".doc,.docx,.pdf,.xls,.xlsx,.ppt,.pptx,.txt" required class="inline-block">';
                        echo '<button type="submit" class="ml-2 bg-bedan-red text-white px-3 py-1 rounded hover:bg-bedan-red-light text-sm">Replace</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="bg-white rounded-lg shadow-md p-6 mb-6 text-gray-400">Templates are yet to be uploaded.</div>';
            }
            ?>
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