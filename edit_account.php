<?php
session_start();
include "db_conn.php";

// Check if user is logged in and has permission to edit accounts
if (!isset($_SESSION["username"]) || !isset($_SESSION["org_id"]) || $_SESSION["position"] != "President") {
    header("Location: Login.php");
    exit();
}

// Check if we have a username to edit
if (!isset($_GET['username']) || empty($_GET['username'])) {
    header("Location: AddAccount.php?error=" . urlencode("No username specified for editing"));
    exit();
}

$username = $_GET['username'];

// Get the account details
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND org_id = ?");
$stmt->bind_param("ss", $username, $_SESSION['org_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: AddAccount.php?error=" . urlencode("Account not found or you don't have permission to edit it"));
    exit();
}

$accountData = $result->fetch_assoc();

// Process form submission if method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function validate($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    $name = validate($_POST['MemberName']);
    $department = validate($_POST['Department']);
    
    // Handle position and associate
    if (isset($_POST['associate_hidden']) && !empty($_POST['associate_hidden'])) {
        // This means we have a properly formatted associate position from the form
        $position = validate($_POST['associate_hidden']);
    } else {
        $position = validate($_POST['MemberPosition']);
        
        // Check if the user selected Associate but didn't properly submit the hidden field
        if ($position === 'Associate' && isset($_POST['AssociateTo']) && !empty($_POST['AssociateTo'])) {
            $associateTo = validate($_POST['AssociateTo']);
            
            // Format the position based on the associateTo value
            if ($associateTo == 'Secretary') {
                $position = "Associate -Secretary";
            } else if ($associateTo == 'President') {
                $position = "Associate -President";
            } else if ($associateTo == 'Treasurer') {
                $position = "Associate -Treasurer";
            } else if ($associateTo == 'Auditor') {
                $position = "Associate -Auditor";
            } else if ($associateTo == 'Level Rep') {
                $position = "Associate -Level Rep";
            } else if (strpos($associateTo, 'Vice President') !== false) {
                // For Vice Presidents, use a shorter format
                if (strpos($associateTo, 'Internal') !== false) {
                    $position = "Associate -VP Internal";
                } else if (strpos($associateTo, 'External') !== false) {
                    $position = "Associate -VP External";
                } else {
                    $position = "Associate -VP";
                }
            } else {
                // For any other role, use a generic format
                $position = "Associate -" . substr($associateTo, 0, 10);
            }
        }
    }
    
    // Strictly enforce position length
    if (strlen($position) > 20) {
        $position = substr($position, 0, 20);
    }
    
    // Update the account
    $updateStmt = $conn->prepare("UPDATE users SET name=?, position=?, department=? WHERE username=? AND org_id=?");
    $updateStmt->bind_param("sssss", $name, $position, $department, $username, $_SESSION['org_id']);
    
    if ($updateStmt->execute()) {
        // Use JavaScript redirect to ensure success message is displayed properly
        echo "<script>window.location='AddAccount.php?success=" . urlencode("Account updated successfully") . "';</script>";
        exit();
    } else {
        header("Location: edit_account.php?username=$username&error=" . urlencode("Failed to update account: " . $conn->error));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Account - The Bedan Herald</title>
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
                <h1 class="text-2xl font-bold text-gray-800">Edit Account</h1>
                <p class="text-gray-600 mt-2">Update member information</p>
            </div>

            <!-- Edit Account Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <?php if (isset($_GET['error'])) { ?>
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><?php echo urldecode($_GET['error']); ?></span>
                    </div>
                <?php } ?>

                <form action="edit_account.php?username=<?php echo htmlspecialchars($username); ?>" method="post" class="space-y-6" id="editAccountForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Member Name -->
                        <div>
                            <label for="MemberName" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="MemberName" id="MemberName" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"
                                placeholder="Enter full name" value="<?php echo htmlspecialchars($accountData['name']); ?>">
                        </div>

                        <!-- Position -->
                        <div>
                            <label for="MemberPosition" class="block text-sm font-medium text-gray-700">Position</label>
                            <select name="MemberPosition" id="MemberPosition" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm position-select">
                                <option value="">Select position</option>
                                <option value="President" <?php echo ($accountData['position'] == 'President') ? 'selected' : ''; ?>>President</option>
                                <option value="Vice President - Internal" <?php echo ($accountData['position'] == 'Vice President - Internal') ? 'selected' : ''; ?>>Vice President - Internal</option>
                                <option value="Vice President - External" <?php echo ($accountData['position'] == 'Vice President - External') ? 'selected' : ''; ?>>Vice President - External</option>
                                <option value="Secretary" <?php echo ($accountData['position'] == 'Secretary') ? 'selected' : ''; ?>>Secretary</option>
                                <option value="Treasurer" <?php echo ($accountData['position'] == 'Treasurer') ? 'selected' : ''; ?>>Treasurer</option>
                                <option value="Auditor" <?php echo ($accountData['position'] == 'Auditor') ? 'selected' : ''; ?>>Auditor</option>
                                <option value="Level Rep" <?php echo ($accountData['position'] == 'Level Rep') ? 'selected' : ''; ?>>Level Rep</option>
                                <option value="Associate" <?php echo (strpos($accountData['position'], 'Associate') === 0) ? 'selected' : ''; ?>>Associate</option>
                                <?php
                                // Add current position if it's not in the predefined list
                                $predefinedPositions = ['President', 'Vice President - Internal', 'Vice President - External', 'Secretary', 'Treasurer', 'Auditor', 'Level Rep'];
                                if (!in_array($accountData['position'], $predefinedPositions) && strpos($accountData['position'], 'Associate') !== 0) {
                                    echo '<option value="' . htmlspecialchars($accountData['position']) . '" selected>' . htmlspecialchars($accountData['position']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Associate To (Appears only when Associate is selected) -->
                        <div id="associateToContainer" class="<?php echo (strpos($accountData['position'], 'Associate') === 0) ? '' : 'hidden'; ?>">
                            <label for="AssociateTo" class="block text-sm font-medium text-gray-700">Associate To</label>
                            <select name="AssociateTo" id="AssociateTo"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <option value="">Select position</option>
                                <?php
                                // Extract associated position if it exists
                                $associatedTo = '';
                                if (strpos($accountData['position'], 'Associate -') === 0) {
                                    $associatedTo = substr($accountData['position'], 11); // After "Associate -"
                                } else if (strpos($accountData['position'], 'Assoc -') === 0) {
                                    // Support for other formats
                                    $associatedTo = substr($accountData['position'], 7);
                                } else if (strpos($accountData['position'], 'Assoc: ') === 0) {
                                    // Support for other formats
                                    $associatedTo = substr($accountData['position'], 6);
                                }
                                
                                // Map shortened VP values back to full titles
                                if ($associatedTo == 'VP Internal') {
                                    $associatedTo = 'Vice President - Internal';
                                } else if ($associatedTo == 'VP External') {
                                    $associatedTo = 'Vice President - External';
                                } else if ($associatedTo == 'VP') {
                                    $associatedTo = 'Vice President';
                                }
                                
                                $associateOptions = ['President', 'Vice President - Internal', 'Vice President - External', 'Secretary', 'Treasurer', 'Auditor', 'Level Rep'];
                                foreach ($associateOptions as $option) {
                                    $selected = ($option == $associatedTo) ? 'selected' : '';
                                    echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        
                        <!-- Department -->
                        <div>
                            <label for="Department" class="block text-sm font-medium text-gray-700">Department</label>
                            <select name="Department" id="Department" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm department-select">
                                <option value="">Select department</option>
                                <option value="President" <?php echo ($accountData['department'] == 'President') ? 'selected' : ''; ?>>President</option>
                                <option value="Logistics" <?php echo ($accountData['department'] == 'Logistics') ? 'selected' : ''; ?>>Logistics</option>
                                <option value="Creatives" <?php echo ($accountData['department'] == 'Creatives') ? 'selected' : ''; ?>>Creatives</option>
                                <option value="Finance" <?php echo ($accountData['department'] == 'Finance') ? 'selected' : ''; ?>>Finance</option>
                                <?php
                                // Add current department if it's not in the predefined list
                                $predefinedDepartments = ['President', 'Logistics', 'Creatives', 'Finance'];
                                if (!in_array($accountData['department'], $predefinedDepartments)) {
                                    echo '<option value="' . htmlspecialchars($accountData['department']) . '" selected>' . htmlspecialchars($accountData['department']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Username (Read-only) -->
                        <div>
                            <label for="UserName" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" id="UserName" readonly
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm"
                                value="<?php echo htmlspecialchars($accountData['username']); ?>">
                            <p class="mt-1 text-xs text-gray-500">Username cannot be changed</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3">
                        <a href="AddAccount.php" 
                            class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-6 py-2 rounded-md text-sm font-medium transition-all">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit" 
                            class="bg-bedan-red hover:bg-bedan-red-light text-white px-6 py-2 rounded-md text-sm font-medium transition-all">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-8">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
                &copy; All rights reserved.
            </div>
        </footer>
        <script src="notifications.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Select2 for searchable dropdowns
                $('.position-select, .department-select').select2({
                    tags: true,
                    placeholder: "Select or type to search",
                    allowClear: true
                });
                
                // Show/hide associate to dropdown based on position selection
                const positionSelect = document.getElementById('MemberPosition');
                const associateContainer = document.getElementById('associateToContainer');
                
                positionSelect.addEventListener('change', function() {
                    if (this.value === 'Associate') {
                        associateContainer.classList.remove('hidden');
                        document.getElementById('AssociateTo').setAttribute('required', 'required');
                        
                        // Initialize Select2 for AssociateTo dropdown if not already initialized
                        if (!$('#AssociateTo').data('select2')) {
                            $('#AssociateTo').select2({
                                placeholder: "Select who this user is associate to",
                                allowClear: true
                            });
                        }
                    } else {
                        associateContainer.classList.add('hidden');
                        document.getElementById('AssociateTo').removeAttribute('required');
                    }
                });
                
                // Initialize AssociateTo dropdown with Select2 if it's visible
                if (!associateContainer.classList.contains('hidden')) {
                    $('#AssociateTo').select2({
                        placeholder: "Select who this user is associate to",
                        allowClear: true
                    });
                }
                
                // Handle form submission to format associate position
                document.getElementById('editAccountForm').addEventListener('submit', function(e) {
                    const positionValue = positionSelect.value;
                    if (positionValue === 'Associate') {
                        const associateToValue = document.getElementById('AssociateTo').value;
                        if (associateToValue) {
                            // Let the server handle the formatting - no need to create special hidden fields
                        }
                    }
                });
            });
        </script>
    </body>
</html> 