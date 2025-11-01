<?php
session_start();
require 'ConnMiniP.php';

// Check if user is logged in
if (isset($_SESSION['email_address'])) {

} else {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
    exit();
}

// Check if movie ID is set in session //sir rassa get method better
if (isset($_GET['movie_id'])) {
    $movie_id = $_GET['movie_id'];
    $_movie = GetMovieByID($movie_id);
} else {
    // If not set, redirect back to movie selection
    header("Location: IndexMiniP.php");
    exit();
}

$movie_id = $_GET['movie_id'];

// Get movie details
$movie = GetMovieByID($movie_id);

// Get all cinema data for this movie
$cinema = GetCinemasByMovieID($movie_id);
?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ZTAVerse | Cinema Selection</title>
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

/* HERO SECTION */
.hero {
  position: relative;
  height: 85vh;
  width: 100%;
  background: url('<?php echo $movie['image_path']; ?>') center/contain no-repeat;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  overflow: hidden;
  margin-top: 80px; /* push hero down so navbar doesn't overlap */
}

.hero::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.9), rgba(255,0,0,0.1));
}

.hero-content {
  position: relative;
  z-index: 2;
  text-align: center;
  padding-bottom: 60px;
}

.hero h1 {
  font-family: 'Manrope', sans-serif;
  font-size: 3rem;
  color: var(--accent-color);
  text-shadow: 0 0 20px #ff0000, 0 0 40px #fff;
}

.hero p {
  color: #ccc;
  margin-top: 12px;
  text-shadow: 0 0 8px #ff1a1a;
}

/* CINEMA GRID */
.cinema-card {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 0 10px rgba(255,0,0,0.3);
  transition: all 0.3s ease;
  color: var(--text-color);
}

.light-mode .cinema-card {
  color: #330000;
}

.cinema-card:hover {
  transform: scale(1.05);
  box-shadow: 0 0 25px var(--accent-color), 0 0 35px rgba(255,77,77,0.3);
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

/* BACK BUTTON IN NAVBAR */
.back-btn-navbar {
  padding: 8px 16px;
  border: 1px solid var(--accent-color);
  border-radius: 10px;
  font-weight: 600;
  transition: 0.3s;
}

/* LIGHT/DARK MODE */
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
.toggle-btn:hover { background: var(--accent-color); }
.light-mode .toggle-btn { background: #ddd; color: #000; }

  </style>
</head>

<body>
  <!-- NAVBAR -->
  <div class="navbar">
    <h1>ZTAVerse</h1>
    <div class="flex items-center">
      <a href="IndexMiniP.php" class="back-btn-navbar">← Back to Movies</a>
      <a href="LogoutMiniP.php" class="ml-4 underline underline-offset-4 hover:text-red-500 transition-all duration-300">Logout</a>
      <button id="modeToggle" class="toggle-btn ml-4">☀ Light Mode</button>
    </div>
  </div>

  <!-- HERO SECTION -->
  <div class="hero">
    <div class="hero-content">
      <h1><?php echo $movie['movie_title']; ?></h1>
      <p><?php echo $movie['pg_rating']; ?> | <?php echo $movie['genre']; ?> | <?php echo $movie['duration']; ?></p>
    </div>
  </div>

  <!-- CINEMA SELECTION -->
  <div class="px-10 py-16 text-center">
    <h2 class="text-3xl font-bold mb-10">Select Your Cinema</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 justify-center max-w-6xl mx-auto">
      <?php foreach($cinema as $cin): ?>
        <div class="cinema-card">
          <h3 class="text-xl font-bold mb-2 text-red-500"><?php echo $cin['location']; ?></h3>
          <p class="mb-1"><?php echo $cin['experience']; ?></p>
          <p class="mb-1">📅 <?php echo $cin['date']; ?></p>
          <p class="mb-4">🕒 <?php echo $cin['showtime']; ?></p>
          <form action="SeatsMiniP.php" method="GET">
            <input type="hidden" name="cinema_id" value="<?php echo $cin['cinema_id']; ?>">
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
