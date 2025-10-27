<?php
session_start();
require 'ConnMiniP.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: LoginMiniP.php");
    exit();
}

// Get the latest cinema selection for this user
$user_id = $_SESSION['user_id'];
$selection = GetUserLatestSelection($user_id);

// If no selection found, redirect back
if (!$selection) {
    header("Location: IndexMiniP.php");
    exit();
}

// Get movie details
$movie = GetMovieByID($selection['movie_id']);

// Get already booked seats for this showtime
$booked_seats = GetBookedSeats($selection['movie_id'], $selection['date'], $selection['showtime'], $selection['location']);

// Initialize selected seats from session or empty array
if(!isset($_SESSION['selected_seats'])) {
    $_SESSION['selected_seats'] = [];
}

// Handle seat selection/deselection via GET
if(isset($_GET['seat'])) {
    $seat = $_GET['seat'];
    
    // Check if seat is not booked
    if(!in_array($seat, $booked_seats)) {
        if(in_array($seat, $_SESSION['selected_seats'])) {
            // Deselect seat
            $_SESSION['selected_seats'] = array_diff($_SESSION['selected_seats'], [$seat]);
        } else {
            // Select seat (max 8)
            if(count($_SESSION['selected_seats']) < 8) {
                $_SESSION['selected_seats'][] = $seat;
            }
        }
    }
    
    // Redirect to remove ?seat= from URL
    header("Location: SelectSeats.php");
    exit();
}

// Handle final confirmation
if(isset($_POST['confirm'])) {
    if(count($_SESSION['selected_seats']) > 0) {
        $selection_id = $selection['id'];
        $seats = implode(',', $_SESSION['selected_seats']);
        
        // Update selection with chosen seats
        $result = UpdateSeatsSelection($selection_id, $seats);
        
        if($result) {
            // Clear selected seats from session
            unset($_SESSION['selected_seats']);
            // Redirect to confirmation page (not payment)
            header("Location: ConfirmationMiniP.php");
            exit();
        } else {
            $error = "Seat selection failed. Please try again.";
        }
    } else {
        $error = "Please select at least one seat.";
    }
}

// Handle clear selection
if(isset($_GET['clear'])) {
    $_SESSION['selected_seats'] = [];
    header("Location: SelectSeats.php");
    exit();
}

$selected_seats = $_SESSION['selected_seats'];
$total_seats = count($selected_seats);
$total_price = $total_seats * 15.00;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Seats</title>
    <style>
        .seat {
            display: inline-block;
            width: 40px;
            height: 40px;
            margin: 3px;
            text-align: center;
            line-height: 40px;
            border: 1px solid black;
            text-decoration: none;
            color: black;
        }
        
        .seat.available {
            background-color: lightgreen;
        }
        
        .seat.selected {
            background-color: yellow;
        }
        
        .seat.booked {
            background-color: red;
            color: white;
        }
        
        .screen {
            background-color: gray;
            color: white;
            text-align: center;
            padding: 10px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h2>Select Your Seats</h2>
    <p>Welcome <?php echo $_SESSION['email_address']; ?>! | <a href="LogoutMiniP.php">Logout</a></p>
    
    <?php if(isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <h3>Booking Details:</h3>
    <p><b>Movie:</b> <?php echo $movie['movie_title']; ?></p>
    <p><b>Location:</b> <?php echo $selection['location']; ?></p>
    <p><b>Date:</b> <?php echo $selection['date']; ?></p>
    <p><b>Experience:</b> <?php echo $selection['experience']; ?></p>
    <p><b>Showtime:</b> <?php echo $selection['showtime']; ?></p>
    
    <hr>
    
    <p>
        <span style="background-color: lightgreen; padding: 5px;">Available</span>
        <span style="background-color: yellow; padding: 5px;">Selected</span>
        <span style="background-color: red; color: white; padding: 5px;">Booked</span>
    </p>

    <div class="screen">SCREEN</div>

    <div>
        <?php
        // Create seat layout (8 rows, 10 seats per row)
        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $seats_per_row = 10;
        
        foreach($rows as $row) {
            echo "<div>";
            echo "<b>$row</b> ";
            
            for($i = 1; $i <= $seats_per_row; $i++) {
                $seat_number = $row . $i;
                $is_booked = in_array($seat_number, $booked_seats);
                $is_selected = in_array($seat_number, $selected_seats);
                
                if($is_booked) {
                    // Booked seat (not clickable)
                    echo "<span class='seat booked'>$i</span>";
                } elseif($is_selected) {
                    // Selected seat
                    echo "<a href='SelectSeats.php?seat=$seat_number' class='seat selected'>$i</a>";
                } else {
                    // Available seat
                    echo "<a href='SelectSeats.php?seat=$seat_number' class='seat available'>$i</a>";
                }
            }
            
            echo "</div>";
        }
        ?>
    </div>
    
    <hr>
    
    <h3>Your Selection:</h3>
    <p><b>Selected Seats:</b> 
        <?php echo count($selected_seats) > 0 ? implode(', ', $selected_seats) : 'None'; ?>
    </p>
    <p><b>Total Seats:</b> <?php echo $total_seats; ?> / 8</p>
    <p><b>Total Price:</b> RM <?php echo number_format($total_price, 2); ?></p>
    
    <form method="POST">
        <button type="submit" name="confirm">Confirm Seats</button>
        
        <?php if(count($selected_seats) > 0): ?>
            <a href="SelectSeats.php?clear=1">
                <button type="button">Clear Selection</button>
            </a>
        <?php endif; ?>
        
        <a href="CinemaMiniP.php">
            <button type="button">Back</button>
        </a>
    </form>
</body>
</html>