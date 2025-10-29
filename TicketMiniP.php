<?php
session_start();
require 'ConnMiniP.php';

if ($_SESSION['email_address']) {

} else {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
    exit();
}

if ($_GET['booking_id']) {
    $booking_id = $_GET['booking_id'];
} else {
    echo "No booking found";
    exit();
}

$email = $_SESSION['email_address'];
$user_sql = "SELECT * FROM users WHERE email_address = '$email'";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();
$user_id = $user['user_id'];

// Fetch booking details
$booking_sql = "SELECT b.*, cs.*, m.movie_title, m.genre, m.pg_rating, m.duration 
                FROM booking b 
                JOIN cinema_selection cs ON b.cinema_id = cs.cinema_id 
                JOIN movies m ON cs.movie_id = m.movie_id 
                WHERE b.booking_id = '$booking_id'";
$result = $conn->query($booking_sql);
$booking = $result->fetch_assoc();

$tickets_sql = "SELECT t.*, s.seat_number 
                FROM ticket t 
                JOIN seat_selection s ON t.Seat_ID = s.seat_id 
                WHERE t.Booking_ID = '$booking_id'";
$tickets_result = $conn->query($tickets_sql);
$tickets = $tickets_result->fetch_all(MYSQLI_ASSOC);
?>

<html>
<head>
    <title>Ticket Page</title>
</head>
<body>
    <h2>E-Ticket</h2>
    <p>Welcome <?php echo $_SESSION['email_address']; ?>! | <a href="LogoutMiniP.php">Logout</a></p>

    <h3>Booking Confirmed!</h3>

    <p><b>Movie Details</b></p>
    <p>Movie: <?php echo $booking['movie_title']; ?></p>
    <p>Genre: <?php echo $booking['genre']; ?></p>
    <p>Rating: <?php echo $booking['pg_rating']; ?></p>
    <p>Duration: <?php echo $booking['duration']; ?></p>

    <p><b>Cinema Details</b></p>
    <p>Location: <?php echo $booking['location']; ?></p>
    <p>Date: <?php echo $booking['date']; ?></p>
    <p>Time: <?php echo $booking['showtime']; ?></p>
    <p>Experience: <?php echo $booking['experience']; ?></p>

    <p><b>Tickets</b></p>
    <?php foreach($tickets as $ticket): ?>
        <p>Ticket <?php echo $ticket['Ticket_ID']; ?> - Seat <?php echo $ticket['seat_number']; ?> - <?php echo $ticket['Ticket_Type']; ?> - RM<?php echo $ticket['Ticket_Price']; ?></p>
    <?php endforeach; ?>

    <p><b>Booking Reference:</b> <?php echo $booking_id; ?></p>

    <p><a href="IndexMiniP.php">Book Another Movie</a></p>
</body>
</html>

