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
?>


<html>
<head>
    <title>Movie Selection</title>
</head>
<body>
    <h2>Movie Selection</h2>
    <p>Choose Your Movie</p>
    <p>Welcome <?php echo $_SESSION['email_address']; ?>! | <a href="LogoutMiniP.php">Logout</a></p>

    <?php if ($has_booking): ?>
    <div>
        You have an existing booking! 
        <a href="TicketMiniP.php?booking_id=<?php echo $latest_booking_id; ?>">
            View My Ticket
        </a>
    </div>
<?php endif; ?>

    <h3>Now Showing</h3>

    <table>
        <tr>

        <?php foreach($movies as $movie): ?>
            <td align="center">
                <img src="<?php echo $movie['image_path']; ?>" width="120" height="180"><br>
                <b><?php echo $movie['movie_title'];?></b><br>
                <?php echo $movie['pg_rating'] . "|" . $movie['genre'] . "|" . $movie['duration'];?><br>
                <form action="CinemaMiniP.php" method="GET">
                    <input type="hidden" name="movie_id" value="<?php echo ($movie['movie_id']); ?>">
                    <button type="submit">Select</button>
            </form>
            </td>

        <?php endforeach ?>
        </tr>
    </table>
</body>
</html>
