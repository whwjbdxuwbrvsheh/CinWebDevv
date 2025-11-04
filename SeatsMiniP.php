<?php
session_start();
require 'ConnMiniP.php';

if (!isset($_SESSION['email_address'])) {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
    exit();
}

if (!isset($_GET['cinema_id']) || !isset($_GET['movie_id'])) {
    header("Location: CinemaMiniP.php");
    exit();
}

$cinema_id = $_GET['cinema_id'];
$movie_id = $_GET['movie_id'];

$_SESSION['cinema_id'] = $cinema_id;
$_SESSION['movie_id'] = $movie_id;

$seat_prices = [
    'VIP' => 35,
    'Premium' => 25,
    'Standard' => 15,
];

$movie = GetMovieByID($movie_id);
$cinema = GetCinemaByID($cinema_id);

$seats_raw = GetSeatsByCinemaID($cinema_id);
$seats = [];
foreach ($seats_raw as $seat) {
    $seat['price'] = $seat_prices[$seat['seat_type']];
    $seats[$seat['seat_id']] = $seat;
}

$seats_by_row = [];
foreach($seats as $seat) {
    $row = substr($seat['seat_number'], 0, 1);
    $seats_by_row[$row][] = $seat;
}

foreach($seats_by_row as $row => $row_seats) {
    usort($seats_by_row[$row], function($a, $b) {
        $numA = (int)substr($a['seat_number'], 1);
        $numB = (int)substr($b['seat_number'], 1);
        return $numA - $numB;
    });
}

ksort($seats_by_row);
$seats_by_row = array_reverse($seats_by_row, true);

if(isset($_GET['selected_seats']) && !empty($_GET['selected_seats'])) {
    $selected_seats = $_GET['selected_seats'];
    $_SESSION['selected_seats'] = $selected_seats;
    header("Location: ConfirmationMiniP.php");
    exit();
}

