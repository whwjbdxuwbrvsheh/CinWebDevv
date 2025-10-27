<?php
session_start();
require 'ConnMiniP.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: LoginMiniP.php");
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'];

// Get latest cinema selection with seats
$selection = GetUserLatestSelection($user_id);

// If no selection found, redirect back
if (!$selection) {
    header("Location: IndexMiniP.php");
    exit();
}

// Check if seats were selected
if (empty($selection['seats'])) {
    header("Location: SelectSeats.php");
    exit();
}

// Get movie details
$movie = GetMovieByID($selection['movie_id']);

// Calculate total price based on experience type
$seats_array = explode(',', $selection['seats']);
$total_seats = count($seats_array);

if($selection['experience'] == '3D') {
    $price_per_seat = 20.00;
} else {
    $price_per_seat = 15.00;
}

$total_price = $total_seats * $price_per_seat;

// Generate booking reference if not exists
if(empty($selection['booking_ref'])) {
    $booking_ref = "BK" . strtoupper(substr(md5($selection['id'] . time()), 0, 8));
    // Save booking reference and total price to database
    UpdateBookingDetails($selection['id'], $booking_ref, $total_price);
} else {
    $booking_ref = $selection['booking_ref'];
}

// Handle confirmation submission
if(isset($_POST['confirm'])) {
    // Redirect to payment page
    header("Location: PaymentMiniP.php");
    exit();
}

// Prepare data for display using array
$booking_details = [
    'Booking Reference' => $booking_ref,
    'Movie' => $movie['movie_title'],
    'Genre' => $movie['genre'],
    'Rating' => $movie['pg_rating'],
    'Duration' => $movie['duration'],
    'Cinema Location' => $selection['location'],
    'Date' => date('d F Y', strtotime($selection['date'])),
    'Experience' => $selection['experience'],
    'Showtime' => $selection['showtime'],
    'Selected Seats' => $selection['seats'],
    'Number of Seats' => $total_seats,
    'Price per Seat' => 'RM ' . number_format($price_per_seat, 2),
    'Total Amount' => 'RM ' . number_format($total_price, 2)
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Confirm Booking</title>
</head>
<body>
    <h2>Confirm Your Booking</h2>
    <p>Welcome <?php echo $_SESSION['email_address']; ?>! | <a href="LogoutMiniP.php">Logout</a></p>
    
    <hr>
    
    <h3>Please review your booking details:</h3>
    
    <table border="1" cellpadding="10" cellspacing="0">
        <?php foreach($booking_details as $label => $value): ?>
            <tr>
                <td><b><?php echo $label; ?>:</b></td>
                <td><?php echo $value; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <hr>
    
    <p><b>Total to Pay: RM <?php echo number_format($total_price, 2); ?></b></p>
    
    <form method="POST">
        <button type="submit" name="confirm">Proceed to Payment</button>
        <a href="SelectSeats.php"><button type="button">Change Seats</button></a>
        <a href="IndexMiniP.php"><button type="button">Cancel</button></a>
    </form>
</body>
</html>