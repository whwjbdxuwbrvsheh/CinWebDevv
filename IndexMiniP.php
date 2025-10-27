<?php
session_start();
require 'ConnMiniP.php';

// Fetch all movies
$movies = GetAllMovies();

// Check if user is logged in
if (isset($_SESSION['email_address'])) {
    echo "Hello, you are logged in as: " . $_SESSION['email_address'];
    echo "<br><a href='LogoutMiniP.php'>Logout</a>";
} else {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
}

// Handle movie selection form submission
if($_POST) {
    $user_id = $_SESSION['user_id'];
    $movie_id = $_POST['movie_id'];
    $location = $_POST['location'];
    $date = $_POST['date'];
    $experience = $_POST['experience'];
    $showtime = $_POST['showtime'];

    // Store cinema selection
    $result = CinemaSelection($user_id, $movie_id, $location, $date, $experience, $showtime);

    // Redirect to cinema selection page upon successful selection
    if($result){
        header("Location: CinemaMiniP.php");
        exit();
    } else {
        echo "<script>alert('Selection Unsuccessful');</script>";
    }
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

    <h3>Now Showing</h3>

    <table>
        <tr>

        <?php foreach($movies as $movie): ?>
            <td align="center">
                <img src="<?php echo $movie['image_path']; ?>" width="120" height="180"><br>
                <b><?php echo $movie['movie_title'];?></b><br>
                <?php echo $movie['pg_rating'] . "|" . $movie['genre'] . "|" . $movie['duration'];?><br>
                <form action="CinemaMiniP.php" method="POST">
                <button type="submit">Select</button>
            </form>
            </td>

        <?php endforeach ?>
        </tr>
    </table>
</body>
</html>






