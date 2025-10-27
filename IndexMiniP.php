
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

    // Redirect to seat selection page upon successful selection
    if($result){
        header("Location: SelectSeats.php");
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
            <td align="center">
                <img src="superhero_biceps.png" width="120" height="180"><br>
                <b>Superhero Biceps</b><br>
                PG13 | Action | 1h 55m<br>
                <form action="CinemaMiniP.php" method="POST">
                <input type="hidden" name="movie_id" value="1">
                <button type="submit">Select</button>
            </form>
            </td>
            <td align="center">
                <img src="cinta_separuh_hati.png" width="120" height="180"><br>
                <b>Cinta Separuh Hati</b><br>
                PG | Romance | 1h 50m<br>
                <form action="CinemaMiniP.php" method="POST">
                <input type="hidden" name="movie_id" value="2">
                <button type="submit">Select</button>
            </form>
            </td>
            <td align="center">
                <img src="zombie_gmi.png" width="120" height="180"><br>
                <b>Zombie GMI</b><br>
                PG13 | Action | 2h 00m<br>
                <form action="CinemaMiniP.php" method="POST">
                <input type="hidden" name="movie_id" value="3">
                <button type="submit">Select</button>
            </form>
            </td>
            <td align="center">
                <img src="my_miss_time.png" width="120" height="180"><br>
                <b>My Miss Time</b><br>
                PG | Drama | 1h 45m<br>
                <form action="CinemaMiniP.php" method="POST">
                <input type="hidden" name="movie_id" value="4">
                <button type="submit">Select</button>
            </form>>
            </td>
            <td align="center">
                <img src="saya_suka_awuck_mr_hensem.png" width="120" height="180"><br>
                <b>Saya Suka Awuck Mr Hensem</b><br>
                PG | Comedy | 1h 40m<br>
                <form action="CinemaMiniP.php" method="POST">
                <input type="hidden" name="movie_id" value="5">
                <button type="submit">Select</button>
            </form>
            </td>
        </tr>

        <tr>
            <td align="center">
                <img src="images/magical_vico.jpg" width="120" height="180"><br>
                <b>Magical Vico</b><br>
                U | Fantasy | 1h 35m<br>
                <form action="CinemaMiniP.php" method="POST">
                <input type="hidden" name="movie_id" value="6">
                <button type="submit">Select</button>
            </form>
            </td>
            <td align="center">
                <img src="images/stokin_nathura.jpg" width="120" height="180"><br>
                <b>Stokin Nathura</b><br>
                U | Comedy | 1h 30m<br>
                <form action="CinemaMiniP.php" method="POST">
                <input type="hidden" name="movie_id" value="7">
                <button type="submit">Select</button>
            </form>
            </td>
            <td align="center">
                <img src="images/say_no_to_gl.jpg" width="120" height="180"><br>
                <b>Say No To GL</b><br>
                U | Documentary | 1h 25m<br>
                <form action="CinemaMiniP.php" method="POST">
                <input type="hidden" name="movie_id" value="8">
                <button type="submit">Select</button>
            </form>
            </td>
            <td align="center">
                <img src="images/aku_bukan_ustaz.jpg" width="120" height="180"><br>
                <b>Aku Bukan Ustaz</b><br>
                PG | Religious | 1h 40m<br>
                <form action="CinemaMiniP.php" method="POST">
                <input type="hidden" name="movie_id" value="9">
                <button type="submit">Select</button>
            </form>
            </td>
            <td align="center">
                <img src="images/ava_and_the_genks.jpg" width="120" height="180"><br>
                <b>Ava and the Genks</b><br>
                PG | Adventure | 1h 50m<br>
                <form action="CinemaMiniP.php" method="POST">
                <input type="hidden" name="movie_id" value="10">
                <button type="submit">Select</button>
            </form>
            </td>
        </tr>
    </table>
</body>
</html>