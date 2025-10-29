<?php
session_start();
require 'ConnMiniP.php';

// Check if user is logged in
if (!isset($_SESSION['email_address'])) {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
    exit();
}

// Check if cinema_id and movie_id are set
if (!isset($_GET['cinema_id']) || !isset($_GET['movie_id'])) {
    header("Location: CinemaMiniP.php");
    exit();
}

$cinema_id = $_GET['cinema_id'];
$movie_id = $_GET['movie_id'];

// Store in session for easier access
$_SESSION['cinema_id'] = $cinema_id;
$_SESSION['movie_id'] = $movie_id;

// Get movie details
$movie = GetMovieByID($movie_id);

// Get cinema details
$cinema = GetCinemaByID($cinema_id);

// Get all seats for this cinema
$seats = GetSeatsByCinemaID($cinema_id);

// Group seats by row for better display
$seats_by_row = [];
foreach($seats as $seat) {
    $row = substr($seat['seat_number'], 0, 1); // Get first character (A, B, C, etc.)
    $seats_by_row[$row][] = $seat;
}

// Sort seats within each row by seat number (numerically)
foreach($seats_by_row as $row => $row_seats) {
    usort($seats_by_row[$row], function($a, $b) {
        // Extract numeric part from seat number (e.g., "J10" -> 10)
        $numA = (int)substr($a['seat_number'], 1);
        $numB = (int)substr($b['seat_number'], 1);
        return $numA - $numB;
    });
}

// Sort rows in reverse order (J to A) so VIP is at back
ksort($seats_by_row);
$seats_by_row = array_reverse($seats_by_row, true);

// Handle seat selection submission
if(isset($_GET['selected_seats']) && !empty($_GET['selected_seats'])) {
    $selected_seats = $_GET['selected_seats']; // Array of seat_ids
    
    // Store selected seats in session for confirmation page
    $_SESSION['selected_seats'] = $selected_seats;
    
    // Redirect to confirmation page
    header("Location: ConfirmationMiniP.php");
    exit();
}

// If form submitted but no seats selected
if(isset($_GET['proceed']) && (!isset($_GET['selected_seats']) || empty($_GET['selected_seats']))) {
    $error_message = "Please select at least one seat before proceeding!";
}
?>

