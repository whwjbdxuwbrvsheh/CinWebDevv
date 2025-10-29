<?php
session_start();
require 'ConnMiniP.php';

// Check if user is logged in
if (isset($_SESSION['email_address'])) {

} else {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
    exit();
}

// Check if movie ID is set in session
if (isset($_POST['movie_id'])) {
    $movie_id = $_POST['movie_id'];
    $_movie = GetMovieByID($movie_id);
} else {
    // If not set, redirect back to movie selection
    header("Location: IndexMiniP.php");
    exit();
}


$movie_id = $_POST['movie_id'];

// Get movie details
$movie = GetMovieByID($movie_id);

// Get all cinema data for this movie
$cinema = GetCinemasByMovieID($movie_id);
?>

<html>
<head>
    <title>Cinema Selection</title>
</head>
<body>
    <h2>Cinema Selection</h2>
    <p>Welcome <?php echo $_SESSION['email_address']; ?>! | <a href="LogoutMiniP.php">Logout</a></p>

    <h3>Cinema for <?php echo $movie['movie_title']; ?></h3>

    <table border="1" cellpadding="10">
        <tr>
            <?php foreach($cinema as $cin): ?>
                <td align="center">
                    <b><?php echo $cin['location']; ?></b><br>
                    <b><?php echo $cin['experience']; ?></b><br>
                    Date: <?php echo $cin['date']; ?><br>
                    Showtime: <?php echo $cin['showtime']; ?><br><br>
                    
                    <form action="SeatsMiniP.php" method="POST">
                        <input type="hidden" name="cinema_id" value="<?php echo $cin['cinema_id']; ?>">
                        <input type="hidden" name="movie_id" value="<?php echo $movie['movie_id']; ?>">
                        <button type="submit">Select</button>
                    </form>
                </td>
            <?php endforeach; ?>
        </tr>
    </table>

<br>
<form action="IndexMiniP.php" method="GET">
    <button type="submit">Back to Movie Selection</button>
</form>
</body>
</html>



