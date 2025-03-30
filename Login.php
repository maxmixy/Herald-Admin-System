<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - The Bedan Herald</title>
    <link rel="stylesheet" href="AdminStyle.css">
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
    <style>
        .login-bg {
            background-image: url('https://images.unsplash.com/photo-1517842645767-c639042777db?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-gray-100 login-bg min-h-screen flex items-center justify-center">
    <div class="w-full max-w-7xl mx-auto p-6">
        <div class="flex flex-col md:flex-row rounded-xl shadow-2xl overflow-hidden">
            <!-- Left side - Brand/Logo Section -->
            <div class="bg-gradient-to-br from-bedan-red to-bedan-red-light w-full md:w-6/12 p-8 text-white flex flex-col justify-between">
                <div>
                    <img src="imgs/logo.png" alt="Logo" class="h-auto mx-auto mb-8">
                    <h1 class="text-3xl font-bold text-center mb-4">San Beda College Alabang</h1>
                    <h1 class="text-2xl font-bold text-center mb-4">Organizational Management System</h1>
                    <p class="text-lg text-center opacity-80 mb-8">San Beda University's Official Student Publication</p>
                </div>
                <div class="space-y-6">
                    <!-- Removed the three sections with text about "Delivering truth...", 
                         "Student-led journalism..." and "Writing the history..." -->
                </div>
                <div class="text-center text-sm opacity-70 mt-8">
                    &copy; <?php echo date("Y"); ?> Bedan Information Technology Society. All Rights Reserved.
                </div>
            </div>
            
            <!-- Right side - Login Form -->
            <div class="w-full md:w-6/12 glass-effect p-8 md:p-12">
                <div class="text-center mb-10 mt-10">
                    <div class="mt-10"></div>
                    <h2 class="text-2xl font-bold text-gray-800">Welcome Back</h2>
                    <p class="text-gray-600">Please login to your account</p>
                </div>
                
                <?php if(isset($_GET['error'])){ ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p class="font-medium">Error</p>
                        <p><?php echo $_GET['error']; ?></p>
                    </div>
                <?php } ?>
                
                <form action="LogProc.php" method="post" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Student Number</label>
                        <div class="relative">
                            <input type="text" id="username" name="username" 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm h-12" 
                                   placeholder="Enter your username" required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-bedan-red focus:ring-bedan-red sm:text-sm h-12" 
                                   placeholder="Enter your password" required>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="text-sm">
                            <a href="#" class="font-medium text-bedan-red hover:text-bedan-red-light">
                                Forgot password?
                            </a>
                        </div>
                    </div>
                    
                    <div>
                        <button type="submit" 
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-bedan-red hover:bg-bedan-red-light focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bedan-red transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                            <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                        </button>
                    </div>
                </form>
                
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        Having trouble logging in? Please contact the IT department for support.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 