<html>
<head>
    <title>Seat Selection</title>
    <style>
        .error-box {
            color: red;
            background-color: #ffcccc;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid red;
            border-radius: 5px;
        }
        .warning-box {
            color: #856404;
            background-color: #fff3cd;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ffc107;
            border-radius: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <h2>Seat Selection</h2>
    <p>Welcome <?php echo $_SESSION['email_address']; ?>! | <a href="LogoutMiniP.php">Logout</a></p>

    <h3>Movie: <?php echo $movie['movie_title']; ?></h3>
    <p>
        <b>Cinema:</b> <?php echo $cinema['location']; ?> | 
        <b>Experience:</b> <?php echo $cinema['experience']; ?><br>
        <b>Date:</b> <?php echo $cinema['date']; ?> | 
        <b>Showtime:</b> <?php echo $cinema['showtime']; ?>
    </p>

    <?php if(isset($error_message)): ?>
        <div class="error-box">
            <strong>Error:</strong> <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <!-- Warning box for gap detection -->
    <div id="gap-warning" class="warning-box">
        <strong>⚠️ Warning:</strong> <span id="gap-message"></span>
    </div>

    <h3>Select Your Seats</h3>

    <!-- Legend -->
    <div style="margin-bottom: 20px;">
        <span style="display: inline-block; width: 30px; height: 30px; background-color: green; border: 1px solid black;"></span> Available
        <span style="display: inline-block; width: 30px; height: 30px; background-color: red; border: 1px solid black; margin-left: 20px;"></span> Occupied
        <span style="display: inline-block; width: 30px; height: 30px; background-color: yellow; border: 1px solid black; margin-left: 20px;"></span> Selected
    </div>

    <!-- Seat Type Pricing -->
    <div style="margin-bottom: 20px; background-color: #f0f0f0; padding: 10px; border: 1px solid #ccc;">
        <strong>Seat Pricing:</strong><br>
        VIP: RM35.00 | Premium: RM25.00 | Standard: RM15.00<br>
        <strong style="color: #d9534f;">⚠️ Note: You cannot leave gaps between selected seats in the same row!</strong>
    </div>

    <form method="GET" id="seat-form">
        <!-- Hidden input to detect form submission -->
        <input type="hidden" name="proceed" value="1">
        <input type="hidden" name="cinema_id" value="<?php echo $cinema_id; ?>">
        <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">

        <!-- Screen (at the front/bottom) -->
        <div style="background-color: #ccc; padding: 10px; text-align: center; margin-bottom: 30px; width: 80%; margin-left: auto; margin-right: auto;">
            <h4>SCREEN</h4>
        </div>

        <!-- Seat Map (VIP at back, Standard at front) -->
        <table border="1" cellpadding="5" style="margin: auto;">
            <?php foreach($seats_by_row as $row_letter => $row_seats): ?>
                <tr>
                    <td><b><?php echo $row_letter; ?></b></td>
                    <?php foreach($row_seats as $seat): ?>
                        <td align="center">
                            <?php if($seat['is_available'] == 1): ?>
                                <!-- Available seat - can be selected -->
                                <input type="checkbox" 
                                       name="selected_seats[]" 
                                       value="<?php echo $seat['seat_id']; ?>" 
                                       id="seat_<?php echo $seat['seat_id']; ?>"
                                       data-row="<?php echo $row_letter; ?>"
                                       data-seat-number="<?php echo $seat['seat_number']; ?>"
                                       style="display: none;"
                                       onclick="toggleSeat(this)">
                                <label for="seat_<?php echo $seat['seat_id']; ?>" 
                                       id="label_<?php echo $seat['seat_id']; ?>"
                                       style="display: inline-block; width: 50px; height: 50px; background-color: green; border: 2px solid black; cursor: pointer; line-height: 20px; text-align: center; padding: 5px; color: white; font-weight: bold;">
                                    <?php echo $seat['seat_number']; ?><br>
                                    <small style="font-size: 9px;"><?php echo $seat['seat_type']; ?></small><br>
                                    <small style="font-size: 9px;">RM<?php echo $seat['price']; ?></small>
                                </label>
                            <?php else: ?>
                                <!-- Occupied seat - cannot be selected -->
                                <div style="display: inline-block; width: 50px; height: 50px; background-color: red; border: 2px solid black; line-height: 20px; text-align: center; padding: 5px; color: white; font-weight: bold; opacity: 0.6;"
                                     data-row="<?php echo $row_letter; ?>"
                                     data-occupied="true"
                                     data-seat-number="<?php echo $seat['seat_number']; ?>">
                                    <?php echo $seat['seat_number']; ?><br>
                                    <small style="font-size: 9px;"><?php echo $seat['seat_type']; ?></small><br>
                                    <small style="font-size: 9px;">RM<?php echo $seat['price']; ?></small>
                                </div>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>

        <br>

        <!-- Selected Seats Summary -->
        <div id="summary" style="text-align: center; margin: 20px; padding: 15px; background-color: #f9f9f9; border: 1px solid #ddd;">
            <h4>Selected Seats Summary</h4>
            <p id="selected-count">No seats selected</p>
            <p id="total-price" style="font-size: 18px; font-weight: bold;">Total: RM 0.00</p>
        </div>

        <div style="text-align: center;">
            <button type="submit" id="proceed-btn" style="padding: 10px 30px; font-size: 16px; background-color: #28a745; color: white; border: none; cursor: pointer;">Proceed to Confirmation</button>
        </div>
    </form>

    <!-- Separate form for back button -->
    <form action="CinemaMiniP.php" method="GET" style="display: inline; margin-left: 10px;">
        <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
        <button type="submit" style="padding: 10px 30px; font-size: 16px; background-color: #dc3545; color: white; border: none; cursor: pointer;">Back to Cinema Selection</button>
    </form>

    <script>
        // Store seat data for calculation
        const seatData = {};
        <?php foreach($seats as $seat): ?>
            <?php if($seat['is_available'] == 1): ?>
                seatData[<?php echo $seat['seat_id']; ?>] = {
                    number: '<?php echo $seat['seat_number']; ?>',
                    type: '<?php echo $seat['seat_type']; ?>',
                    price: <?php echo $seat['price']; ?>,
                    row: '<?php echo substr($seat['seat_number'], 0, 1); ?>'
                };
            <?php endif; ?>
        <?php endforeach; ?>

        // Create a map of all seats by row for gap detection
        const allSeatsMap = {};
        <?php foreach($seats_by_row as $row_letter => $row_seats): ?>
            allSeatsMap['<?php echo $row_letter; ?>'] = [
                <?php foreach($row_seats as $seat): ?>
                {
                    id: <?php echo $seat['seat_id']; ?>,
                    number: '<?php echo $seat['seat_number']; ?>',
                    available: <?php echo $seat['is_available']; ?>
                },
                <?php endforeach; ?>
            ];
        <?php endforeach; ?>

        // Check for gaps in seat selection
        function checkForGaps() {
            const selectedSeats = document.querySelectorAll('input[name="selected_seats[]"]:checked');
            
            // Group selected seats by row
            const seatsByRow = {};
            selectedSeats.forEach(checkbox => {
                const row = checkbox.dataset.row;
                if (!seatsByRow[row]) {
                    seatsByRow[row] = [];
                }
                seatsByRow[row].push(parseInt(checkbox.value));
            });

            // Check each row for gaps
            for (const row in seatsByRow) {
                const selectedInRow = seatsByRow[row].sort((a, b) => a - b);
                const allSeatsInRow = allSeatsMap[row];
                
                if (selectedInRow.length > 1) {
                    // Find the indices of selected seats
                    const selectedIndices = selectedInRow.map(seatId => 
                        allSeatsInRow.findIndex(s => s.id === seatId)
                    );
                    
                    // Check if there are available seats between selected seats
                    const minIndex = Math.min(...selectedIndices);
                    const maxIndex = Math.max(...selectedIndices);
                    
                    for (let i = minIndex + 1; i < maxIndex; i++) {
                        const seatBetween = allSeatsInRow[i];
                        if (seatBetween.available === 1 && !selectedInRow.includes(seatBetween.id)) {
                            return {
                                hasGap: true,
                                row: row,
                                missingSeat: seatBetween.number
                            };
                        }
                    }
                }
            }
            
            return { hasGap: false };
        }

        // JavaScript to change seat color when selected and update summary
        function toggleSeat(checkbox) {
            var label = document.getElementById('label_' + checkbox.value);
            if(checkbox.checked) {
                label.style.backgroundColor = 'yellow';
                label.style.color = 'black';
            } else {
                label.style.backgroundColor = 'green';
                label.style.color = 'white';
            }
            updateSummary();
            validateSelection();
        }

        // Validate selection for gaps
        function validateSelection() {
            const gapCheck = checkForGaps();
            const warningBox = document.getElementById('gap-warning');
            const proceedBtn = document.getElementById('proceed-btn');
            
            if (gapCheck.hasGap) {
                warningBox.style.display = 'block';
                document.getElementById('gap-message').textContent = 
                    `You cannot leave seat ${gapCheck.missingSeat} empty in row ${gapCheck.row}. Please select consecutive seats!`;
                proceedBtn.disabled = true;
                proceedBtn.style.opacity = '0.5';
                proceedBtn.style.cursor = 'not-allowed';
                return false;
            } else {
                warningBox.style.display = 'none';
                proceedBtn.disabled = false;
                proceedBtn.style.opacity = '1';
                proceedBtn.style.cursor = 'pointer';
                return true;
            }
        }

        // Update selected seats summary
        function updateSummary() {
            var checkboxes = document.querySelectorAll('input[name="selected_seats[]"]:checked');
            var count = checkboxes.length;
            var total = 0;
            var seatNumbers = [];

            checkboxes.forEach(function(checkbox) {
                var seatId = checkbox.value;
                if(seatData[seatId]) {
                    total += seatData[seatId].price;
                    seatNumbers.push(seatData[seatId].number);
                }
            });

            if(count > 0) {
                document.getElementById('selected-count').innerHTML = 
                    '<strong>' + count + ' seat(s) selected:</strong> ' + seatNumbers.join(', ');
                document.getElementById('total-price').innerHTML = 
                    'Total: RM ' + total.toFixed(2);
            } else {
                document.getElementById('selected-count').innerHTML = 'No seats selected';
                document.getElementById('total-price').innerHTML = 'Total: RM 0.00';
            }
        }

        // Prevent form submission if there are gaps
        document.getElementById('seat-form').addEventListener('submit', function(e) {
            if (!validateSelection()) {
                e.preventDefault();
                alert('Please fix the seat selection errors before proceeding!');
            }
        });
    </script>
</body>
</html>
