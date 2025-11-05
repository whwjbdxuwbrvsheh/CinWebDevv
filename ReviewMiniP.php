<?php
session_start();
require 'ConnMiniP.php';

// Check if user logged in
if (!isset($_SESSION['email_address'])) {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
    exit();
}

// Ensure booking_id is available
if (!isset($_GET['booking_id'])) {
    // Redirect to a safe page if booking_id is missing, assuming IndexMiniP.php
    header("Location: IndexMiniP.php");
    exit();
}

$booking_id = $_GET['booking_id'];
$email = $_SESSION['email_address'];

// Get user details
$user = GetUserByEmail($email);
$user_id = $user['user_id'];

// Fetch all ratings using function
$ratings = GetAllRatings();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Insert review using function
    CreateReview($user_id, $rating, $comment);

    // Redirect back to ticket page
    header("Location: TicketMiniP.php?booking_id=$booking_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Review - TZA Theatre Zenith Atrium</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // --- Tailwind Configuration (Cinematic Red Theme) ---
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            500: '#a40000', // CINEMATIC RED 
                            600: '#8e0000', 
                            700: '#730000', 
                        },
                        darkbg: '#0a0a0a', // Near-black background
                        cardbg: '#181818', // Slightly lighter for card contrast
                        lightcard: '#f0f0f0', 
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        /* --- Global Transitions --- */
        * {
            transition: background-color 0.5s ease-in-out, color 0.5s ease-in-out, border-color 0.5s ease-in-out, box-shadow 0.5s ease-in-out, transform 0.3s ease-in-out;
        }

        /* Light mode body background */
        body {
            background-color: #ffffff;
        }

        .dark body {
            background-color: #000000;
        }

        /* Glass morphism effect */
        .glass-container {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
        }

        .dark .glass-container {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(164, 0, 0, 0.2);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }

        /* Button styling */
        .select-btn {
            background: #a40000;
            border: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
        }

        .select-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(164, 0, 0, 0.4);
            background: #8e0000;
        }

        .select-btn:active {
            transform: translateY(0);
        }

        /* Secondary button styling */
        .secondary-btn {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: #1f2937;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
        }

        .dark .secondary-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .secondary-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.9);
        }

        .dark .secondary-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        /* Clean typography */
        .form-label {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            color: #1f2937; /* Default light mode color */
        }

        .dark .form-label {
            color: white;
        }

        .form-text {
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            color: #4b5563; /* Default light mode color */
        }

        .dark .form-text {
            color: rgba(255, 255, 255, 0.9);
        }

        .heading {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            color: #1f2937; /* Default light mode color */
        }

        .dark .heading {
            color: white;
        }

        /* Input/Select/Textarea Styling - Crucial for Dark Mode Visibility */
        .form-input {
            background: white;
            border: 1px solid #d1d5db;
            color: #1f2937;
            transition: all 0.3s ease;
        }

        .dark .form-input {
            background: #1f2937; /* Darker input background for dark mode */
            border: 1px solid #4b5563;
            color: white;
        }
        
        /* Dropdown/Select Specific Styling to ensure text is visible */
        select.form-input option {
            background-color: #ffffff; /* Explicit light mode option bg */
            color: #1f2937; /* Explicit light mode option color */
        }
        
        .dark select.form-input option {
            background-color: #1f2937; /* Explicit dark mode option bg */
            color: #ffffff; /* Explicit dark mode option color */
        }

        /* Status card glow effect */
        .status-card-glow {
            transition: all 0.3s ease;
        }

        .dark .status-card-glow:hover {
            box-shadow: 0 10px 40px rgba(164, 0, 0, 0.2);
        }

        /* Overrides for text colors in light mode */
        .dark .text-white {
            color: white;
        }
        .dark .text-white\/70 {
            color: rgba(255, 255, 255, 0.7);
        }
    </style>
