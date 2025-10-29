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








