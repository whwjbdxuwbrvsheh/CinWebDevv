<?php
session_start();
require 'ConnMiniP.php';

// Check if user is logged in
if (!isset($_SESSION['email_address'])) {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
    exit();
}

// Check if required session data exists
if (!isset($_SESSION['selected_seats']) || !isset($_SESSION['cinema_id']) || !isset($_SESSION['movie_id'])) {
    header("Location: IndexMiniP.php");
    exit();
}

// Get session data
$selected_seat_ids = $_SESSION['selected_seats'];
$cinema_id = $_SESSION['cinema_id'];
$movie_id = $_SESSION['movie_id'];
$user_id = $_SESSION['user_id'];

// Get movie details
$movie = GetMovieByID($movie_id);

// Get cinema details
$cinema = GetCinemaByID($cinema_id);

// Get selected seats details
$selected_seats_details = [];
$total_price = 0;

foreach($selected_seat_ids as $seat_id) {
    $seat = GetSeatByID($seat_id);
    if($seat) {
        $selected_seats_details[] = $seat;
        $total_price += $seat['price'];
    }
}

// Handle confirmation submission
if(isset($_GET['confirm_booking'])) {
    // Insert booking record
    $booking_id = CreateBooking($user_id, $cinema_id);
    
    if($booking_id) {
        // Mark seats as occupied
        foreach($selected_seat_ids as $seat_id) {
            UpdateSeatAvailability($seat_id, 0); // 0 = not available
        }
        
        // Store booking_id and total_price in session for payment page
        $_SESSION['booking_id'] = $booking_id;
        $_SESSION['total_price'] = $total_price;
        
        // Redirect to payment page
        header("Location: PaymentMiniP.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - TZA Theatre Zenith Atrium</title>
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

        /* Link styling */
        .nav-link {
            transition: all 0.3s ease;
            position: relative;
            font-family: 'Inter', sans-serif;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Cinema card styling */
        .cinema-card {
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .dark .cinema-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .cinema-card:hover {
            background: rgba(255, 255, 255, 0.8);
            border-color: rgba(164, 0, 0, 0.3);
            transform: translateY(-5px);
        }

        .dark .cinema-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(164, 0, 0, 0.3);
        }

        /* Clean typography */
        .form-label {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
        }

        .dark .form-label {
            color: white;
        }

        .form-text {
            font-family: 'Inter', sans-serif;
            font-weight: 400;
        }

        .dark .form-text {
            color: rgba(255, 255, 255, 0.9);
        }

        .heading {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
        }

        .dark .heading {
            color: white;
        }

        /* Status card glow effect */
        .status-card-glow {
            transition: all 0.3s ease;
        }

        .dark .status-card-glow:hover {
            box-shadow: 0 10px 40px rgba(164, 0, 0, 0.2);
        }

        /* Light mode text colors */
        .text-white {
            color: #1f2937;
        }

        .dark .text-white {
            color: white;
        }

        .text-white\/70 {
            color: rgba(55, 65, 81, 0.8);
        }

        .dark .text-white\/70 {
            color: rgba(255, 255, 255, 0.7);
        }

        .text-white\/80 {
            color: rgba(55, 65, 81, 0.9);
        }

        .dark .text-white\/80 {
            color: rgba(255, 255, 255, 0.8);
        }

        .text-primary-400 {
            color: #c45a00;
        }

        .dark .text-primary-400 {
            color: #fb923c;
        }

        .bg-primary-500\/20 {
            background-color: rgba(164, 0, 0, 0.1);
        }

        .dark .bg-primary-500\/20 {
            background-color: rgba(164, 0, 0, 0.2);
        }

        .border-primary-500\/30 {
            border-color: rgba(164, 0, 0, 0.2);
        }

        .dark .border-primary-500\/30 {
            border-color: rgba(164, 0, 0, 0.3);
        }

        .text-gray-400 {
            color: #9ca3af;
        }

        .dark .text-gray-400 {
            color: #9ca3af;
        }

        .text-gray-500 {
            color: #6b7280;
        }

        .dark .text-gray-500 {
            color: #6b7280;
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

    <!-- Background Image with Subtle Overlay -->
    <div class="absolute inset-0 z-0">
        <img src="https://i.pinimg.com/1200x/f0/b0/c3/f0b0c339e09dfaa74f7c8f68b94a5ce3.jpg" 
             alt="Cinema Background" 
             class="w-full h-full object-cover opacity-30 dark:opacity-60"
             style="mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0.8) 70%, rgba(0,0,0,0) 100%); -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0.8) 70%, rgba(0,0,0,0) 100%);">
        <div class="absolute inset-0 bg-gradient-to-br from-white/50 via-white/30 to-white/50 dark:from-black/70 dark:via-black/50 dark:to-black/70"></div>
    </div>

    <!-- Navigation -->
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

    <!-- Main Content -->
    <main class="relative z-10 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
                                    <!-- Welcome Status Card -->
            <div class="bg-white dark:bg-black rounded-xl p-8 shadow-2xl transition-shadow status-card-glow hover:scale-[1.005] mb-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-3xl heading text-gray-900 dark:text-white">
                            Confirm Your Booking, <?php echo htmlspecialchars(explode('@', $_SESSION['email_address'] ?? 'Guest')[0]); ?>!
                        </h2>
                        <p class="text-md text-gray-600 dark:text-gray-300 mt-1 form-text">
                            Please review your booking details before proceeding to payment
                        </p>
                    </div>
                    
                    <h2 class="text-2xl heading text-primary-500">
                        "<?php echo htmlspecialchars($movie['movie_title']); ?>"
                    </h2>
                </div>
            </div>
            
            <!-- Booking Details -->
            <div class="glass-container rounded-2xl p-8 mb-8">
                <div class="pb-6 mb-8 border-b-4 border-primary-500/70">
                    <h2 class="text-4xl heading text-gray-900 dark:text-white pl-0 mb-2">
                        Booking Summary
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-white/70 form-text pl-0">
                        Review your selected movie, cinema, and seats.
                    </p>
                </div>

                <!-- Movie & Cinema Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="cinema-card rounded-xl p-6">
                        <h3 class="text-xl heading text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-film text-primary-500 mr-3"></i>
                            Movie Details
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3 text-gray-700 dark:text-white/80 form-text">
                                <i class="fas fa-ticket-alt text-primary-500"></i>
                                <span class="font-medium">Movie:</span>
                                <span><?php echo htmlspecialchars($movie['movie_title']); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="cinema-card rounded-xl p-6">
                        <h3 class="text-xl heading text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-building text-primary-500 mr-3"></i>
                            Cinema Details
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3 text-gray-700 dark:text-white/80 form-text">
                                <i class="fas fa-map-marker-alt text-primary-500"></i>
                                <span class="font-medium">Location:</span>
                                <span><?php echo htmlspecialchars($cinema['location']); ?></span>
                            </div>
                            <div class="flex items-center space-x-3 text-gray-700 dark:text-white/80 form-text">
                                <i class="fas fa-star text-primary-500"></i>
                                <span class="font-medium">Experience:</span>
                                <span><?php echo htmlspecialchars($cinema['experience']); ?></span>
                            </div>
                            <div class="flex items-center space-x-3 text-gray-700 dark:text-white/80 form-text">
                                <i class="far fa-calendar text-primary-500"></i>
                                <span class="font-medium">Date:</span>
                                <span><?php echo htmlspecialchars($cinema['date']); ?></span>
                            </div>
                            <div class="flex items-center space-x-3 text-gray-700 dark:text-white/80 form-text">
                                <i class="far fa-clock text-primary-500"></i>
                                <span class="font-medium">Showtime:</span>
                                <span><?php echo htmlspecialchars($cinema['showtime']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selected Seats -->
                <div class="cinema-card rounded-xl p-6 mb-8">
                    <h3 class="text-xl heading text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-chair text-primary-500 mr-3"></i>
                        Selected Seats
                    </h3>
                    
                    <?php if (!empty($selected_seats_details)): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach($selected_seats_details as $seat): ?>
                                <div class="bg-primary-500/20 border border-primary-500/30 rounded-lg p-4 flex justify-between items-center">
                                    <div>
                                        <div class="font-bold text-lg text-gray-900 dark:text-white">
                                            <?php echo htmlspecialchars($seat['seat_number']); ?>
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400 form-text">
                                            <?php echo htmlspecialchars($seat['seat_type']); ?>
                                        </div>
                                    </div>
                                    <div class="text-lg font-bold text-primary-500">
                                        RM <?php echo number_format($seat['price'], 2); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-gray-500 dark:text-gray-400 form-text">No seats selected.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Total Price -->
                <div class="cinema-card rounded-xl p-6 bg-gradient-to-r from-primary-500/10 to-primary-700/10 border border-primary-500/30">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div>
                            <h3 class="text-2xl heading text-gray-900 dark:text-white">
                                Total Amount
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 form-text">
                                Including all selected seats
                            </p>
                        </div>
                        <div class="text-3xl font-bold text-primary-500 mt-4 md:mt-0">
                            RM <?php echo number_format($total_price, 2); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <form method="GET" class="w-full md:w-auto">
                    <button type="submit" name="confirm_booking" 
                            class="w-full py-4 px-8 rounded-lg select-btn font-semibold text-lg transform hover:scale-[1.02] transition-transform">
                        <i class="fas fa-credit-card mr-2"></i>
                        Confirm & Proceed to Payment
                    </button>
                </form>

                <form action="SeatsMiniP.php" method="GET" class="w-full md:w-auto">
                    <input type="hidden" name="cinema_id" value="<?php echo $cinema_id; ?>">
                    <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
                    <button type="submit" 
                            class="w-full py-4 px-8 rounded-lg secondary-btn font-semibold text-lg transform hover:scale-[1.02] transition-transform">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Seat Selection
                    </button>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
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
