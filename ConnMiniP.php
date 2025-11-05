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

// Fetch user details by email address
function GetUserByEmail($email) {
    global $conn;
    $sql = "SELECT * FROM users WHERE email_address = '$email'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Fetch user details by ID
function getUserByID($id){
   global $conn;
   $sql = "SELECT * FROM users WHERE user_id = '$id'";
   $result = $conn->query($sql);
   return $result->fetch_assoc();
}

// Update user details by ID
function UpdateByID($id, $full_name, $mobile_number, $email_address, $gender, $race, $profession, $location){
   global $conn;
   $sql = "UPDATE users SET full_name='$full_name', mobile_number='$mobile_number', email_address='$email_address', gender='$gender', race='$race', profession='$profession', location='$location' WHERE user_id = '$id'";
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

// Fetch movie details by booking_id
function GetMovieByBookingID($booking_id) {
    global $conn;
    $sql = "SELECT m.* FROM booking b
            JOIN cinema_selection c ON b.cinema_id = c.cinema_id
            JOIN movies m ON c.movie_id = m.movie_id
            WHERE b.booking_id = '$booking_id'";
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

// Fetch cinema details by cinema_id
function GetCinemaByID($cinema_id){
    global $conn;
    $sql = "SELECT * FROM cinema_selection WHERE cinema_id = '$cinema_id'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Fetch all seats for a specific cinema
function GetSeatsByCinemaID($cinema_id){
    global $conn;
    $sql = "SELECT * FROM seat_selection WHERE cinema_id = '$cinema_id' ORDER BY seat_number";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Update seat availability (mark as occupied)
function UpdateSeatAvailability($seat_id, $is_available){
    global $conn;
    $sql = "UPDATE seat_selection SET is_available = '$is_available' WHERE seat_id = '$seat_id'";
    return $conn->query($sql);
}

// Get seat details by seat_id
function GetSeatByID($seat_id){
    global $conn;
    $sql = "SELECT * FROM seat_selection WHERE seat_id = '$seat_id'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Create a new booking
function CreateBooking($user_id, $cinema_id){
    global $conn;
    $booking_date = date('Y-m-d H:i:s');
    $sql = "INSERT INTO booking (user_id, cinema_id, booking_date, status) 
            VALUES ('$user_id', '$cinema_id', '$booking_date', 'Pending')";
    if($conn->query($sql)) {
        return $conn->insert_id; // Return the booking_id
    }
    return false;
}
// Payment function
function CreatePayment($booking_id, $payment_method, $card_number, $card_name, $expiry_date, $cvv, $wallet_address, $crypto_amount, $amount, $status) {
    global $conn;
    $sql = "INSERT INTO payment (booking_id, payment_method, card_number, card_name, expiry_date, cvv, wallet_address, crypto_amount, amount, status) 
            VALUES ('$booking_id', '$payment_method', '$card_number', '$card_name', '$expiry_date', '$cvv', '$wallet_address', '$crypto_amount', '$amount', '$status')";
    return $conn->query($sql);
}

// Get payment methods
function GetPaymentMethodsByType($type) {
    global $conn;
    $sql = "SELECT * FROM payment_methods WHERE method_type = '$type'";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}
// Get all ratings
function GetAllRatings() {
    global $conn;
    $sql = "SELECT * FROM ratings ORDER BY rating_value";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Create review
function CreateReview($user_id, $rating, $comment) {
    global $conn;
    $sql = "INSERT INTO review (user_id, rating, comment) 
            VALUES ('$user_id', '$rating', '$comment')";
    return $conn->query($sql);

}

// Ticket function
function CreateTicket($Booking_ID, $Seat_ID, $Ticket_Type, $Ticket_Price) {
    global $conn;
    $sql = "INSERT INTO ticket (Booking_ID, Seat_ID, Ticket_Type, Ticket_Price) 
            VALUES ('$Booking_ID', '$Seat_ID', '$Ticket_Type', '$Ticket_Price')";
    return $conn->query($sql);
}

// Fetch booking details by booking ID (includes movie + cinema)
function GetBookingDetailsByID($booking_id) {
    global $conn;
    $sql = "SELECT b.*, cs.*, m.movie_title, m.genre, m.pg_rating, m.duration
            FROM booking b
            JOIN cinema_selection cs ON b.cinema_id = cs.cinema_id
            JOIN movies m ON cs.movie_id = m.movie_id
            WHERE b.booking_id = '$booking_id'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Fetch tickets for a specific booking
function GetTicketsByBookingID($booking_id) {
    global $conn;
    $sql = "SELECT t.*, s.seat_number 
            FROM ticket t
            JOIN seat_selection s ON t.Seat_ID = s.seat_id
            WHERE t.Booking_ID = '$booking_id'";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}




