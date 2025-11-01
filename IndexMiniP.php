<?php
session_start();
require 'ConnMiniP.php';

// Fetch all movies
$movies = GetAllMovies();

// Check if user is logged in
if (isset($_SESSION['email_address'])) {

} else {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
}

// Get user info
$email = $_SESSION['email_address'];
$sql_user = "SELECT * FROM users WHERE email_address = '$email'";
$user_result = $conn->query($sql_user);
$user = $user_result->fetch_assoc();
$user_id = $user['user_id'];

// Check if user has existing booking
$sql_booking = "SELECT booking_id FROM booking WHERE user_id = '$user_id' ORDER BY booking_id DESC LIMIT 1";
$booking_result = $conn->query($sql_booking);

$has_booking = false;
$latest_booking_id = null;

if ($booking_result && $booking_result->num_rows > 0) {
    $has_booking = true;
    $latest_booking = $booking_result->fetch_assoc();
    $latest_booking_id = $latest_booking['booking_id'];
}

// Define the top movies by index (zero-based)
$top_indices = [1, 5, 3, 7];

$hero_movies = [];
foreach ($top_indices as $i) {
    if (isset($movies[$i])) {
        $hero_movies[] = $movies[$i];
    }
}
?>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ZTAVerse | Now Showing</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@700&family=Saira+Semi+Condensed:wght@400;700&display=swap" rel="stylesheet">

