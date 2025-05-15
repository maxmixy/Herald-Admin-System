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
        <title>Create Account</title>
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
                <p class="text-gray-600 mt-2">Add new members to the organization</p>
            </div>

            <!-- Account Creation Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <?php if (isset($_GET['success'])) { ?>
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><?php echo $_GET['success']; ?></span>
                    </div>
                <?php } ?>
                
                <?php if (isset($_GET['error'])) { ?>
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><?php echo urldecode($_GET['error']); ?></span>
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
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm position-select">
                                <option value="">Select position</option>
                                <option value="President">President</option>
                                <option value="Vice President - Internal">Vice President - Internal</option>
                                <option value="Vice President - External">Vice President - External</option>
                                <option value="Secretary">Secretary</option>
                                <option value="Treasurer">Treasurer</option>
                                <option value="Auditor">Auditor</option>
                                <option value="Level Rep">Level Rep</option>
                                <option value="Associate">Associate</option>
                            </select>
                        </div>

                        <!-- Associate To (Appears only when Associate is selected) -->
                        <div id="associateToContainer" class="hidden">
                            <label for="AssociateTo" class="block text-sm font-medium text-gray-700">Associate To</label>
                            <select name="AssociateTo" id="AssociateTo"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm">
                                <option value="">Select position</option>
                                <option value="President">President</option>
                                <option value="Vice President - Internal">Vice President - Internal</option>
                                <option value="Vice President - External">Vice President - External</option>
                                <option value="Secretary">Secretary</option>
                                <option value="Treasurer">Treasurer</option>
                                <option value="Auditor">Auditor</option>
                                <option value="Level Rep">Level Rep</option>
                            </select>
                        </div>
                        
                        <!-- Department -->
                        <div>
                            <label for="Department" class="block text-sm font-medium text-gray-700">Department</label>
                            <select name="Department" id="Department" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm department-select">
                                <option value="">Select department</option>
                                <option value="President">President</option>
                                <option value="Logistics">Logistics</option>
                                <option value="Creatives">Creatives</option>
                                <option value="Finance">Finance</option>
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
                                            <a href='delete_account.php?username=" . htmlspecialchars($account['username']) . "' class='text-red-600 hover:text-red-900'><i class='fas fa-trash'></i></a>
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
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            /* Custom Tailwind-inspired Select2 styles */
            .select2-container--default .select2-selection--single {
                height: auto !important;
                padding: 0.375rem 0.75rem;
                border-radius: 0.375rem !important;
                border-color: #D1D5DB !important;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            }
            
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 100% !important;
            }
            
            .select2-dropdown {
                border-color: #D1D5DB !important;
                border-radius: 0.375rem !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            }
            
            .select2-container--default .select2-results__option--highlighted[aria-selected] {
                background-color: #9B1919 !important;
                color: white !important;
            }
            
            .select2-container--default .select2-search--dropdown .select2-search__field {
                border-color: #D1D5DB !important;
                border-radius: 0.25rem !important;
                padding: 0.375rem 0.75rem !important;
            }
            
            .select2-results__option {
                padding: 0.5rem 0.75rem !important;
            }
            
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 1.5 !important;
                color: #1F2937 !important;
            }
            
            /* Hide search dropdown for department */
            #Department-container .select2-search--dropdown {
                display: none;
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Position select with searchable functionality
                $('#MemberPosition').select2({
                    tags: true,
                    placeholder: "Select or type to search",
                    allowClear: true,
                    width: '100%',
                    dropdownCssClass: 'rounded-md shadow-md',
                    minimumResultsForSearch: Infinity // Hide dropdown search
                });
                
                // Clear placeholder text when clicking on Position field
                $(document).on('mousedown', '.select2-selection--single', function(e) {
                    if ($(this).closest('.select2-container').prev().is('#MemberPosition')) {
                        $('#MemberPosition').select2('open');
                        e.preventDefault();
                    }
                });
                
                // Make the Select2 input field directly editable for Position and implement search
                $('#MemberPosition').on('select2:open', function() {
                    $('.select2-search__field').css('display', 'none');
                    let renderedField = $('.select2-container--open .select2-selection__rendered');
                    renderedField.attr('contenteditable', 'true').focus();
                    
                    // Clear placeholder text
                    if (renderedField.text().trim() === "Select or type to search") {
                        renderedField.text('');
                    }
                    
                    // Filter dropdown options as user types
                    renderedField.on('input', function() {
                        let searchText = $(this).text().trim().toLowerCase();
                        $('.select2-results__option').each(function() {
                            let optionText = $(this).text().toLowerCase();
                            if (optionText.includes(searchText)) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    });
                });
                
                // Handle selection from filtered dropdown
                $(document).on('click', '.select2-results__option', function() {
                    let selectedText = $(this).text();
                    let select = $('#MemberPosition');
                    
                    // Check if option exists, otherwise create it
                    if (select.find("option[value='" + selectedText + "']").length === 0) {
                        select.append(new Option(selectedText, selectedText, true, true));
                    }
                    
                    select.val(selectedText).trigger('change');
                    select.select2('close');
                });
                
                // Initialize Department select without typing capability
                $('#Department').select2({
                    tags: false, 
                    placeholder: "Select department",
                    allowClear: true,
                    width: '100%',
                    dropdownCssClass: 'rounded-md shadow-md',
                    minimumResultsForSearch: Infinity, // Hide search completely
                    containerCssClass: 'department-container',
                    dropdownParent: $('#Department').parent()
                });
                
                // Prevent typing in Department field
                $('#Department').on('keydown', function(e) {
                    e.preventDefault();
                    return false;
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
                                allowClear: true,
                                width: '100%',
                                dropdownCssClass: 'rounded-md shadow-md',
                                minimumResultsForSearch: Infinity // Hide search completely
                            });
                        }
                    } else {
                        associateContainer.classList.add('hidden');
                        document.getElementById('AssociateTo').removeAttribute('required');
                    }
                });
                
                // Handle Reset button functionality
                $('button[type="reset"]').on('click', function(e) {
                    e.preventDefault(); // Prevent default reset behavior
                    
                    // Clear regular form fields
                    $('form')[0].reset();
                    
                    // Reset Select2 fields
                    $('#MemberPosition').val('').trigger('change');
                    $('#Department').val('').trigger('change');
                    $('#AssociateTo').val('').trigger('change');
                    
                    // Hide Associate To container
                    associateContainer.classList.add('hidden');
                    document.getElementById('AssociateTo').removeAttribute('required');
                    
                    // Focus on the first input field
                    $('#MemberName').focus();
                });
                
                // Handle form submission to format associate position
                document.querySelector('form').addEventListener('submit', function(e) {
                    const positionValue = positionSelect.value;
                    if (positionValue === 'Associate') {
                        const associateToValue = document.getElementById('AssociateTo').value;
                        if (associateToValue) {
                            // Let the server handle the formatting since it needs to be short enough for the database
                            // No need to disable fields or create hidden ones - we'll use the server-side logic
                        }
                    }
                });
                
                // Ensure org_id restriction is enforced
                // This is handled on the server side in AddAccProc.php
            });
        </script>
    </body>
</html>