if(isset($_GET['proceed']) && (!isset($_GET['selected_seats']) || empty($_GET['selected_seats']))) {
    $error_message = "Please select at least one seat before proceeding!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Selection - TZA Theatre Zenith Atrium</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            500: '#a40000',
                            600: '#8e0000',
                            700: '#730000',
                        },
                        darkbg: '#0a0a0a',
                        cardbg: '#181818',
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
        * {
            transition: background-color 0.5s ease-in-out, color 0.5s ease-in-out, border-color 0.5s ease-in-out, box-shadow 0.5s ease-in-out, transform 0.3s ease-in-out;
        }

        body {
            background-color: #ffffff;
        }

        .dark body {
            background-color: #000000;
        }

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

        .select-btn:disabled {
            background: #4b5563;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

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

        .status-card-glow {
            transition: all 0.3s ease;
        }

        .dark .status-card-glow:hover {
            box-shadow: 0 10px 40px rgba(164, 0, 0, 0.2);
        }

        .cinema-seat {
            width: 45px;
            height: 55px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            filter: drop-shadow(0 6px 12px rgba(0,0,0,0.4));
        }

        .dark .cinema-seat {
            filter: drop-shadow(0 6px 12px rgba(0,0,0,0.4));
        }

        .seat-number-label {
            z-index: 10;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            text-shadow: none;
        }

        .dark .seat-number-label {
            text-shadow: none;
        }

        .cinema-seat:hover:not(.seat-occupied):not(.seat-selected) {
            transform: scale(1.15) translateY(-5px);
            filter: drop-shadow(0 8px 20px rgba(0,0,0,0.6));
        }

        .seat-selected:hover {
            transform: scale(1.25) translateY(-10px);
            filter: drop-shadow(0 0 20px rgba(34, 197, 94, 0.8));
        }

        .dark .cinema-seat:hover:not(.seat-occupied) {
            filter: drop-shadow(0 8px 20px rgba(0,0,0,0.6));
        }

        .seat-available .seat-back,
        .seat-available .seat-cushion,
        .seat-available .seat-arm,
        .seat-available .seat-base {
            fill: #000000 !important;
            stroke: none !important;
        }

        .dark .seat-available .seat-back,
        .dark .seat-available .seat-cushion,
        .dark .seat-available .seat-arm,
        .dark .seat-available .seat-base {
            fill: #1f2937 !important;
            stroke: none !important;
        }

        .seat-selected .seat-back,
        .seat-selected .seat-cushion,
        .seat-selected .seat-arm,
        .seat-selected .seat-base {
            fill: #22c55e !important;
            stroke: none !important;
        }

        .seat-occupied .seat-back,
        .seat-occupied .seat-cushion,
        .seat-occupied .seat-arm,
        .seat-occupied .seat-base {
            fill: #dc2626 !important;
            stroke: none !important;
        }

        .seat-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2px;
            margin-bottom: 4px;
        }

        .row-label {
            background: linear-gradient(135deg, rgba(164, 0, 0, 0.2) 0%, rgba(164, 0, 0, 0.1) 100%);
            color: #374151;
            border-radius: 20px;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 0.8rem;
            min-width: 35px;
            text-align: center;
            border: 1px solid rgba(164, 0, 0, 0.2);
            margin-right: 5px;
            z-index: 10;
        }

        .dark .row-label {
            background: linear-gradient(135deg, rgba(164, 0, 0, 0.4) 0%, rgba(164, 0, 0, 0.2) 100%);
            color: white;
            border: 1px solid rgba(164, 0, 0, 0.3);
        }

        .aisle {
            width: 10px;
            text-align: center;
            color: rgba(55, 65, 81, 0.4);
            font-size: 0.65rem;
            font-weight: bold;
            letter-spacing: 0;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
        }

        .dark .aisle {
            color: rgba(255,255,255,0.3);
        }

        .cinema-screen {
            text-align: center;
            margin: 2rem 0;
            padding: 1rem;
            background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.05) 100%);
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,0.2);
            color: #1f2937;
        }

        .dark .cinema-screen {
            background: linear-gradient(180deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
        }

        .sticky-cart {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 40;
            padding: 1rem;
            border-top: 1px solid rgba(164, 0, 0, 0.2);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.95);
        }

        .dark .sticky-cart {
            background: rgba(0, 0, 0, 0.85);
            border-top: 1px solid rgba(164, 0, 0, 0.3);
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.5);
        }

        .warning-box {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #d97706;
            padding: 15px;
            margin-bottom: 24px;
            border-radius: 12px;
            display: none;
        }

        .dark .warning-box {
            background: rgba(245, 158, 11, 0.2);
            border: 1px solid rgba(245, 158, 11, 0.5);
            color: #f59e0b;
        }

        .error-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #dc2626;
            padding: 15px;
            margin-bottom: 24px;
            border-radius: 12px;
        }

        .dark .error-box {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.5);
            color: #ef4444;
        }

        @media (min-width: 1024px) {
            .cinema-seat {
                width: 60px;
                height: 70px;
            }
            .seat-row {
                gap: 4px;
                margin-bottom: 6px;
            }
.aisle {
    width: 50px;
    writing-mode: horizontal-tb;
    transform: none;
    font-size: 0.75rem;
    letter-spacing: 1px;
}
            .row-label {
                padding: 8px 14px;
                font-size: 0.9rem;
                min-width: 45px;
                margin-right: 0;
            }
        }
    </style>