<style>
:root {
    --bg-color: linear-gradient(135deg, #0a0a0a, #1a0000, #330000);
    --text-color: #fff;
    --card-bg: rgba(20,20,20,0.85);
    --accent-color: #ff1a1a;
}

.light-mode {
    --bg-color: linear-gradient(135deg, #fff0f0, #ffeaea);
    --text-color: #111;
    --card-bg: linear-gradient(145deg, #ffcccc, #ff9999);
    --accent-color: #ff4d4d;
}

body {
    font-family: 'Saira Semi Condensed', sans-serif;
    background: var(--bg-color);
    color: var(--text-color);
    margin: 0;
    overflow-x: hidden;
    transition: background 0.5s, color 0.5s;
}

/* HERO SECTION - yg top selected movie*/
.hero {
    position: relative;
    height: 600px;
    width: 100%;
    margin-top: 80px;
    overflow: hidden;
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.3), rgba(0,0,0,0.05));
    z-index: 1;
}

.hero-carousel {
    display: flex;
    justify-content: center; /* center horizontally */
    align-items: center;     /* center vertically within padding */
    gap: 2rem;
    flex-wrap: wrap;         /* wrap if smaller screens */
    padding: 4rem 1rem;
}

.hero-carousel::-webkit-scrollbar {
    height: 10px;
}

.hero-carousel::-webkit-scrollbar-thumb {
    background-color: #ff1a1a;
    border-radius: 10px;
}

.hero-card {
    flex-shrink: 0;
    cursor: pointer;
    transition: transform 0.3s ease;
}
.hero-card img {
    border-radius: 1rem;
    object-fit: cover;
    width: 16rem;
    height: 24rem;
    box-shadow: 0 0 25px rgba(255, 0, 0, 0.5), 0 0 50px rgba(255, 77, 77, 0.3);
    transition: transform 0.3s, box-shadow 0.3s;
}

.hero-card img:hover {
    transform: scale(1.08);
    box-shadow: 0 0 35px rgba(255, 0, 0, 0.7), 0 0 60px rgba(255, 77, 77, 0.4);
}

.hero-card:hover {
    transform: scale(1.05);
}

/* Floating animation for posters */
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.animate-float {
    animation: float 4s ease-in-out infinite;
}

.hero-card:hover img {
    transform: scale(1.08) rotate(-2deg);
    box-shadow: 0 10px 30px rgba(255,0,0,0.5);
}

.hero-heading h2 {
    font-size: 4.5rem;
    color: #ff6666; /* bright red */
    text-shadow: 0 0 15px #ff6666, 0 0 30px #fff;
}

/* MOVIE GRID */
.movie-card {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 0 10px rgba(255,0,0,0.3);
    transition: all 0.3s ease;
}

.movie-card:hover {
    transform: scale(1.05);
    box-shadow: 0 0 25px var(--accent-color), 0 0 35px rgba(255,77,77,0.3);
}

.movie-card img {
    border-radius: 12px;
    width: 100%;
    height: 330px;
    object-fit: contain;
    background: #000;
}

/* BUTTONS */
.glow-btn {
    background: linear-gradient(90deg, #fff, #ff0000);
    color: black;
    font-weight: bold;
    padding: 10px 24px;
    border-radius: 12px;
    transition: all 0.3s ease;
    box-shadow: 0 0 15px rgba(255,255,255,0.4);
}

.glow-btn:hover {
    transform: scale(1.07);
    background: linear-gradient(90deg, #ff0000, #fff);
    box-shadow: 0 0 35px rgba(255,0,0,0.6);
}

/* NAVBAR */
.navbar {
    position: fixed;
    top: 0;
    width: 100%;
    background: linear-gradient(90deg, #220000, #440000);
    padding: 15px 60px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 10;
    backdrop-filter: blur(8px);
}

.navbar h1 {
    color: var(--accent-color);
    font-size: 2rem;
    font-weight: 900;
}

.navbar a, .back-btn-navbar {
    margin-left: 25px;
    color: #fff;
    transition: 0.3s;
    text-decoration: none;
}
.navbar a:hover, .back-btn-navbar:hover {
    color: #ff4d4d;
}

.toggle-btn {
    background: #222;
    color: #fff;
    border: none;
    border-radius: 20px;
    padding: 8px 16px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.4s;
}
.toggle-btn:hover { 
    background: var(--accent-color); 
}
.light-mode .toggle-btn { 
    background: #ddd; color: #000; 
}

</style>
</head>

<body>
<!-- NAVBAR -->
<div class="navbar">
    <h1>ZTAVerse</h1>
    <div class="flex items-center">
        <?php if ($has_booking): ?>
            <a href="TicketMiniP.php?booking_id=<?php echo $latest_booking_id; ?>" class="back-btn-navbar">🎟 View Ticket</a>
        <?php endif; ?>
        <a href="LogoutMiniP.php" class="ml-4 underline underline-offset-4 hover:text-red-500 transition-all duration-300">Logout</a>
        <button id="modeToggle" class="toggle-btn ml-4">☀ Light Mode</button>
    </div>
</div>

<div class="hero">
    <div class="hero-overlay"></div>

    <!-- Posters and heading -->
    <div class="hero-carousel-container relative z-2 flex flex-col items-center justify-center px-10 py-16">
        <!-- Heading centered above posters -->
         <h2 class="text-4xl font-extrabold text-red-400 drop-shadow-xl mb-8 text-center animate-pulse">
            Top Movies Selected Now
        </h2>

        <!-- Posters -->
        <div class="hero-carousel flex justify-center items-center gap-8 flex-wrap">
            <?php foreach ($hero_movies as $h_movie): ?>
                <a href="CinemaMiniP.php?movie_id=<?php echo $h_movie['movie_id']; ?>" class="hero-card">
                    <img src="<?php echo $h_movie['image_path']; ?>" alt="Movie Poster" 
                         class="rounded-xl object-cover w-64 h-96 shadow-2xl hover:scale-105 hover:rotate-2 transition-transform duration-500 animate-float">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- MOVIE GRID -->
<div class="px-10 py-16 text-center">
    <h2 class="text-3xl font-bold mb-10">Now Showing</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 justify-center max-w-6xl mx-auto">
        <?php foreach($movies as $movie): ?>
            <div class="movie-card">
                <img src="<?php echo $movie['image_path']; ?>" alt="<?php echo $movie['movie_title']; ?>">
                <h3 class="text-xl font-bold mb-2 text-red-500"><?php echo $movie['movie_title']; ?></h3>
                <p class="mb-1"><?php echo $movie['pg_rating'] . " | " . $movie['genre'] . " | " . $movie['duration']; ?></p>
                <form action="CinemaMiniP.php" method="GET">
                    <input type="hidden" name="movie_id" value="<?php echo $movie['movie_id']; ?>">
                    <button type="submit" class="glow-btn w-full">Select</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    <p class="text-gray-500 text-sm mt-12">© 2025 ZTAVerse Movie Booking System</p>
</div>

<!-- LIGHT/DARK MODE JS -->
<script>
const toggleBtn = document.getElementById('modeToggle');
toggleBtn.addEventListener('click', () => {
    document.body.classList.toggle('light-mode');
    toggleBtn.textContent = document.body.classList.contains('light-mode') ? '🌙 Dark Mode' : '☀ Light Mode';
});
</script>
</body>
</html>
