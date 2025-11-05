<?php
session_start();
require 'ConnMiniP.php';

// --- MOVIE FETCH LOGIC (Simplified: No Search Filtering) ---
$movies = GetAllMovies(); 
// --- END MOVIE FETCH LOGIC ---

// Check if user is logged in (optional for index page)
$is_logged_in = isset($_SESSION['email_address']);

// Get user info only if logged in
$has_booking = false;
$latest_booking_id = null;

if ($is_logged_in) {
    $email = $_SESSION['email_address'];
    $sql_user = "SELECT * FROM users WHERE email_address = '$email'";
    $user_result = $conn->query($sql_user);
    $user = $user_result->fetch_assoc();
    $user_id = $user['user_id'];

    // Check if user has existing booking
    $sql_booking = "SELECT booking_id FROM booking WHERE user_id = '$user_id' ORDER BY booking_id DESC LIMIT 1";
    $booking_result = $conn->query($sql_booking);

    if ($booking_result && $booking_result->num_rows > 0) {
        $has_booking = true;
        $latest_booking = $booking_result->fetch_assoc();
        $latest_booking_id = $latest_booking['booking_id'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theatre Zenith Atrium</title>
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

        /* Improvised: Elevated Booking Status Card Glow */
        .dark .status-card-glow:hover {
            box-shadow: 0 10px 40px rgba(164, 0, 0, 0.2);
        }
        
        /* --- 3D Flip Card Styling --- */
        .flip-card {
            perspective: 1000px;
            width: 100%; 
            cursor: pointer;
            outline: none !important;
        }
        
        .flip-card-inner {
            position: relative;
            width: 100%;
            height: 100%; 
            transition: transform 0.8s; 
            transform-style: preserve-3d;
            aspect-ratio: 2/3; 
            border-radius: 0;
        }

        .flip-card:hover .flip-card-inner {
            transform: rotateY(180deg);
        }

        .flip-card-front, .flip-card-back {
            position: absolute; 
            width: 100%;
            height: 100%;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); 
            overflow: hidden; 
            border-radius: 0;
            border: none;
            outline: none;
        }

        .flip-card-front {
            background-color: #bbb;
            color: black;
        }

        .flip-card-front img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
            border-radius: 0;
        }
        
        .flip-card:hover .flip-card-front img {
            transform: scale(1.05); 
        }

        .flip-card-back {
            background: #ffffff;
            color: #1f2937;
            transform: rotateY(180deg);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.5rem;
            border: none;
            font-family: 'Inter', sans-serif;
        }

        .dark .flip-card-back {
            background: linear-gradient(145deg, #1f1f1f, #0a0a0a); 
            color: white;
            border: 1px solid rgba(164, 0, 0, 0.3); 
        }

        /* Remove focus outlines */
        .flip-card:focus,
        .flip-card:focus-visible,
        .flip-card-front:focus,
        .flip-card-back:focus {
            outline: none !important;
            box-shadow: none !important;
        }

        /* Clean typography */
        .heading {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
        }

        .form-text {
            font-family: 'Inter', sans-serif;
            font-weight: 400;
        }
    </style>
</head>
<body class="bg-white text-gray-900 dark:bg-darkbg dark:text-white min-h-screen font-sans">
    
            <nav class="sticky top-0 z-50 shadow-2xl border-b border-gray-900 
                 bg-white/90 backdrop-blur-md
                 dark:bg-darkbg/95 dark:border-primary-700/50 dark:shadow-none">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20"> 
                <div class="flex items-center space-x-3">
                    <span class="text-2xl font-bold text-gray-900 dark:text-white heading tracking-wider">Theatre Zenith Atrium</span>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Theme Toggle Button -->
                    <button id="theme-toggle" title="Toggle Dark Mode" class="p-3 rounded-full bg-gray-200 dark:bg-cardbg text-gray-700 dark:text-gray-400 hover:text-primary-500 dark:hover:text-primary-500 transition-colors duration-300 shadow-md">
                        <i class="fas fa-moon dark:hidden text-xl"></i>
                        <i class="fas fa-sun hidden dark:block text-xl"></i>
                    </button>
                    
                    <!-- Profile Link (only show when logged in) -->
                    <?php if ($is_logged_in): ?>
                        <a href="ProfileMiniP.php" class="text-gray-700 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-500 transition-colors font-medium flex items-center space-x-1">
                            <i class="fas fa-user"></i>
                            <span class="hidden sm:inline">Profile</span>
                        </a>
                    <?php endif; ?>
                    
                    <div class="flex items-center space-x-3 group relative">
                        <div class="hidden md:block text-right">
                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-xs"> 
                                <?php echo $is_logged_in ? htmlspecialchars($_SESSION['email_address']) : 'Guest User'; ?>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-primary-500 transition-colors">
                                <?php if ($is_logged_in): ?>
                                    <a href="LogoutMiniP.php" class="hover:underline transition-colors">Sign Out</a>
                                <?php else: ?>
                                    <a href="LoginMiniP.php" class="hover:underline transition-colors">Login</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold text-lg shadow-lg ring-2 ring-primary-500">
                                <?php echo $is_logged_in ? strtoupper(substr($_SESSION['email_address'], 0, 1)) : 'G'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <section class="relative h-96 md:h-[500px] overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="https://i.pinimg.com/1200x/f0/b0/c3/f0b0c339e09dfaa74f7c8f68b94a5ce3.jpg" 
                 alt="Coming Soon: Catnip Heist" 
                 class="w-full h-full object-cover object-center opacity-70 dark:opacity-50 transition-opacity">
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-darkbg/95 via-darkbg/60 to-transparent z-10 dark:from-darkbg dark:via-darkbg/70"></div>

        <div class="relative z-20 h-full flex flex-col justify-end pb-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
             <div class="flex justify-between items-end mb-4 w-full">
                <span class="bg-primary-500 text-white text-lg md:text-xl font-extrabold heading uppercase tracking-widest px-4 py-1.5 shadow-xl transition-transform duration-300 transform -rotate-1 dark:bg-primary-500 dark:text-white">
                    Coming Soon
                </span>
                <div></div>
            </div>
            <h1 class="text-4xl md:text-6xl font-bold mb-3 text-white heading leading-tight">
                Catnip Heist
            </h1>
            <p class="text-xl md:text-2xl text-gray-200 font-light max-w-3xl form-text">
                Book Your Ultimate Cinematic Experience. Secure your seat for the latest blockbusters and independent features.
            </p>
        </div>
    </section>

    <main class="py-12">
    
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
            <div class="bg-lightcard dark:bg-cardbg rounded-xl p-8 shadow-2xl transition-shadow status-card-glow
                        border border-gray-200 dark:border-primary-500/50 hover:scale-[1.005]">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-3xl heading text-gray-900 dark:text-white">
                            <?php if ($is_logged_in): ?>
                                Welcome, <?php echo htmlspecialchars(explode('@', $_SESSION['email_address'])[0]); ?>!
                            <?php else: ?>
                                Welcome to Theatre Zenith Atrium!
                            <?php endif; ?>
                        </h2>
                        <p class="text-md text-gray-600 dark:text-gray-400 mt-1 form-text">
                            <?php if ($is_logged_in): ?>
                                Your current booking status is displayed below.
                            <?php else: ?>
                                Please login to book tickets and view your booking status.
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <?php if ($is_logged_in && $has_booking): ?>
                    <div class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-4 w-full md:w-auto">
                        <div class="flex items-center space-x-2 bg-primary-500/10 text-primary-500 dark:bg-primary-900/40 dark:text-primary-300 py-2 px-4 rounded-full font-medium border border-primary-500 dark:border-primary-700 w-full justify-center">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Active Booking Found</span>
                        </div>
                        <a href="TicketMiniP.php?booking_id=<?php echo $latest_booking_id; ?>" 
                           class="bg-primary-500 hover:bg-primary-600 text-white font-bold py-3 px-6 rounded-lg shadow-xl transition-all w-full sm:w-auto text-center transform hover:scale-105">
                            <i class="fas fa-eye mr-2"></i> View Latest Ticket
                        </a>
                    </div>
                    <?php elseif ($is_logged_in): ?>
                    <div class="text-gray-500 dark:text-gray-500 py-3 px-4 bg-gray-100 dark:bg-black/20 rounded-lg form-text">
                        <i class="fas fa-info-circle mr-2"></i> No active bookings to show. Time to watch a movie!
                    </div>
                    <?php else: ?>
                    <div class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-4 w-full md:w-auto">
                        <a href="LoginMiniP.php" 
                           class="bg-primary-500 hover:bg-primary-600 text-white font-bold py-3 px-6 rounded-lg shadow-xl transition-all w-full sm:w-auto text-center transform hover:scale-105">
                            <i class="fas fa-sign-in-alt mr-2"></i> Login to Book Tickets
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <section class="mb-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="pb-6 mb-8 border-b-4 border-primary-500/70">
                    <h2 class="text-4xl heading text-gray-900 dark:text-white pl-0 mb-2">
                        Now Showing
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 pl-0 form-text">
                        Hover over a poster to see details and book your seat.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 
                        sm:grid-cols-3 sm:gap-6
                        md:grid-cols-4 md:gap-6
                        lg:grid-cols-5 lg:gap-8
                        px-4 sm:px-6 lg:px-8">
                <?php if (!empty($movies)): ?>
                    <?php foreach($movies as $movie): ?>
                    
                    <div class="flip-card" tabindex="0"> 
                        <div class="flip-card-inner">
                            <div class="flip-card-front">
                                <img src="<?php echo htmlspecialchars($movie['image_path']); ?>" 
                                          alt="<?php echo htmlspecialchars($movie['movie_title']); ?>">
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/50 to-transparent p-4">
                                </div>
                            </div>
                            
                            <div class="flip-card-back p-6 flex flex-col justify-between">
                                <div class="w-full">
                                    <h4 class="text-xl heading text-primary-500 dark:text-primary-400 transition-colors">
                                        <?php echo htmlspecialchars($movie['movie_title']); ?>
                                    </h4>
                                    <?php
                                    $rating_class = 'bg-primary-500'; 
                                    if (strpos($movie['pg_rating'], 'PG-13') !== false) $rating_class = 'bg-yellow-600';
                                    elseif (strpos($movie['pg_rating'], 'G') !== false) $rating_class = 'bg-green-600';
                                    elseif (strpos($movie['pg_rating'], 'R') !== false) $rating_class = 'bg-red-700';
                                    ?>
                                    <span class="inline-block mt-2 text-white py-1 px-3 rounded-md font-extrabold text-sm <?php echo $rating_class; ?> shadow-md">
                                        <?php echo htmlspecialchars($movie['pg_rating']); ?>
                                    </span>
                                </div>

                                <div class="my-4 text-left space-y-3 w-full">
                                    <p class="text-base text-gray-700 dark:text-gray-300 transition-colors flex items-center form-text">
                                        <i class="far fa-clock mr-3 text-lg text-primary-500 dark:text-primary-400 transition-colors w-6"></i> 
                                        <span class="font-semibold">Duration:</span> <?php echo htmlspecialchars($movie['duration']); ?>
                                    </p>
                                    <p class="text-base text-gray-700 dark:text-gray-300 transition-colors flex items-center form-text">
                                        <i class="fas fa-tags mr-3 text-lg text-primary-500 dark:text-primary-400 transition-colors w-6"></i> 
                                        <span class="font-semibold">Genre:</span> <?php echo htmlspecialchars($movie['genre']); ?>
                                    </p>
                                </div>
                                
                                <form action="CinemaMiniP.php" method="GET" class="w-full mt-4">
                                    <input type="hidden" name="movie_id" value="<?php echo htmlspecialchars($movie['movie_id']); ?>">
                                    <button type="submit" 
                                            class="w-full bg-primary-500 hover:bg-primary-600 text-white font-bold py-3 px-4 rounded-lg shadow-xl 
                                                   transition-all duration-300 transform hover:scale-[1.03] flex items-center justify-center text-lg">
                                        <i class="fas fa-ticket-alt mr-3"></i> Book Now
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-5 w-full text-center py-12 bg-lightcard dark:bg-cardbg rounded-xl border border-gray-300 dark:border-gray-700 shadow-lg mx-4 sm:mx-6 lg:mx-8">
                        <i class="fas fa-film text-5xl text-gray-400 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 heading">
                            No movies are scheduled right now.
                        </h3>
                        <p class="text-gray-500 dark:text-gray-500 mt-2 form-text">Please check back soon for our latest listings.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="bg-gray-100 dark:bg-cardbg border-t border-gray-200 dark:border-primary-700/50 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-4 md:mb-0">
                    <span class="text-xl font-bold text-gray-900 dark:text-white heading"> Theatre Zenith Atrium</span>
                </div>
                
                <div class="text-center md:text-right">
                    <p class="text-gray-500 dark:text-gray-400 text-sm form-text">© 2023 Theatre Zenith Atrium. All rights reserved.</p>
                    <p class="text-gray-400 dark:text-gray-500 text-xs mt-1 form-text">Design inspired by modern cinematic interfaces.</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // --- Theme Toggle Functionality ---
        const themeToggle = document.getElementById('theme-toggle');
        const htmlElement = document.documentElement;
        
        // 1. Initial setup (Check localStorage or system preference)
        const isDarkMode = localStorage.getItem('color-theme') === 'dark' || 
                            (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

        if (isDarkMode) {
            htmlElement.classList.add('dark');
        } else {
            htmlElement.classList.remove('dark');
        }
        
        // 2. Toggle Handler
        themeToggle.addEventListener('click', function() {
            const isCurrentlyDark = htmlElement.classList.toggle('dark');
            
            if (isCurrentlyDark) {
                localStorage.setItem('color-theme', 'dark');
            } else {
                localStorage.setItem('color-theme', 'light');
            }
        });

        // Remove focus outlines from flip cards when clicked
        document.addEventListener('click', function(e) {
            if (e.target.closest('.flip-card')) {
                e.target.closest('.flip-card').blur();
            }
        });
    </script>
</body>
</html>
