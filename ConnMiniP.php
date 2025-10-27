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
   $sql = "SELECT * FROM users WHERE user_id = '$id'";
   $result = $conn->query($sql);
   return $result->fetch_assoc();
}

// Update user details by ID
function UpdateByID($id, $username, $email, $password){
   global $conn;
   $sql = "UPDATE users SET username='$username', email='$email', password='$password' WHERE user_id = '$id'";
   return $conn->query($sql);
}

// Fetch all movies from the database
function GetAllMovies(){
   global $conn; 
   $sql = "SELECT * FROM movies"; 
   $result = $conn->query($sql);
   return $result -> fetch_all(MYSQLI_ASSOC); 
}

// Fetch movie details by ID - handle both id and movie_id possibilities
function GetMovieByID($movie_id){ 
    global $conn;
    // Try with 'id' first (most common)
    $sql = "SELECT * FROM movies WHERE id = '$movie_id'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();

}

// Store cinema selection details - UPDATED to include user_id
function CinemaSelection($user_id, $movie_id, $location, $date, $experience, $showtime){
    global $conn;
    $sql = "INSERT INTO cinema_selection (user_id, movie_id, location, date, experience, showtime) 
           VALUES ('$user_id', '$movie_id', '$location','$date', '$experience', '$showtime')";
   return $conn->query($sql);
}

// Get user's latest cinema selection
function GetUserLatestSelection($user_id) {
    global $conn;
    $sql = "SELECT * FROM cinema_selection WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    
    if($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Get already booked seats for this showtime
function GetBookedSeats($movie_id, $date, $showtime, $location) {
    global $conn;
    $sql = "SELECT seats FROM cinema_selection 
            WHERE movie_id = '$movie_id' 
            AND date = '$date' 
            AND showtime = '$showtime' 
            AND location = '$location'
            AND seats IS NOT NULL";
    $result = $conn->query($sql);
    
    $booked = [];
    if($result) {
        while($row = $result->fetch_assoc()) {
            if(!empty($row['seats'])) {
                $seats = explode(',', $row['seats']);
                $booked = array_merge($booked, $seats);
            }
        }
    }
    return $booked;
}

// Update seats in selection
function UpdateSeatsSelection($selection_id, $seats) {
    global $conn;
    $sql = "UPDATE cinema_selection SET seats = '$seats' WHERE id = '$selection_id'";
    return $conn->query($sql);
}

// Update booking reference and total price
function UpdateBookingDetails($selection_id, $booking_ref, $total_price) {
    global $conn;
    $sql = "UPDATE cinema_selection 
            SET booking_ref = '$booking_ref', 
                total_price = '$total_price' 
            WHERE id = '$selection_id'";
    return $conn->query($sql);
}

// Update payment information
function UpdatePaymentInfo($selection_id, $payment_method) {
    global $conn;
    $sql = "UPDATE cinema_selection 
            SET payment_method = '$payment_method', 
                payment_status = 'Paid' 
            WHERE id = '$selection_id'";
    return $conn->query($sql);
}

// Get booking by ID
function GetBookingByID($selection_id) {
    global $conn;
    $sql = "SELECT * FROM cinema_selection WHERE id = '$selection_id'";
    $result = $conn->query($sql);
    
    if($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}
?>