</head>
<body class="min-h-screen font-sans">
<script>
    // Theme initialization - must run immediately before page render
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
</script>

    <div class="absolute inset-0 z-0">
        <img src="https://i.pinimg.com/1200x/f0/b0/c3/f0b0c339e09dfaa74f7c8f68b94a5ce3.jpg" 
             alt="Cinema Background" 
             class="w-full h-full object-cover opacity-30 dark:opacity-60"
             style="mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0.8) 70%, rgba(0,0,0,0) 100%); -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0.8) 70%, rgba(0,0,0,0) 100%);">
        <div class="absolute inset-0 bg-gradient-to-br from-white/50 via-white/30 to-white/50 dark:from-black/70 dark:via-black/50 dark:to-black/70"></div>
    </div>

    <nav class="sticky top-0 z-50 shadow-2xl border-b border-gray-300
                    bg-white/90 backdrop-blur-md
                    dark:bg-darkbg/95 dark:border-primary-700/50 dark:shadow-none">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20"> 
                <div class="flex items-center space-x-3">
                    <span class="text-2xl font-bold text-gray-900 dark:text-white heading tracking-wider">Theatre Zenith Atrium</span>
                </div>

                <div class="flex items-center space-x-4">
                    <button id="theme-toggle" title="Toggle Dark Mode" class="p-3 rounded-full bg-gray-200 dark:bg-cardbg text-gray-700 dark:text-gray-400 hover:text-primary-500 dark:hover:text-primary-500 transition-colors duration-300 shadow-md">
                        <i class="fas fa-moon dark:hidden text-xl"></i>
                        <i class="fas fa-sun hidden dark:block text-xl"></i>
                    </button>
                    
                    <div class="flex items-center space-x-3 group relative">
                        <div class="hidden md:block text-right">
                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-xs"> 
                                <?php echo htmlspecialchars($_SESSION['email_address'] ?? 'Guest User'); ?>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-primary-500 transition-colors">
                                <a href="LogoutMiniP.php" class="hover:underline transition-colors">Sign Out</a>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold text-lg shadow-lg ring-2 ring-primary-500">
                                <?php echo strtoupper(substr($_SESSION['email_address'] ?? 'G', 0, 1)); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="relative z-10 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-black rounded-xl p-8 shadow-2xl transition-shadow status-card-glow hover:scale-[1.005] mb-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-3xl heading text-gray-900 dark:text-white">
                            Website Review
                        </h2>
                        <p class="text-md text-gray-600 dark:text-gray-300 mt-1 form-text">
                            Welcome <?php echo htmlspecialchars($_SESSION['email_address'] ?? 'Guest'); ?>! We value your feedback on your booking experience.
                        </p>
                    </div>
                    
                    <h3 class="text-xl heading text-primary-500 text-center md:text-right">
                        How was your experience?
                    </h3>
                </div>
            </div>
            
            <div class="glass-container rounded-2xl p-8 mb-8">
                <div class="pb-6 mb-8 border-b-4 border-primary-500/70">
                    <h2 class="text-4xl heading text-gray-900 dark:text-white pl-0 mb-2">
                        Submit Your Review
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-white/70 form-text pl-0">
                        Share your thoughts on the website's usability and design.
                    </p>
                </div>

                <form method="POST" class="space-y-6">
                    <div>
                        <label for="rating" class="form-label block text-lg mb-2">
                            <i class="fas fa-star text-primary-500 mr-2"></i> Rating:
                        </label>
                        <select name="rating" id="rating" required
                                class="form-input w-full py-3 px-4 rounded-lg focus:ring-primary-500 focus:border-primary-500 shadow-md appearance-none">
                            <option value="" class="text-gray-500">Select Rating</option>
                            <?php foreach ($ratings as $rating_row): ?>
                                <option value="<?php echo htmlspecialchars($rating_row['rating_value']); ?>">
                                    <?php echo htmlspecialchars($rating_row['rating_label']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="comment" class="form-label block text-lg mb-2">
                            <i class="fas fa-comment-alt text-primary-500 mr-2"></i> Comment:
                        </label>
                        <textarea name="comment" id="comment" rows="4" 
                                  class="form-input w-full py-3 px-4 rounded-lg focus:ring-primary-500 focus:border-primary-500 shadow-md resize-none"
                                  placeholder="Enter your detailed feedback here..."></textarea>
                    </div>

                    <button type="submit"
                            class="w-full py-4 px-8 rounded-lg select-btn font-semibold text-lg transform hover:scale-[1.01] transition-transform shadow-xl">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Review
                    </button>
                </form>
            </div>

            <div class="flex justify-center">
                <a href="TicketMiniP.php?booking_id=<?php echo htmlspecialchars($booking_id); ?>" class="w-full md:w-auto">
                    <button type="button"
                            class="w-full py-4 px-8 rounded-lg secondary-btn font-semibold text-lg transform hover:scale-[1.02] transition-transform">
                        <i class="fas fa-ticket-alt mr-2"></i>
                        View My Ticket
                    </button>
                </a>
            </div>

        </div>
    </main>

    <footer class="relative z-10 bg-white/90 backdrop-blur-md border-t border-gray-300 dark:bg-black/80 dark:border-gray-800 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-4 md:mb-0">
                    <span class="text-xl font-bold text-gray-900 dark:text-white heading"> Theatre Zenith Atrium</span>
                </div>
                
                <div class="text-center md:text-right">
                    <p class="text-gray-600 dark:text-gray-400 text-sm form-text">© 2023 Theatre Zenith Atrium. All rights reserved.</p>
                    <p class="text-gray-500 dark:text-gray-500 text-xs mt-1 form-text">Design inspired by modern cinematic interfaces.</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // --- Theme Toggle Functionality ---
        const themeToggle = document.getElementById('theme-toggle');
        const htmlElement = document.documentElement;
        
        // Toggle Handler
        themeToggle.addEventListener('click', function() {
            const isCurrentlyDark = htmlElement.classList.toggle('dark');
            
            if (isCurrentlyDark) {
                localStorage.setItem('color-theme', 'dark');
            } else {
                localStorage.setItem('color-theme', 'light');
            }
        });
    </script>
</body>
</html>

