<?php
session_start();
require 'ConnMiniP.php';

// Check if user is logged in
if (!isset($_SESSION['email_address'])) {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
    exit();
}

// Check if required session data exists
if (!isset($_SESSION['selected_seats']) || !isset($_SESSION['cinema_id']) || !isset($_SESSION['movie_id'])) {
    header("Location: IndexMiniP.php");
    exit();
}

// Get session data
$selected_seat_ids = $_SESSION['selected_seats'];
$cinema_id = $_SESSION['cinema_id'];
$movie_id = $_SESSION['movie_id'];
$user_id = $_SESSION['user_id'];

// Get movie details
$movie = GetMovieByID($movie_id);

// Get cinema details
$cinema = GetCinemaByID($cinema_id);

// Get selected seats details
$selected_seats_details = [];
$total_price = 0;

foreach($selected_seat_ids as $seat_id) {
    $seat = GetSeatByID($seat_id);
    if($seat) {
        $selected_seats_details[] = $seat;
        $total_price += $seat['price'];
    }
}

// Handle confirmation submission
if(isset($_GET['confirm_booking'])) {
    // Insert booking record
    $booking_id = CreateBooking($user_id, $cinema_id);
    
    if($booking_id) {
        // Mark seats as occupied
        foreach($selected_seat_ids as $seat_id) {
            UpdateSeatAvailability($seat_id, 0); // 0 = not available
        }
        
        // Store booking_id and total_price in session for payment page
        $_SESSION['booking_id'] = $booking_id;
        $_SESSION['total_price'] = $total_price;
        
        // Redirect to payment page
        header("Location: PaymentMiniP.php");
        exit();
    }
}
?>

<html>
<head>
    <title>Booking Confirmation</title>
</head>
<body>
    <h2>Booking Confirmation</h2>
    <p>Welcome <?php echo $_SESSION['email_address']; ?>! | <a href="LogoutMiniP.php">Logout</a></p>

    <h3>Confirm Your Booking</h3>

    <p><b>Movie:</b> <?php echo $movie['movie_title']; ?></p>
    <p><b>Cinema:</b> <?php echo $cinema['location']; ?> | <b>Experience:</b> <?php echo $cinema['experience']; ?></p>
    <p><b>Date:</b> <?php echo $cinema['date']; ?> | <b>Showtime:</b> <?php echo $cinema['showtime']; ?></p>

    <h3>Selected Seats:</h3>
    
    <?php foreach($selected_seats_details as $seat): ?>
        <p>
            Seat: <b><?php echo $seat['seat_number']; ?></b> | 
            Type: <b><?php echo $seat['seat_type']; ?></b> | 
            Price: <b>RM <?php echo number_format($seat['price'], 2); ?></b>
        </p>
    <?php endforeach; ?>

    <h3>Total Amount: RM <?php echo number_format($total_price, 2); ?></h3>

    <form method="GET">
        <button type="submit" name="confirm_booking">Confirm & Proceed to Payment</button>
    </form>

    <form action="SeatsMiniP.php" method="GET" style="display: inline;">
        <input type="hidden" name="cinema_id" value="<?php echo $cinema_id; ?>">
        <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
        <button type="submit">Back to Seat Selection</button>
    </form>
</body>
</html>
