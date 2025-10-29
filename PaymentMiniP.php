<?php
session_start();
require 'ConnMiniP.php'; // Your existing connection and functions

// Check if user is logged in
if (!isset($_SESSION['email_address'])) {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
    exit();
}

// Check if booking_id and total_price exist in session
if (isset($_SESSION['booking_id'])) {
    $booking_id = $_SESSION['booking_id'];
} else {
    echo "No booking found. Please confirm your seats first.";
    exit();
}

if (isset($_SESSION['total_price'])) {
    $total_price = $_SESSION['total_price'];
} else {
    $total_price = 0;
}

// Fetch payment methods from database
$sql = "SELECT * FROM payment_methods WHERE active = 1";
$result = $conn->query($sql);
$payment_methods = [];
if ($result) {
    $payment_methods = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle payment submission
if (isset($_POST['pay'])) {
    $selected_method_id = $_POST['payment_method'];
    
    // Get method name
    $method_sql = "SELECT method_name FROM payment_methods WHERE method_id = '$selected_method_id'";
    $method_result = $conn->query($method_sql);
    $method = $method_result->fetch_assoc();
    $method_name = $method['method_name'];

    // Insert payment record
    $insert_sql = "INSERT INTO payment (booking_id, payment_method, amount, payment_status) 
                   VALUES ('$booking_id', '$method_name', '$total_price', 'Paid')";
    if ($conn->query($insert_sql)) {
        echo "<h3>Payment Successful!</h3>";
        echo "<p>Booking ID: $booking_id</p>";
        echo "<p>Payment Method: $method_name</p>";
        echo "<p>Amount Paid: RM " . number_format($total_price, 2) . "</p>";
        echo "<p>Status: Paid</p>";
        echo "<p><a href='IndexMiniP.php'>Back to Home</a></p>";
        
        // Optional: clear session for this booking
        unset($_SESSION['booking_id']);
        unset($_SESSION['total_price']);
        unset($_SESSION['selected_seats']);
        exit();
    } else {
        echo "Payment failed. Please try again.";
    }
}
?>

<h2>Payment Page</h2>
<p>Total Amount: RM <?php echo number_format($total_price, 2); ?></p>

<form method="POST">
    <label>Select Payment Method:</label><br><br>
    <?php foreach($payment_methods as $method): ?>
        <input type="radio" name="payment_method" value="<?php echo $method['method_id']; ?>" required>
        <?php echo $method['method_name']; ?> (<?php echo $method['provider']; ?>)<br>
    <?php endforeach; ?>
    <br>
    <button type="submit" name="pay">Pay Now</button>
</form>