</head>
<body class="min-h-screen font-sans">
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('color-theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    });
</script>

    <div class="absolute inset-0 z-0">
        <img src="https://i.pinimg.com/1200x/f0/b0/c3/f0b0c339e09dfaa74f7c8f68b94a5ce3.jpg"
             alt="Cinema Background"
             class="w-full h-full object-cover opacity-30 dark:opacity-60"
             style="mask-image: linear-gradient(to bottom, black 0%, black 60%, transparent 100%); -webkit-mask-image: linear-gradient(to bottom, black 0%, black 60%, transparent 100%);">
        <div class="absolute inset-0 bg-gradient-to-br from-white/50 via-white/30 to-white/50 dark:from-black/70 dark:via-black/50 dark:to-black/70"></div>
    </div>

    <nav class="sticky top-0 z-50 shadow-2xl border-b border-gray-300 bg-white/90 backdrop-blur-md dark:bg-darkbg/95 dark:border-primary-700/50 dark:shadow-none">
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

    <main class="relative z-10 py-8 mb-28">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="bg-white dark:bg-black rounded-xl p-8 shadow-2xl transition-shadow status-card-glow hover:scale-[1.005] mb-8">
                <div class="text-center">
                    <h1 class="text-4xl heading text-gray-900 dark:text-white mb-4">Pick Your Spot</h1>
                    <p class="text-gray-600 dark:text-white/70 form-text text-lg">
                        Hey <?php echo htmlspecialchars(explode('@', $_SESSION['email_address'] ?? 'Guest')[0]); ?>, ready for the show?
                    </p>
                    <h2 class="text-2xl heading text-primary-500 mt-4">"<?php echo htmlspecialchars($movie['movie_title']); ?>"</h2>
                    <p class="text-gray-500 dark:text-white/60 form-text mt-2">
                        <b>Cinema:</b> <?php echo htmlspecialchars($cinema['location']); ?> |
                        <b>Experience:</b> <?php echo htmlspecialchars($cinema['experience']); ?><br>
                        <b>Date:</b> <?php echo htmlspecialchars($cinema['date']); ?> |
                        <b>Showtime:</b> <?php echo htmlspecialchars($cinema['showtime']); ?>
                    </p>
                </div>
            </div>

            <?php if(isset($error_message)): ?>
                <div class="error-box">
                    <strong><i class="fas fa-exclamation-circle mr-2"></i>Whoops!</strong> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <div id="gap-warning" class="warning-box">
                <strong><i class="fas fa-exclamation-triangle mr-2"></i>Hold up!</strong> <span id="gap-message"></span>
            </div>

            <div class="glass-container rounded-2xl p-4 md:p-8">
                <svg width="0" height="0" style="position:absolute">
                    <symbol id="cinema-seat-shape" viewBox="0 0 60 70">
                        <path class="seat-base" d="M 5 65 C 5 60 10 55 15 50 L 45 50 C 50 55 55 60 55 65 L 5 65 Z" rx="5" ry="5" stroke="none"/>
                        <rect class="seat-back" x="10" y="5" width="40" height="45" rx="5" stroke="none"/>
                        <path class="seat-cushion" d="M 12 50 C 12 40 48 40 48 50 L 48 55 C 48 58 45 60 42 60 L 18 60 C 15 60 12 58 12 55 L 12 50 Z" stroke="none"/>
                        <path class="seat-arm" d="M 5 50 L 5 20 C 5 15 10 10 15 10 L 15 55 L 5 50 Z" stroke="none"/>
                        <path class="seat-arm" d="M 55 50 L 55 20 C 55 15 50 10 45 10 L 45 55 L 55 50 Z" stroke="none"/>
                    </symbol>
                </svg>

                                <div class="pricing-section">
                    <h3 class="text-3xl heading text-gray-900 dark:text-white mb-6 text-left">Seat Classes & Pricing</h3>
                    <div class="flex flex-col md:flex-row justify-center gap-6 max-w-4xl mx-auto">
                        <div class="flex-1 text-center p-8 rounded-2xl bg-white/80 dark:bg-black/80 backdrop-blur-md border border-amber-500/30 shadow-2xl shadow-amber-500/20">
                            <div class="text-amber-600 dark:text-amber-400 font-bold text-xl mb-3">VIP</div>
                            <div class="text-amber-700 dark:text-amber-300 font-black text-5xl mb-3">RM35</div>
                            <div class="text-amber-600/80 dark:text-amber-400/80 text-lg font-medium">Rows A-C</div>
                        </div>
                        <div class="flex-1 text-center p-8 rounded-2xl bg-white/80 dark:bg-black/80 backdrop-blur-md border border-blue-500/30 shadow-2xl shadow-blue-500/20">
                            <div class="text-blue-600 dark:text-blue-400 font-bold text-xl mb-3">Premium</div>
                            <div class="text-blue-700 dark:text-blue-300 font-black text-5xl mb-3">RM25</div>
                            <div class="text-blue-600/80 dark:text-blue-400/80 text-lg font-medium">Rows D-F</div>
                        </div>
                        <div class="flex-1 text-center p-8 rounded-2xl bg-white/80 dark:bg-black/80 backdrop-blur-md border border-emerald-500/30 shadow-2xl shadow-emerald-500/20">
                            <div class="text-emerald-600 dark:text-emerald-400 font-bold text-xl mb-3">Standard</div>
                            <div class="text-emerald-700 dark:text-emerald-300 font-black text-5xl mb-3">RM15</div>
                            <div class="text-emerald-600/80 dark:text-emerald-400/80 text-lg font-medium">Rows G-J</div>
                        </div>
                    </div>
                    <div class="seating-notice text-center text-gray-900 dark:text-white heading text-lg mt-8">
                        Please select consecutive seats in the same row
                    </div>
                </div>
                <div class="flex flex-wrap justify-center gap-6 md:gap-10 mb-8">
                    <div class="flex items-center space-x-3">
                        <svg class="cinema-seat seat-available" viewBox="0 0 60 70" width="45" height="55">
                            <path class="seat-base" d="M 5 65 C 5 60 10 55 15 50 L 45 50 C 50 55 55 60 55 65 L 5 65 Z" rx="5" ry="5"/>
                            <rect class="seat-back" x="10" y="5" width="40" height="45" rx="5"/>
                            <path class="seat-cushion" d="M 12 50 C 12 40 48 40 48 50 L 48 55 C 48 58 45 60 42 60 L 18 60 C 15 60 12 58 12 55 L 12 50 Z"/>
                            <path class="seat-arm" d="M 5 50 L 5 20 C 5 15 10 10 15 10 L 15 55 L 5 50 Z" />
                            <path class="seat-arm" d="M 55 50 L 55 20 C 55 15 50 10 45 10 L 45 55 L 55 50 Z" />
                        </svg>
                        <span class="form-text text-sm md:text-lg text-gray-900 dark:text-white">Available</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="cinema-seat seat-selected" viewBox="0 0 60 70" width="45" height="55">
                            <path class="seat-base" d="M 5 65 C 5 60 10 55 15 50 L 45 50 C 50 55 55 60 55 65 L 5 65 Z" rx="5" ry="5"/>
                            <rect class="seat-back" x="10" y="5" width="40" height="45" rx="5"/>
                            <path class="seat-cushion" d="M 12 50 C 12 40 48 40 48 50 L 48 55 C 48 58 45 60 42 60 L 18 60 C 15 60 12 58 12 55 L 12 50 Z"/>
                            <path class="seat-arm" d="M 5 50 L 5 20 C 5 15 10 10 15 10 L 15 55 L 5 50 Z" />
                            <path class="seat-arm" d="M 55 50 L 55 20 C 55 15 50 10 45 10 L 45 55 L 55 50 Z" />
                        </svg>
                        <span class="form-text text-sm md:text-lg text-gray-900 dark:text-white">Selected</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="cinema-seat seat-occupied" viewBox="0 0 60 70" width="45" height="55">
                            <path class="seat-base" d="M 5 65 C 5 60 10 55 15 50 L 45 50 C 50 55 55 60 55 65 L 5 65 Z" rx="5" ry="5"/>
                            <rect class="seat-back" x="10" y="5" width="40" height="45" rx="5"/>
                            <path class="seat-cushion" d="M 12 50 C 12 40 48 40 48 50 L 48 55 C 48 58 45 60 42 60 L 18 60 C 15 60 12 58 12 55 L 12 50 Z"/>
                            <path class="seat-arm" d="M 5 50 L 5 20 C 5 15 10 10 15 10 L 15 55 L 5 50 Z" />
                            <path class="seat-arm" d="M 55 50 L 55 20 C 55 15 50 10 45 10 L 45 55 L 55 50 Z" />
                        </svg>
                        <span class="form-text text-sm md:text-lg text-gray-900 dark:text-white">Occupied</span>
                    </div>
                </div>

                <div class="cinema-screen mb-10 md:mb-14">
                    <h4 class="text-xl md:text-3xl heading text-gray-900 dark:text-white font-black tracking-wider">SCREEN</h4>
                </div>

                <form method="GET" id="seat-form">
                    <input type="hidden" name="proceed" value="1">
                    <input type="hidden" name="cinema_id" value="<?php echo $cinema_id; ?>">
                    <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">

                    <div class="seat-map-container">
                        <div class="seat-row-view flex flex-col items-center space-y-4 md:space-y-6 overflow-x-auto pb-4">
                            <?php foreach($seats_by_row as $row_letter => $row_seats): ?>
                                <div class="seat-row flex-nowrap"> 
                                    <div class="row-label"><?php echo $row_letter; ?></div>
                                    <?php
                                    $seat_count = 0;
                                    $total_seats = count($row_seats);
                                    foreach($row_seats as $seat):
                                        $seat_count++;
                                        if ($seat_count == 5 || ($seat_count == 9 && $total_seats > 12)): ?>
                                            <div class="aisle">AISLE</div>
                                        <?php endif; ?>

                                        <div class="relative flex-shrink-0">
                                            <?php
                                                $seat_data = json_encode([
                                                    'id' => $seat['seat_id'],
                                                    'number' => $seat['seat_number'],
                                                    'row' => $row_letter,
                                                    'type' => $seat['seat_type'],
                                                    'price' => $seat['price']
                                                ]);
                                                
                                                $is_available = isset($seat['is_available']) ? $seat['is_available'] : 1;
                                                $seat_class = $is_available ? 'seat-available' : 'seat-occupied';
                                            ?>
                                            
                                            <?php if($is_available): ?>
                                                <input type="checkbox"
                                                        name="selected_seats[]"
                                                        value="<?php echo $seat['seat_id']; ?>"
                                                        id="seat_<?php echo $seat['seat_id']; ?>"
                                                        data-seat='<?php echo htmlspecialchars($seat_data, ENT_QUOTES, 'UTF-8'); ?>'
                                                        style="display: none;"
                                                        onchange="handleSeatToggle(this)">
                                                <label for="seat_<?php echo $seat['seat_id']; ?>" class="cursor-pointer block">
                                                    <svg class="cinema-seat <?php echo $seat_class; ?> <?php echo $seat['seat_type'] == 'VIP' ? 'seat-vip' : ($seat['seat_type'] == 'Premium' ? 'seat-premium' : ''); ?>"
                                                        viewBox="0 0 60 70"
                                                        id="svg_<?php echo $seat['seat_id']; ?>"
                                                        width="45" 
                                                        height="55">
                                                        <path class="seat-base" d="M 5 65 C 5 60 10 55 15 50 L 45 50 C 50 55 55 60 55 65 L 5 65 Z" rx="5" ry="5"/>
                                                        <rect class="seat-back" x="10" y="5" width="40" height="45" rx="5"/>
                                                        <path class="seat-cushion" d="M 12 50 C 12 40 48 40 48 50 L 48 55 C 48 58 45 60 42 60 L 18 60 C 15 60 12 58 12 55 L 12 50 Z"/>
                                                        <path class="seat-arm" d="M 5 50 L 5 20 C 5 15 10 10 15 10 L 15 55 L 5 50 Z" />
                                                        <path class="seat-arm" d="M 55 50 L 55 20 C 55 15 50 10 45 10 L 45 55 L 55 50 Z" />
                                                    </svg>
                                                    <div class="absolute inset-0 flex items-center justify-center seat-number-label text-[10px] md:text-[14px] font-black text-white">
                                                        <?php echo substr($seat['seat_number'], 1); ?>
                                                    </div>
                                                </label>
                                            <?php else: ?>
                                                <div class="relative flex-shrink-0" title="Seat Taken">
                                                    <svg class="cinema-seat <?php echo $seat_class; ?> <?php echo $seat['seat_type'] == 'VIP' ? 'seat-vip' : ($seat['seat_type'] == 'Premium' ? 'seat-premium' : ''); ?>" 
                                                          viewBox="0 0 60 70"
                                                          width="45" 
                                                          height="55">
                                                        <path class="seat-base" d="M 5 65 C 5 60 10 55 15 50 L 45 50 C 50 55 55 60 55 65 L 5 65 Z" rx="5" ry="5"/>
                                                        <rect class="seat-back" x="10" y="5" width="40" height="45" rx="5"/>
                                                        <path class="seat-cushion" d="M 12 50 C 12 40 48 40 48 50 L 48 55 C 48 58 45 60 42 60 L 18 60 C 15 60 12 58 12 55 L 12 50 Z"/>
                                                        <path class="seat-arm" d="M 5 50 L 5 20 C 5 15 10 10 15 10 L 15 55 L 5 50 Z" />
                                                        <path class="seat-arm" d="M 55 50 L 55 20 C 55 15 50 10 45 10 L 45 55 L 55 50 Z" />
                                                    </svg>
                                                    <div class="absolute inset-0 flex items-center justify-center seat-number-label text-[10px] md:text-[14px] font-black text-white">
                                                        <?php echo substr($seat['seat_number'], 1); ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <div class="sticky-cart">
        <div class="max-w-6xl mx-auto flex justify-between items-center w-full px-2 md:px-0">
            <div class="flex flex-col md:flex-row md:items-center space-y-1 md:space-y-0 md:space-x-8">
                <div class="text-sm md:text-lg font-medium text-gray-900 dark:text-white">
                    <i class="fas fa-ticket-alt text-primary-500 mr-2"></i>
                    Seats: <span id="selected-count" class="font-bold text-lg md:text-2xl">0</span>
                </div>
                <div class="text-sm md:text-lg font-medium text-gray-900 dark:text-white">
                    <i class="fas fa-couch text-primary-500 mr-2"></i>
                    <span id="selected-names" class="text-xs md:text-base font-normal italic max-w-[200px] inline-block truncate md:max-w-full opacity-70">
                        (Select your seats)
                    </span>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-xl md:text-3xl font-extrabold text-primary-500 heading">
                    Total: <span id="total-price" class="text-gray-900 dark:text-white">RM0</span>
                </div>
                <button type="submit" form="seat-form" id="proceed-btn" class="select-btn px-6 py-3 rounded-xl uppercase text-lg" disabled>
                    Proceed to Payment <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        const seatForm = document.getElementById('seat-form');
        const proceedBtn = document.getElementById('proceed-btn');
        const selectedCountSpan = document.getElementById('selected-count');
        const selectedNamesSpan = document.getElementById('selected-names');
        const totalPriceSpan = document.getElementById('total-price');
        const gapWarningBox = document.getElementById('gap-warning');
        const gapMessageSpan = document.getElementById('gap-message');
        const allAvailableSeats = Array.from(document.querySelectorAll('input[name="selected_seats[]"]'));

        const seatsData = {};
        allAvailableSeats.forEach(input => {
            try {
                const data = JSON.parse(input.getAttribute('data-seat'));
                seatsData[data.id] = data;
                if (input.checked) {
                    const svg = document.getElementById(`svg_${data.id}`);
                    if (svg) {
                        svg.classList.remove('seat-available');
                        svg.classList.add('seat-selected');
                    }
                }
            } catch (e) {
                console.error("Error parsing seat data:", e);
            }
        });

        function checkConsecutive(selectedSeats) {
            if (selectedSeats.length === 0) return { valid: true, message: "" };

            const seatsByRow = selectedSeats.reduce((acc, seatId) => {
                const seat = seatsData[seatId];
                if (!acc[seat.row]) acc[seat.row] = [];
                acc[seat.row].push(parseInt(seat.number.substring(1)));
                return acc;
            }, {});

            let allValid = true;
            let firstInvalidRow = null;

            for (const row in seatsByRow) {
                const seatNumbers = seatsByRow[row].sort((a, b) => a - b);
                
                if (seatNumbers.length > 0) {
                    let isConsecutive = true;

                    for (let i = 1; i < seatNumbers.length; i++) {
                        if (seatNumbers[i] !== seatNumbers[i-1] + 1) {
                            isConsecutive = false;
                            break;
                        }
                    }

                    if (!isConsecutive) {
                        allValid = false;
                        firstInvalidRow = row;
                        break;
                    }
                }
            }
            
            if (!allValid) {
                return { valid: false, message: `The selected seats in **Row ${firstInvalidRow}** are not consecutive. Please select seats next to each other.` };
            }

            const rows = Object.keys(seatsByRow);
            if (rows.length > 1) {
                return { valid: false, message: `You have selected seats from multiple rows: **${rows.join(', ')}**. Please select seats from only one row.` };
            }

            return { valid: true, message: "" };
        }

        function updateCart() {
            const selectedInputs = Array.from(document.querySelectorAll('input[name="selected_seats[]"]:checked'));
            const selectedIds = selectedInputs.map(input => input.value);
            const selectedSeatsInfo = selectedIds
                .map(id => seatsData[id])
                .sort((a, b) => a.row.localeCompare(b.row) || (parseInt(a.number.substring(1)) - parseInt(b.number.substring(1))));

            const validation = checkConsecutive(selectedIds);
            
            if (validation.valid) {
                gapWarningBox.style.display = 'none';
            } else {
                gapMessageSpan.innerHTML = validation.message.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                gapWarningBox.style.display = 'block';
            }

            const totalCount = selectedSeatsInfo.length;
            const totalPrice = selectedSeatsInfo.reduce((sum, seat) => sum + parseFloat(seat.price), 0);
            const namesList = totalCount > 0 ? selectedSeatsInfo.map(s => `${s.row}${s.number.substring(1)}`).join(', ') : '(Select your seats)';

            selectedCountSpan.textContent = totalCount;
            selectedNamesSpan.textContent = namesList;
            totalPriceSpan.textContent = `RM${totalPrice.toFixed(2)}`;

            proceedBtn.disabled = totalCount === 0 || !validation.valid;
        }

        function handleSeatToggle(checkbox) {
            const seatId = checkbox.value;
            const svgElement = document.getElementById(`svg_${seatId}`);

            svgElement.classList.remove('seat-available', 'seat-selected', 'seat-occupied');

            if (checkbox.checked) {
                svgElement.classList.add('seat-selected');
                svgElement.style.transform = 'scale(1.25) translateY(-10px)';
                svgElement.style.filter = 'drop-shadow(0 0 20px rgba(34, 197, 94, 0.8))';
            } else {
                svgElement.classList.add('seat-available');
                svgElement.style.transform = 'none';
                svgElement.style.filter = '';
            }

            updateCart();
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateCart();
            
            const themeToggleBtn = document.getElementById('theme-toggle');
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', () => {
                    const html = document.documentElement;
                    const isDark = html.classList.contains('dark');
                    
                    if (isDark) {
                        html.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                    } else {
                        html.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                    }
                });
            }
        });
    </script>
</body>
</html>
