<?php
ob_start();
session_start();
include "db_conn.php";
include_once "notifications.php";

if (!isset($_SESSION["username"]) && !isset($_SESSION["org_id"])) { 
    header("Location: Login.php");
    exit();
}
?>


<!DOCTYPE html>
<html>
    <head>  
        <title>Document Templates</title>
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
                <h1 class="text-2xl font-bold text-gray-800">Document Templates</h1>
                <p class="text-gray-600 mt-2">See document templates provided by the Office of Student Affairs</p>
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
                        echo '<p class="text-gray-600 mt-2">' . htmlspecialchars($doc['Name']) . '</p>';
                        echo '<a href="' . htmlspecialchars($doc['Path']) . '" class="text-bedan-red hover:underline flex items-center" download>';
                        echo '<i class="fas fa-download mr-2"></i> Download Template';
                        echo '</a>';
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