<?php
session_start();
require 'ConnMiniP.php';

if ($_SESSION['email_address']) {

} else {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
    exit();
}

$booking_id = $_GET['booking_id'];

$email = $_SESSION['email_address'];
$user_sql = "SELECT * FROM users WHERE email_address = '$email'";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();
$user_id = $user['user_id'];

$ratings_sql = "SELECT * FROM ratings ORDER BY rating_value";
$ratings_result = $conn->query($ratings_sql);
$ratings = $ratings_result->fetch_all(MYSQLI_ASSOC);

if ($_POST) {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    
    $review_sql = "INSERT INTO review (user_id, rating, comment) 
                   VALUES ('$user_id', '$rating', '$comment')";
    
    $conn->query($review_sql);
    
    header("Location: TicketMiniP.php?booking_id=$booking_id");
    exit();
}
?>

<html>
<head>
    <title>Review Page</title>
</head>
<body>
    <h2>Website Review</h2>
    <p>Welcome <?php echo $_SESSION['email_address']; ?>! | <a href="LogoutMiniP.php">Logout</a></p>

    <h3>How was your experience with our website?</h3>

    <form method="POST">
        <p>Rating:</p>
        <select name="rating">
            <option value="">Select Rating</option>
            <?php foreach($ratings as $rating_row): ?>
                <option value="<?php echo $rating_row['rating_value']; ?>">
                    <?php echo $rating_row['rating_label']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <p>Comment:</p>
        <textarea name="comment" rows="4" cols="50"></textarea>
        
        <br><br>
        <button type="submit">Submit Review</button>
    </form>
    
    <br>
    <a href="TicketMiniP.php?booking_id=<?php echo $booking_id; ?>">
        <button type="button">View Ticket</button>
    </a>
</body>
</html>
