<?php
session_start();
require 'ConnMiniP.php';

// Check if user is logged in
if (isset($_SESSION['email_address'])) {
    $email = $_SESSION['email_address'];
} else {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
    exit();
}

// Check if booking_id is provided
if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];
} else {
    // Redirect or display error if booking_id is missing
    header("Location: IndexMiniP.php");
    exit();
}

// Get user details
$user = GetUserByEmail($email);
$user_id = $user['user_id'];

// Get booking details (includes movie + cinema info)
$booking = GetBookingDetailsByID($booking_id);

// Get tickets for this booking
$tickets = GetTicketsByBookingID($booking_id);

// Check if booking data was retrieved
if (!$booking) {
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Error</title></head><body><div style='padding: 20px; text-align: center;'>Booking details not found for ID: " . htmlspecialchars($booking_id) . ". <a href='IndexMiniP.php'>Go Home</a></div></body></html>";
    exit();
}

// Calculate total price for display
$total_price = 0;
foreach ($tickets as $ticket) {
    $total_price += $ticket['Ticket_Price'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket - TZA Theatre Zenith Atrium</title>
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

        /* Secondary button styling (for Review) */
        .secondary-btn-outline {
            background: transparent;
            border: 2px solid #a40000;
            color: #a40000;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .dark .secondary-btn-outline {
            border: 2px solid #a40000;
            color: #a40000;
        }

        .secondary-btn-outline:hover {
            background: #a40000;
            color: white;
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(164, 0, 0, 0.3);
        }

        /* Typography styles */
        .form-label {font-family:'Inter',sans-serif;font-weight:500; color: #1f2937;}
        .dark .form-label {color:white;}
        .form-text {font-family:'Inter',sans-serif;font-weight:400; color: #4b5563;}
        .dark .form-text {color:rgba(255,255,255,0.9);}
        .heading {font-family:'Inter',sans-serif;font-weight:600; color: #1f2937;}
        .dark .heading {color:white;}
        
        /* New styles for ticket info display */
        .ticket-info-section { border-bottom: 1px dashed #a40000; padding-bottom: 1rem; margin-bottom: 1rem; }
        .dark .ticket-info-section { border-bottom: 1px dashed rgba(255, 255, 255, 0.4); }
        .detail-label { font-weight: 500; color: #730000; }
        .dark .detail-label { color: #ffcccc; }
        .ticket-entry { border-bottom: 1px dotted rgba(0, 0, 0, 0.1); padding: 0.5rem 0; }
        .dark .ticket-entry { border-bottom: 1px dotted rgba(255, 255, 255, 0.1); }
        .ticket-entry:last-child { border-bottom: none; }
    </style>
</head>
<body class="min-h-screen font-sans flex flex-col">
<script>
    // Theme initialization
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

<nav class="sticky top-0 z-50 shadow-2xl border-b border-gray-300 bg-white/90 backdrop-blur-md dark:bg-darkbg/95 dark:border-primary-700/50 dark:shadow-none">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <span class="text-2xl font-bold text-gray-900 dark:text-white heading tracking-wider">Theatre Zenith Atrium</span>
            <div class="flex items-center space-x-4">
                <button id="theme-toggle" title="Toggle Dark Mode" class="p-3 rounded-full bg-gray-200 dark:bg-cardbg text-gray-700 dark:text-gray-400 hover:text-primary-500 dark:hover:text-primary-500 transition-colors duration-300 shadow-md">
                    <i class="fas fa-moon dark:hidden text-xl"></i>
                    <i class="fas fa-sun hidden dark:block text-xl"></i>
                </button>
                <div class="flex items-center space-x-3 group relative">
                    <div class="hidden md:block text-right">
                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-xs"> 
                            <?php echo htmlspecialchars($_SESSION['email_address']); ?>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-primary-500 transition-colors">
                            <a href="LogoutMiniP.php" class="hover:underline transition-colors">Sign Out</a>
                        </div>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold text-lg shadow-lg ring-2 ring-primary-500">
                        <?php echo strtoupper(substr($_SESSION['email_address'], 0, 1)); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<main class="relative z-10 py-12 flex-grow">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="bg-white dark:bg-black rounded-xl p-8 shadow-2xl mb-8 text-center">
            <h2 class="text-4xl heading text-primary-500 flex items-center justify-center">
                <i class="fas fa-ticket-alt mr-3"></i> Your E-Ticket
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-300 mt-2 form-text">
                Your Booking is Confirmed! Enjoy the show.

        <div class="glass-container rounded-2xl p-8 lg:p-12">
            
            <h3 class="text-2xl heading text-gray-900 dark:text-white mb-6 border-b pb-2">
                <i class="fas fa-check-circle mr-2 text-primary-500"></i> Booking Confirmation
            </h3>

            <div class="mb-6 bg-primary-500/10 dark:bg-primary-500/20 p-4 rounded-lg">
                <p class="text-lg form-text text-gray-800 dark:text-gray-100 flex justify-between items-center">
                    <span class="font-bold text-primary-700 dark:text-primary-500">Booking Reference:</span>
                    <span class="ml-2 font-mono text-xl tracking-wider"><?php echo htmlspecialchars($booking_id); ?></span>
                </p>
            </div>

            <div class="ticket-info-section grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <p class="detail-label text-xl">Movie Title:</p>
                    <p class="text-2xl form-text text-gray-900 dark:text-white font-bold"><?php echo htmlspecialchars($booking['movie_title']); ?></p>
                </div>
                <div>
                    <p class="detail-label">Genre / Rating:</p>
                    <p class="text-lg form-text text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($booking['genre']); ?> / <?php echo htmlspecialchars($booking['pg_rating']); ?></p>
                </div>
                <div>
                    <p class="detail-label">Duration:</p>
                    <p class="text-lg form-text text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($booking['duration']); ?> mins</p>
                </div>
            </div>

            <div class="ticket-info-section grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <p class="detail-label">Date:</p>
                    <p class="text-lg form-text text-gray-700 dark:text-gray-300 font-semibold"><?php echo htmlspecialchars($booking['date']); ?></p>
                </div>
                <div>
                    <p class="detail-label">Time:</p>
                    <p class="text-lg form-text text-gray-700 dark:text-gray-300 font-semibold"><?php echo htmlspecialchars($booking['showtime']); ?></p>
                </div>
                <div>
                    <p class="detail-label">Experience:</p>
                    <p class="text-lg form-text text-gray-700 dark:text-gray-300 font-semibold"><?php echo htmlspecialchars($booking['experience']); ?></p>
                </div>
                <div class="col-span-1 sm:col-span-3">
                    <p class="detail-label">Cinema Location:</p>
                    <p class="text-lg form-text text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($booking['location']); ?></p>
                </div>
            </div>

            <div class="mt-6">
                <h4 class="text-xl heading text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-chair mr-2 text-primary-500"></i> Your Seats (Total: <?php echo count($tickets); ?>)
                </h4>
                <div class="space-y-2">
                    <?php if (count($tickets) > 0): ?>
                        <?php 
                        foreach ($tickets as $ticket): 
                        ?>
                            <div class="ticket-entry text-md form-text text-gray-800 dark:text-gray-200 flex justify-between items-center bg-gray-50/70 dark:bg-cardbg/70 p-3 rounded-md">
                                <span class="font-medium text-sm text-gray-600 dark:text-gray-400">#<?php echo htmlspecialchars($ticket['Ticket_ID']); ?></span>
                                <span class="font-semibold text-xl text-primary-700 dark:text-primary-500">Seat <?php echo htmlspecialchars($ticket['seat_number']); ?></span>
                                <span class="text-sm italic text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($ticket['Ticket_Type']); ?></span>
                                <span class="font-bold text-lg">RM<?php echo number_format($ticket['Ticket_Price'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="mt-4 pt-4 border-t-2 border-primary-500/50 flex justify-between items-center">
                            <span class="text-xl heading text-gray-900 dark:text-white">Total Paid:</span>
                            <span class="text-2xl font-extrabold text-primary-700 dark:text-primary-500">RM<?php echo number_format($total_price, 2); ?></span>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 dark:text-gray-400 italic">No tickets found for this booking.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-gray-300 dark:border-gray-700 space-y-4">
                <a href="ReviewMiniP.php?booking_id=<?php echo urlencode($booking_id); ?>"
                    class="w-full text-center py-3 rounded-lg secondary-btn-outline font-semibold text-lg transition block">
                    <i class="fas fa-thumbs-up mr-2"></i> Leave a Review on the Booking Process
                </a>
                <a href="IndexMiniP.php" class="w-full py-4 rounded-lg select-btn font-semibold text-xl block text-center">
                    <i class="fas fa-film mr-2"></i> Book Another Movie
                </a>
            </div>

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
themeToggle.addEventListener('click', () => {
    const isDark = htmlElement.classList.toggle('dark');
    localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
});
</script>
</body>
</html>



