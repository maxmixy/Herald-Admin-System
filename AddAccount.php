<?php
session_start();
include "db_conn.php";

// Check if user is logged in and has permission to access this page
if (!isset($_SESSION["username"]) || !isset($_SESSION["org_id"]) || 
    ($_SESSION["position"] != "President" && $_SESSION["position"] != "Head Admin" && $_SESSION["position"] != "OSA")) {
    header("Location: Login.php");
    exit();
}

// Continue with page content
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Account - The Bedan Herald</title>
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
            <!-- Page Title -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Create New Account</h1>
                <p class="text-gray-600 mt-2">Add new members to The Bedan Herald team</p>
            </div>

            <!-- Account Creation Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <?php if (isset($_GET['success'])) { ?>
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><?php echo $_GET['success']; ?></span>
                    </div>
                <?php } ?>

                <form action="AddAccProc.php" method="post" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Member Name -->
                        <div>
                            <label for="MemberName" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="MemberName" id="MemberName" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"
                                placeholder="Enter full name">
                        </div>

                        <!-- Position -->
                        <div>
                            <label for="MemberPosition" class="block text-sm font-medium text-gray-700">Position</label>
                            <select name="MemberPosition" id="MemberPosition" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <option value="">Select position</option>
                                <option value="Writer">Writer</option>
                                <option value="Section Editor">Section Editor</option>
                                <option value="Admin">Admin</option>
                                <option value="Head Admin">Head Admin</option>
                                <option value="Human Resources">Human Resources</option>
                            </select>
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="UserName" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" name="UserName" id="UserName" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"
                                placeholder="Enter username">
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="UserPassword" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="UserPassword" id="UserPassword" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"
                                placeholder="Enter password">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3">
                        <button type="reset" 
                            class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-6 py-2 rounded-md text-sm font-medium transition-all">
                            <i class="fas fa-undo mr-2"></i>Reset
                        </button>
                        <button type="submit" 
                            class="bg-bedan-red hover:bg-bedan-red-light text-white px-6 py-2 rounded-md text-sm font-medium transition-all">
                            <i class="fas fa-user-plus mr-2"></i>Create Account
                        </button>
                    </div>
                </form>
            </div>

            <!-- Recent Accounts -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Recently Created Accounts</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            // Get recently created accounts from the database (last 7 days)
                            $recentAccountsQuery = "SELECT username, name, position, department, created_at
                                                   FROM users 
                                                   WHERE org_id = ? 
                                                   ORDER BY created_at DESC 
                                                   LIMIT 10";
                            $recentStmt = $conn->prepare($recentAccountsQuery);
                            $recentStmt->bind_param("s", $_SESSION['org_id']);
                            $recentStmt->execute();
                            $recentResult = $recentStmt->get_result();
                            
                            if ($recentResult->num_rows > 0) {
                                while ($account = $recentResult->fetch_assoc()) {
                                    echo "<tr class='hover:bg-gray-50'>";
                                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>" . htmlspecialchars($account['name']) . "</td>";
                                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>" . htmlspecialchars($account['position']) . "</td>";
                                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>" . htmlspecialchars($account['username']) . "</td>";
                                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>" . htmlspecialchars($account['department']) . "</td>";
                                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>" . ($account['created_at'] ? date('M d, Y', strtotime($account['created_at'])) : 'N/A') . "</td>";
                                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>
                                            <a href='edit_account.php?username=" . htmlspecialchars($account['username']) . "' class='text-blue-600 hover:text-blue-900 mr-2'><i class='fas fa-edit'></i></a>
                                            <a href='delete_account.php?username=" . htmlspecialchars($account['username']) . "' class='text-red-600 hover:text-red-900' onclick=\"return confirm('Are you sure you want to delete this account?')\"><i class='fas fa-trash'></i></a>
                                        </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='px-6 py-4 text-center text-gray-500'>No accounts found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
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
