<?php
session_start();
require 'ConnMiniP.php';

// Check if user is logged in
if (!isset($_SESSION['email_address'])) {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
    exit();
}

// Get booking ID from GET
if (!isset($_GET['booking_id'])) {
    echo "No booking found";
    exit();
}
$booking_id = $_GET['booking_id'];

// Fetch user info
$user = GetUserByEmail($_SESSION['email_address']);
$user_id = $user['user_id'];

// Fetch booking details
$booking = GetBookingDetailsByID($booking_id);
if (!$booking) {
    echo "Booking not found!";
    exit();
}

// Fetch tickets
$tickets = GetTicketsByBookingID($booking_id);
?>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>E-Ticket | ZTAVerse</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Saira+Semi+Condensed:wght@400;700&display=swap" rel="stylesheet">

<style>
/* ===========================
   CSS - Styling for E-Ticket
=========================== */
:root {
    --bg-color: linear-gradient(135deg,#0a0a0a,#1a0000,#330000);
    --text-color: #fff;
    --card-bg: rgba(20,20,20,0.85);
    --accent-color: #ff1a1a;
}
.light-mode {
    --bg-color: linear-gradient(135deg,#fff0f0,#ffeaea);
    --text-color: #111;
    --card-bg: linear-gradient(145deg,#ffcccc,#ff9999);
    --accent-color: #ff4d4d;
}
body {
    font-family: 'Saira Semi Condensed', sans-serif;
    background: var(--bg-color);
    color: var(--text-color);
    margin: 0;
    transition: background 0.5s, color 0.5s;
}
/* ---------------- Navbar ---------------- */
.navbar {
    position: fixed;
    top: 0;
    width: 100%;
    background: linear-gradient(90deg,#220000,#440000);
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
    text-decoration: none;
    transition: 0.3s;
}
.navbar a:hover { color: #ff4d4d; }
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

/* ---------------- Ticket Card ---------------- */
.ticket-card {
    background: var(--card-bg);
    border-radius: 1rem;
    margin-bottom: 20px;
    box-shadow: 0 0 20px var(--accent-color);
    overflow: hidden;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    border: 2px dashed rgba(255,255,255,0.2);
    position: relative;
    transition: transform 0.3s;
}
.ticket-card:hover { transform: scale(1.02); }
.ticket-top, .ticket-bottom { padding: 15px 20px; }
.ticket-top h3 { color: var(--accent-color); font-size: 1.4rem; margin-bottom: 5px; }
.ticket-bottom { display: flex; justify-content: flex-start; align-items: center; border-top: 2px dashed rgba(255,255,255,0.2); }

/* ---------------- Buttons ---------------- */
.print-btn {
    background: #ff1a1a;
    color: #fff;
    font-weight: bold;
    padding: 10px 24px;
    border-radius: 12px;
    transition: all 0.3s ease;
}
.print-btn:hover { background: #ff4d4d; }
</style>
</head>

<body>
<!-- ================= Navbar ================= -->
<div class="navbar">
    <h1>ZTAVerse</h1>
    <div class="flex items-center">
        <a href="LogoutMiniP.php">Logout</a>
        <button id="modeToggle" class="toggle-btn ml-4">☀ Light Mode</button>
    </div>
</div>

<!-- ================= Ticket Content ================= -->
<div class="pt-28 px-6">
    <h2 class="text-3xl font-bold mb-6 text-center">🎫 E-Ticket</h2>
    <p class="text-center mb-8">Welcome <?php echo $_SESSION['email_address']; ?>!</p>

    <!-- Loop through tickets -->
    <?php foreach($tickets as $ticket): ?>
        <div class="ticket-card">
            <div class="ticket-top">
                <h3><?php echo $booking['movie_title']; ?></h3>
                <p><b>Genre:</b> <?php echo $booking['genre']; ?> | <b>Rating:</b> <?php echo $booking['pg_rating']; ?> | <b>Duration:</b> <?php echo $booking['duration']; ?></p>
                <p><b>Cinema:</b> <?php echo $booking['location']; ?> | <b>Date:</b> <?php echo $booking['date']; ?> | <b>Time:</b> <?php echo $booking['showtime']; ?></p>
                <p><b>Experience:</b> <?php echo $booking['experience']; ?></p>
            </div>
            <div class="ticket-bottom">
                <p>🎟 <?php echo $ticket['Ticket_Type']; ?> - Seat <?php echo $ticket['seat_number']; ?> - RM<?php echo $ticket['Ticket_Price']; ?></p>
            </div>
            <p class="text-center mt-2">Booking Ref: <b><?php echo $booking_id; ?></b></p>
        </div>
    <?php endforeach; ?>

    <div class="text-center mt-8 flex justify-center gap-4">
        <a href="IndexMiniP.php" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded transition">Book Another Movie</a>
        <button class="print-btn" onclick="openPrint()">🖨 Print Ticket</button>
    </div>
</div>

<!-- ================= Scripts ================= -->
<script>
const toggleBtn = document.getElementById('modeToggle');
toggleBtn.addEventListener('click', () => {
    document.body.classList.toggle('light-mode');
    toggleBtn.textContent = document.body.classList.contains('light-mode') ? '🌙 Dark Mode' : '☀ Light Mode';
});

// Open Print Ticket page
function openPrint() {
    const bookingId = "<?php echo $booking_id; ?>";
    window.open('PrintTicketMiniP.php?booking_id=' + bookingId, '_blank');
}
</script>

</body>
</html>


