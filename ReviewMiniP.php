<?php
session_start();
require 'ConnMiniP.php';

// Ensure user is logged in
if (!isset($_SESSION['email_address'])) {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
    exit();
}

// Ensure booking_id exists
if (!isset($_GET['booking_id'])) {
    echo "No booking found.";
    exit();
}

$booking_id = $_GET['booking_id'];

// Get user info using function
$user = GetUserByEmail($_SESSION['email_address']);
$user_id = $user['user_id'];

// Get rating options using function
$ratings = GetAllRatings();

// If form submitted, process review
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Get movie info using function
    $movie = GetMovieByBookingID($booking_id);
    $movie_id = $movie['movie_id'];

    // Save review using function
    CreateReview($user_id,  $rating, $comment);

    // Redirect to ticket page
    header("Location: TicketMiniP.php?booking_id=$booking_id");
    exit();
}
?>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ZTAVerse | Review</title>
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
    transition: background 0.5s, color 0.5s;
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

.navbar a {
    margin-left: 25px;
    color: #fff;
    transition: 0.3s;
    text-decoration: none;
}

.navbar a:hover {
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

.toggle-btn:hover { background: var(--accent-color); }
.light-mode .toggle-btn { background: #ddd; color: #000; }

/* FORM CARD */
.review-card {
    background: var(--card-bg);
    max-width: 600px;
    margin: 120px auto 60px;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 0 20px rgba(255,0,0,0.3);
}

.review-card h2 {
    color: var(--accent-color);
    font-size: 2rem;
    margin-bottom: 1rem;
    text-align: center;
}

.review-card form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.review-card select, .review-card textarea {
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.2);
    padding: 12px;
    background-color: rgba(255,255,255,0.08);
    color: var(--text-color);
    transition: background 0.3s, color 0.3s;
}

.light-mode .review-card select,
.light-mode .review-card textarea {
    background-color: rgba(255,255,255,0.9);
    color: #000;
    border: 1px solid #ccc;
}


.review-card textarea {
    resize: none;
}

.review-card button {
    background: linear-gradient(90deg, #ff0000, #ff4d4d);
    color: white;
    font-weight: bold;
    padding: 10px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: 0.3s;
}

.review-card button:hover {
    transform: scale(1.05);
    background: linear-gradient(90deg, #fff, #ff0000);
    color: black;
}

.view-ticket-btn {
    display: block;
    margin: 20px auto 0;
    text-align: center;
    background: linear-gradient(90deg, #fff, #ff0000);
    color: black;
    padding: 10px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}

.view-ticket-btn:hover {
    transform: scale(1.05);
    background: linear-gradient(90deg, #ff0000, #fff);
}

select option {
    background-color: #fff;
    color: #000;
}

.dark-mode select option {
    background-color: #222;
    color: #fff;
}

</style>
</head>

<body>
<!-- NAVBAR -->
<div class="navbar">
    <h1>ZTAVerse</h1>
    <div class="flex items-center">
        <a href="TicketMiniP.php?booking_id=<?php echo $booking_id; ?>" class="back-btn-navbar">🎟 View Ticket</a>
        <a href="LogoutMiniP.php" class="ml-4 underline underline-offset-4 hover:text-red-500 transition-all duration-300">Logout</a>
        <button id="modeToggle" class="toggle-btn ml-4">☀ Light Mode</button>
    </div>
</div>

<!-- REVIEW FORM -->
<div class="review-card">
    <h2>Website Review</h2>
    <p class="text-center mb-4">Welcome <?php echo $_SESSION['email_address']; ?>!<br>How was your experience with our website?</p>

    <form method="POST">
        <label>Rating:</label>
        <select name="rating" required>
            <option value="">Select Rating</option>
            <?php foreach ($ratings as $rating_row): ?>
                <option value="<?php echo $rating_row['rating_value']; ?>">
                    <?php echo $rating_row['rating_label']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Comment:</label>
        <textarea name="comment" rows="4" cols="50" required></textarea>

        <button type="submit">Submit Review</button>
    </form>

    <a href="TicketMiniP.php?booking_id=<?php echo $booking_id; ?>" class="view-ticket-btn">View Ticket</a>
</div>

<!-- LIGHT/DARK MODE SCRIPT -->
<script>
const toggleBtn = document.getElementById('modeToggle');
toggleBtn.addEventListener('click', () => {
    document.body.classList.toggle('light-mode');
    toggleBtn.textContent = document.body.classList.contains('light-mode') ? '🌙 Dark Mode' : '☀ Light Mode';
});
</script>

</body>
</html>

