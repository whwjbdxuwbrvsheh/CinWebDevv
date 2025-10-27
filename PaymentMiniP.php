<?php
session_start();
require 'ConnMiniP.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: LoginMiniP.php");
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'];

// Get latest cinema selection
$selection = GetUserLatestSelection($user_id);

// If no selection found, redirect back
if (!$selection) {
    header("Location: IndexMiniP.php");
    exit();
}

// Check if seats were selected
if (empty($selection['seats'])) {
    header("Location: SelectSeats.php");
    exit();
}

// Check if booking reference exists
if (empty($selection['booking_ref'])) {
    header("Location: ConfirmationMiniP.php");
    exit();
}

// Get movie details
$movie = GetMovieByID($selection['movie_id']);

// Get total price from database
$total_price = $selection['total_price'];

// Handle payment form submission
if(isset($_POST['pay'])) {
    $payment_method = $_POST['payment_method'];
    
    // Simple validation
    if(empty($payment_method)) {
        $error = "Please select a payment method!";
    } else {
        // Save payment info to database
        UpdatePaymentInfo($selection['id'], $payment_method);
        
        // Redirect to receipt/success page
        header("Location: ReceiptMiniP.php");
        exit();
    }
}

// Prepare order summary for display
$order_summary = [
    'Movie' => $movie['movie_title'],
    'Location' => $selection['location'],
    'Date' => date('d F Y', strtotime($selection['date'])),
    'Showtime' => $selection['showtime'],
    'Experience' => $selection['experience'],
    'Seats' => $selection['seats'],
    'Total Amount' => 'RM ' . number_format($total_price, 2)
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
</head>
<body>
    <h2>Payment</h2>
    <p>Welcome <?php echo $_SESSION['email_address']; ?>! | <a href="LogoutMiniP.php">Logout</a></p>
    
    <?php if(isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <hr>
    
    <h3>Order Summary:</h3>
    
    <table border="1" cellpadding="10" cellspacing="0">
        <?php foreach($order_summary as $label => $value): ?>
            <tr>
                <td><b><?php echo $label; ?>:</b></td>
                <td><?php echo $value; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <hr>
    
    <h3>Payment Method:</h3>
    
    <form method="POST">
        <p><b>Select Payment Method:</b></p>
        
        <input type="radio" name="payment_method" value="Credit Card" id="credit" required>
        <label for="credit">Credit Card</label><br>
        
        <input type="radio" name="payment_method" value="Debit Card" id="debit">
        <label for="debit">Debit Card</label><br>
        
        <input type="radio" name="payment_method" value="Online Banking" id="banking">
        <label for="banking">Online Banking</label><br>
        
        <input type="radio" name="payment_method" value="E-Wallet" id="ewallet">
        <label for="ewallet">E-Wallet (Touch 'n Go / GrabPay)</label><br>
        
        <br>
        
        <h4>Card Details:</h4>
        <p>
            <label>Cardholder Name:</label><br>
            <input type="text" name="cardholder_name" placeholder="Enter name on card">
        </p>
        
        <p>
            <label>Card Number:</label><br>
            <input type="text" name="card_number" placeholder="XXXX-XXXX-XXXX-XXXX" maxlength="19">
        </p>
        
        <p>
            <label>Expiry Date:</label><br>
            <input type="text" name="expiry_date" placeholder="MM/YY" maxlength="5" style="width: 80px;">
        </p>
        
        <p>
            <label>CVV:</label><br>
            <input type="text" name="cvv" placeholder="XXX" maxlength="3" style="width: 60px;">
        </p>
        
        <hr>
        
        <p><b>Total to Pay: RM <?php echo number_format($total_price, 2); ?></b></p>
        
        <button type="submit" name="pay">Complete Payment</button>
        <a href="ConfirmationMiniP.php"><button type="button">Back</button></a>
    </form>
</body>
</html>