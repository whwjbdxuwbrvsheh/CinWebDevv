<?php
session_start();
require 'ConnMiniP.php';

// Ensure user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    header("Location: LoginMiniP.php");
    exit();
}

// Check if movie_id is set from previous page
if(isset($_POST['movie_id'])) {
    $movie_id = $_POST['movie_id'];
    $movie = GetMovieByID($movie_id);
    $_SESSION['movie_id'] = $movie_id;
} else {
    header("Location: IndexMiniP.php");
    exit();
}

// Handle form submission
if(isset($_POST['user_id'], $_POST['movie_id'], $_POST['location'], $_POST['date'], $_POST['experience'], $_POST['showtime'])) {   
    
    $user_id = $_SESSION['user_id'];
    $movie_id = $_POST['movie_id'];
    $location = $_POST['location'];
    $date = $_POST['date'];
    $experience = $_POST['experience'];
    $showtime = $_POST['showtime'];

    // Process cinema selection
    CinemaSelection($user_id, $movie_id, $location, $date, $experience, $showtime);
    header("Location: SelectSeats.php");
    exit();
}
?>

<html>
<head>
    <title>Cinema Selection</title>
</head>
<body>
    <h2>Cinema Selection</h2>
    <p>Welcome <?php echo $_SESSION['email_address']; ?>! | <a href="LogoutMiniP.php">Logout</a></p>


    <form action="CinemaMiniP.php" method="POST">
    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">    
    <input type="hidden" name="movie_id" value="<?php echo $_SESSION['movie_id']; ?>">
        <p><b>Select Cinema Location:</b></p>
        <select name="location" required>
            <option value="GSC Mid Valley">GSC Mid Valley</option>
            <option value="TGV Bukit Jalil">TGV Bukit Jalil</option>
            <option value="MBO Melaka">MBO Melaka</option>
            <option value="GSC IOI City Mall">GSC IOI City Mall</option>
        </select>

        <p><b>Select Date:</b></p>
        <input type="date" name="date" required>

        <p><b>Select Experience:</b></p>
        <input type="radio" name="experience" value="2D"> 2D
        <input type="radio" name="experience" value="3D"> 3D

        <p><b>Select Showtime:</b></p>
        <select name="showtime" required>
            <option value="10:00 AM">10:00 AM</option>
            <option value="1:00 PM">1:00 PM</option>
            <option value="4:00 PM">4:00 PM</option>
            <option value="7:00 PM">7:00 PM</option>
        </select>

        <br><br>
        <button type="submit" name="next">Next: Select Seats</button>
        <a href="IndexMiniP.php"><button type="button">Back to Movies</button></a>
    </form>
</body>
</html>