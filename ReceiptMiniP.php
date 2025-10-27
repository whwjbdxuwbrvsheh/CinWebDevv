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

// Get latest cinema selection
$selection = GetUserLatestSelection($user_id);

// If no selection found or not paid
if (!$selection || $selection['payment_status'] != 'Paid') {
    header("Location: IndexMiniP.php");
    exit();
}

// Get movie details
$movie = GetMovieByID($selection['movie_id']);

// Prepare receipt data
$receipt_data = [
    'Booking Reference' => $selection['booking_ref'],
    'Payment Status' => $selection['payment_status'],
    'Payment Method' => $selection['payment_method'],
    'Movie' => $movie['movie_title'],
    'Cinema Location' => $selection['location'],
    'Date' => date('d F Y', strtotime($selection['date'])),
    'Showtime' => $selection['showtime'],
    'Experience' => $selection['experience'],
    'Seats' => $selection['seats'],
    'Total Paid' => 'RM ' . number_format($selection['total_price'], 2)
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt</title>
</head>
<body>
    <h2>✓ Payment Successful!</h2>
    <p>Welcome <?php echo $_SESSION['email_address']; ?>! | <a href="LogoutMiniP.php">Logout</a></p>
    
    <hr>
    
    <h3>Your Booking Receipt:</h3>
    
    <table border="1" cellpadding="10" cellspacing="0">
        <?php foreach($receipt_data as $label => $value): ?>
            <tr>
                <td><b><?php echo $label; ?>:</b></td>
                <td><?php echo $value; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <hr>
    
    <p><i>Please arrive at least 15 minutes before showtime.</i></p>
    <p><i>Present this booking reference at the cinema counter: <b><?php echo $selection['booking_ref']; ?></b></i></p>
    
    <br>
    
    <a href="IndexMiniP.php"><button type="button">Book Another Movie</button></a>
    <button type="button" onclick="window.print()">Print Receipt</button>
</body>
</html>