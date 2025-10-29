<?php
session_start();
require 'ConnMiniP.php';

// Check if user is logged in
if (isset($_SESSION['email_address'])) {

} else {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
    exit();
}

// Get booking details from session
$booking_id = $_SESSION['booking_id'];
$selected_seats = $_SESSION['selected_seats'];

// Calculate total amount
$total_amount = 0;
foreach ($selected_seats as $seat_id) {
    $seat = GetSeatByID($seat_id);
    $total_amount = $total_amount + $seat['price'];
}

// Check if payment was successful
if (isset($_GET['success'])) {
    $payment_success = true;
} else {
    $payment_success = false;
}

// Process form submission
if ($_POST) {
    $payment_method = $_POST['payment_method'];
    $action = $_POST['action'];
    
    if ($action == 'select') {
        $user_choice = $payment_method;
    }
    
    if ($action == 'pay') {
        if ($payment_method == 'Bitcoin' || $payment_method == 'Monero') {
            $wallet_address = $_POST['wallet_address'];
            $crypto_amount = $_POST['crypto_amount'];
        } else {
            $card_number = $_POST['card_number'];
            $card_name = $_POST['card_name'];
            $expiry_date = $_POST['expiry_date'];
            $cvv = $_POST['cvv'];
        }
        
        if (CreatePayment($booking_id, $payment_method, $card_number, $card_name, $expiry_date, $cvv, $wallet_address, $crypto_amount, $total_amount, 'Completed')) {
            foreach ($selected_seats as $seat_id) {
                $seat = GetSeatByID($seat_id);
                CreateTicket($booking_id, $seat_id, $seat['seat_type'], $seat['price']);
            }
            
            header("Location: PaymentMiniP.php?success=1");
            exit();
        }
    }
}

// Get payment methods from database
$crypto_options = GetPaymentMethodsByType('crypto');
$card_options = GetPaymentMethodsByType('card');

// Check user choice
if (isset($user_choice)) {
    $current_choice = $user_choice;
} else {
    $current_choice = '';
}
?>

<html>
<head>
    <title>Payment Selection</title>
</head>
<body>
    <h2>Payment Selection</h2>
    <p>Welcome <?php echo $_SESSION['email_address']; ?>! | <a href="LogoutMiniP.php">Logout</a></p>

    <?php if ($payment_success) { ?>
        <h3>Payment Successful!</h3>
        <p><a href="ReviewMiniP.php?booking_id=<?php echo $booking_id; ?>">Continue to Review</a></p>
    <?php } else { ?>
        <h3>Total Amount: RM<?php echo $total_amount; ?></h3>

        <form method="post">
            <p>Cryptocurrencies:</p>
            <?php foreach ($crypto_options as $crypto): ?>
                <input type="radio" name="payment_method" value="<?php echo $crypto['method_name']; ?>">
                <?php echo $crypto['method_name']; ?><br>
            <?php endforeach; ?>
            
            <p>Credit Cards:</p>
            <?php foreach ($card_options as $card): ?>
                <input type="radio" name="payment_method" value="<?php echo $card['method_name']; ?>">
                <?php echo $card['method_name']; ?><br>
            <?php endforeach; ?>
            
            <br>

            <?php if ($current_choice == 'Bitcoin' || $current_choice == 'Monero') { ?>
                <p>Wallet Address: <input type="text" name="wallet_address"></p>
                <p>Amount: <input type="text" name="crypto_amount"></p>
                <input type="hidden" name="action" value="pay">
                <button type="submit">Pay with <?php echo $current_choice; ?></button>
            <?php } ?>

            <?php if ($current_choice == 'Visa' || $current_choice == 'MasterCard' || $current_choice == 'American Express') { ?>
                <p>Card Number: <input type="text" name="card_number"></p>
                <p>Cardholder Name: <input type="text" name="card_name"></p>
                <p>Expiry Date: <input type="text" name="expiry_date"></p>
                <p>CVV: <input type="text" name="cvv"></p>
                <input type="hidden" name="action" value="pay">
                <button type="submit">Pay with <?php echo $current_choice; ?></button>
            <?php } ?>

            <?php if ($current_choice == '') { ?>
                <input type="hidden" name="action" value="select">
                <button type="submit">Select Payment Method</button>
            <?php } ?>
            
        </form>
    <?php } ?>
    
</body>
</html>




