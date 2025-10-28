<?php
$servername = "127.0.0.1";
$username   = "root";
$password   = "";
$dbname     = "Mini_project";

// Create connection using mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Register new user
function register($full_name, $mobile_number, $email_address, $password, $date_of_birth, $gender, $race, $profession, $location) {
   global $conn;
   $sql = "INSERT INTO users (full_name, mobile_number, email_address, password, date_of_birth, gender, race, profession, location) 
           VALUES ('$full_name', '$mobile_number', '$email_address', '$password', '$date_of_birth', '$gender', '$race', '$profession', '$location')";
   return $conn->query($sql);
}

// Login function to authenticate user
function login($email_address, $password) {
   global $conn;
   $sql = "SELECT * FROM users WHERE email_address = '$email_address' AND password = '$password'";
   $result = $conn->query($sql);
   return $result->fetch_assoc();
}

// Fetch user details by ID
function SelectUsersByID($id){
   global $conn;
   $sql = "SELECT * FROM users WHERE id = '$id'";
   $result = $conn->query($sql);
   return $result->fetch_assoc();
}

// Update user details by ID
function UpdateByID($id, $username, $email, $password){
   global $conn;
   $sql = "UPDATE users SET username='$username', email='$email', password='$password' WHERE id = '$id'";
   return $conn->query($sql);
}

// Fetch all movies from the database
function GetAllMovies(){
   global $conn; 
   $sql = "SELECT * FROM movies"; 
   $result = $conn->query($sql);
   return $result -> fetch_all(MYSQLI_ASSOC); 
}

// Fetch movie details by ID
function GetMovieByID($id){ 
    global $conn;
    $sql = "SELECT * FROM movies WHERE movie_id = '$id'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Fetch all cinemas from the database
function GetAllCinemas(){
   global $conn; 
   $sql = "SELECT * FROM cinema_selection"; 
   $result = $conn->query($sql);
   return $result -> fetch_all(MYSQLI_ASSOC); 
}

// Fetch cinemas by movie ID
function GetCinemasByMovieID($movie_id){
    global $conn;
    $sql = "SELECT * FROM cinema_selection WHERE movie_id = '$movie_id'";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

?>
