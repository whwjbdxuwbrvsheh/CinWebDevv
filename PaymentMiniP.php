<?php
session_start();
require 'ConnMiniP.php';

// Check if user is logged in
if (!isset($_SESSION['email_address'])) {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
    exit();
}

// Check if required session data exists
if (!isset($_SESSION['booking_id']) || !isset($_SESSION['selected_seats'])) {
    // Redirect if essential booking data is missing
    header("Location: IndexMiniP.php");
    exit();
}

// Get booking details from session
$booking_id = $_SESSION['booking_id'];
$selected_seats = $_SESSION['selected_seats'];

// Calculate total amount by iterating over selected seats
$total_amount = 0;
// Assuming GetSeatByID is defined in ConnMiniP.php and returns seat details
foreach ($selected_seats as $seat_id) {
    $seat = GetSeatByID($seat_id);
    if ($seat && isset($seat['price'])) {
        $total_amount += $seat['price'];
    }
}

// Check if payment was successful
if (isset($_GET['success'])) {
    $payment_success = true;
} else {
    $payment_success = false;
}

// Variables initialization (No 'null' assigned, initialized to empty strings/zero)
$current_choice = '';
$payment_method = '';
$action = '';
$card_number = '';
$card_name = '';
$expiry_date = '';
$cvv = '';
$wallet_address = '';
$crypto_amount = 0;

// Process form submission
if ($_POST) {
    
    // Use isset() and assign only if the key exists
    if (isset($_POST['payment_method'])) {
        $payment_method = $_POST['payment_method'];
    }
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
    }
    
    if ($action == 'select') {
        // --- UI FLOW FIX: Set current_choice immediately after selection ---
        $current_choice = $payment_method;
        
    } elseif ($action == 'pay') {
        $current_choice = $payment_method; // Keep the choice selected during 'pay' attempt
        
        if ($payment_method == 'Bitcoin' || $payment_method == 'Monero') {
            if (isset($_POST['wallet_address'])) {
                $wallet_address = $_POST['wallet_address'];
            }
            if (isset($_POST['crypto_amount'])) {
                // Ensure it's treated as a number
                $crypto_amount = (float)$_POST['crypto_amount']; 
            }
        } else {
            // Card processing
            if (isset($_POST['card_number'])) {
                $card_number = $_POST['card_number'];
            }
            if (isset($_POST['card_name'])) {
                $card_name = $_POST['card_name'];
            }
            if (isset($_POST['expiry_date'])) {
                $expiry_date = $_POST['expiry_date'];
            }
            if (isset($_POST['cvv'])) {
                $cvv = $_POST['cvv'];
            }
        }

        // validation
        if (empty($payment_method)) {
            echo "<script>alert('Please select a payment method.');</script>";
        }
        if (($payment_method == 'Bitcoin' || $payment_method == 'Monero')) {
            if (empty($wallet_address)) {
            echo "<script>alert('Please enter your wallet address.');</script>";
            }
        } else {
            if (empty($card_number) || !preg_match('/^\d{16}$/', $card_number)) {
                echo "<script>alert('Please enter a valid 16-digit card number.');</script>";
            }
            if (empty($card_name)) {
                echo "<script>alert('Please enter the cardholder name.');</script>";;
            }
            if (empty($expiry_date) || !preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry_date)) {
                echo "<script>alert('Please enter a valid expiry date in MM/YY format.');</script>";
            }
            if (empty($cvv) || !preg_match('/^\d{3,4}$/', $cvv)) {
                echo "<script>alert('Please enter a valid CVV.');</script>";
            }
        }
            
        
        // Execute Payment
        if (CreatePayment($booking_id, $payment_method, $card_number, $card_name, $expiry_date, $cvv, $wallet_address, $crypto_amount, $total_amount, 'Completed')) {
            foreach ($selected_seats as $seat_id) {
                $seat = GetSeatByID($seat_id); 
                CreateTicket($booking_id, $seat_id, $seat['seat_type'], $seat['price']); 
            }
            
            header("Location: PaymentMiniP.php?success=1");
            exit();
        } else {
             // Handle payment failure
             echo "<script>alert('Payment failed. Please try again.');</script>";
        }
    }
}

// Get payment methods from database
$crypto_options = GetPaymentMethodsByType('crypto');
$card_options = GetPaymentMethodsByType('card');
// --- No further changes needed in HTML/CSS ---
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - TZA Theatre Zenith Atrium</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // --- Tailwind Configuration (Cinematic Red Theme) ---
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            500: '#a40000', // CINEMATIC RED 
                            600: '#8e0000', 
                            700: '#730000', 
                        },
                        darkbg: '#0a0a0a', // Near-black background
                        cardbg: '#181818', // Slightly lighter for card contrast
                        lightcard: '#f0f0f0', 
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        /* --- Global Transitions --- */
        * {
            transition: background-color 0.5s ease-in-out, color 0.5s ease-in-out, border-color 0.5s ease-in-out, box-shadow 0.5s ease-in-out, transform 0.3s ease-in-out;
        }

        /* Light mode body background */
        body {
            background-color: #ffffff;
        }

        .dark body {
            background-color: #000000;
        }

        /* Glass morphism effect */
        .glass-container {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
        }

        .dark .glass-container {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(164, 0, 0, 0.2);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }
        
        /* Input and Textarea Styling (Custom for Glass/Dark theme) */
        .glass-input {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: #1f2937;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .dark .glass-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .glass-input:focus {
            outline: none;
            border-color: #a40000;
            box-shadow: 0 0 0 3px rgba(164, 0, 0, 0.3);
        }

        /* Button styling */
        .select-btn {
            background: #a40000;
            border: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
        }

        .select-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(164, 0, 0, 0.4);
            background: #8e0000;
        }

        .select-btn:active {
            transform: translateY(0);
        }

        /* Secondary button styling */
        .secondary-btn {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: #1f2937;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
        }

        .dark .secondary-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .secondary-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.9);
        }

        .dark .secondary-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        
        /* Radio button style (Hidden native radio and custom replacement) */
        .custom-radio input[type="radio"] {
            display: none;
        }
        .custom-radio span {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 500;
            background: rgba(0, 0, 0, 0.05); /* Light mode subtle background */
        }
        .dark .custom-radio span {
            background: rgba(255, 255, 255, 0.05); /* Dark mode subtle background */
        }
        .custom-radio span:hover {
            border-color: rgba(164, 0, 0, 0.5);
            transform: translateY(-1px);
        }
        .custom-radio input[type="radio"]:checked + span {
            background-color: #a40000; /* Primary color background when checked */
            color: white;
            border-color: #a40000;
            box-shadow: 0 5px 15px rgba(164, 0, 0, 0.3);
        }
        .dark .custom-radio input[type="radio"]:checked + span {
             background-color: #a40000;
             color: white;
        }
        
        /* Clean typography */
        .form-label {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
        }

        .dark .form-label {
            color: white;
        }

        .form-text {
            font-family: 'Inter', sans-serif;
            font-weight: 400;
        }

        .dark .form-text {
            color: rgba(255, 255, 255, 0.9);
        }

        .heading {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
        }

        .dark .heading {
            color: white;
        }
    </style>
</head>
<body class="min-h-screen font-sans flex flex-col">
<script>
    // Theme initialization - must run immediately before page render
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
</script>

    <div class="absolute inset-0 z-0">
        <img src="https://i.pinimg.com/1200x/f0/b0/c3/f0b0c339e09dfaa74f7c8f68b94a5ce3.jpg" 
            alt="Cinema Background" 
            class="w-full h-full object-cover opacity-30 dark:opacity-60"
            style="mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0.8) 70%, rgba(0,0,0,0) 100%); -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0.8) 70%, rgba(0,0,0,0) 100%);">
        <div class="absolute inset-0 bg-gradient-to-br from-white/50 via-white/30 to-white/50 dark:from-black/70 dark:via-black/50 dark:to-black/70"></div>
    </div>

    <nav class="sticky top-0 z-50 shadow-2xl border-b border-gray-300
                 bg-white/90 backdrop-blur-md
                 dark:bg-darkbg/95 dark:border-primary-700/50 dark:shadow-none">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20"> 
                <div class="flex items-center space-x-3">
                    <span class="text-2xl font-bold text-gray-900 dark:text-white heading tracking-wider">Theatre Zenith Atrium</span>
                </div>

                <div class="flex items-center space-x-4">
                    <button id="theme-toggle" title="Toggle Dark Mode" class="p-3 rounded-full bg-gray-200 dark:bg-cardbg text-gray-700 dark:text-gray-400 hover:text-primary-500 dark:hover:text-primary-500 transition-colors duration-300 shadow-md">
                        <i class="fas fa-moon dark:hidden text-xl"></i>
                        <i class="fas fa-sun hidden dark:block text-xl"></i>
                    </button>
                    
                    <div class="flex items-center space-x-3 group relative">
                        <div class="hidden md:block text-right">
                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-xs"> 
                                <?php echo htmlspecialchars($_SESSION['email_address'] ?? 'Guest User'); ?>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-primary-500 transition-colors">
                                <a href="LogoutMiniP.php" class="hover:underline transition-colors">Sign Out</a>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold text-lg shadow-lg ring-2 ring-primary-500">
                                <?php echo strtoupper(substr($_SESSION['email_address'] ?? 'G', 0, 1)); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="relative z-10 py-12 flex-grow">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-black rounded-xl p-8 shadow-2xl transition-shadow status-card-glow hover:scale-[1.005] mb-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <?php if ($payment_success) { ?>
                            <h2 class="text-3xl heading text-primary-500 flex items-center">
                                <i class="fas fa-check-circle mr-3"></i> Payment Successful!
                            </h2>
                            <p class="text-md text-gray-600 dark:text-gray-300 mt-1 form-text">
                                Your tickets are confirmed. <br> Thank you for your booking!
                            </p>
                        <?php } else { ?>
                            <h2 class="text-3xl heading text-gray-900 dark:text-white">
                                Complete Your Payment
                            </h2>
                            <p class="text-md text-gray-600 dark:text-gray-300 mt-1 form-text">
                                Select a method and enter your details to finalize your booking.
                            </p>
                        <?php } ?>
                    </div>
                    
                    <?php if (!$payment_success) { ?>
                        <div class="text-right">
                            <p class="text-lg text-gray-600 dark:text-white/70 form-text">Total Amount</p>
                            <h3 class="text-4xl heading text-primary-500">
                                RM<?php echo number_format($total_amount, 2); ?>
                            </h3>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="glass-container rounded-2xl p-8">
                <?php if ($payment_success) { ?>
                    <div class="text-center py-10">
                        <p class="text-xl text-gray-700 dark:text-white form-text mb-6">
                            You can now proceed to review your complete ticket details.
                        </p>
                        <a href="ReviewMiniP.php?booking_id=<?php echo $booking_id; ?>" 
                            class="inline-block py-3 px-6 rounded-lg select-btn font-semibold text-lg transform hover:scale-[1.02] transition-transform">
                            <i class="fas fa-file-invoice mr-2"></i> Continue to Review
                        </a>
                    </div>
                <?php } else { ?>
                    <form method="post">
                        
                        <div class="pb-6 mb-8 border-b border-primary-500/50">
                            <h3 class="text-2xl heading text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-credit-card text-primary-500 mr-3"></i> Choose Payment Method
                            </h3>

                            <p class="form-label text-lg text-gray-700 dark:text-white/90 mb-3 mt-6">Cryptocurrencies:</p>
                            <div class="flex flex-wrap gap-4 mb-6">
                                <?php foreach ($crypto_options as $crypto): ?>
                                    <label class="custom-radio form-text text-gray-700 dark:text-gray-300">
                                        <input type="radio" name="payment_method" value="<?php echo htmlspecialchars($crypto['method_name']); ?>" <?php echo ($current_choice == $crypto['method_name']) ? 'checked' : ''; ?>>
                                        <span><?php echo htmlspecialchars($crypto['method_name']); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            
                            <p class="form-label text-lg text-gray-700 dark:text-white/90 mb-3 mt-6">Credit/Debit Cards:</p>
                            <div class="flex flex-wrap gap-4 mb-6">
                                <?php foreach ($card_options as $card): ?>
                                    <label class="custom-radio form-text text-gray-700 dark:text-gray-300">
                                        <input type="radio" name="payment_method" value="<?php echo htmlspecialchars($card['method_name']); ?>" <?php echo ($current_choice == $card['method_name']) ? 'checked' : ''; ?>>
                                        <span><?php echo htmlspecialchars($card['method_name']); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>

                            <?php if ($current_choice == '') { ?>
                                <input type="hidden" name="action" value="select">
                                <button type="submit" class="w-full py-3 mt-6 rounded-lg select-btn font-semibold text-lg transform hover:scale-[1.005] transition-transform">
                                    Select Payment Method
                                </button>
                            <?php } ?>
                        </div>
                        
                        <?php if ($current_choice != '') { ?>
                            <div class="mt-8 p-6 rounded-xl border border-gray-300 dark:border-primary-500/30 bg-white/60 dark:bg-black/30">
                                <h3 class="text-xl heading text-gray-900 dark:text-white mb-6 flex items-center">
                                    <i class="fas fa-money-bill-wave text-primary-500 mr-3"></i> Pay with <?php echo htmlspecialchars($current_choice); ?>
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                    <?php if ($current_choice == 'Bitcoin' || $current_choice == 'Monero') { ?>
                                        
                                        <div>
                                            <label class="form-label text-gray-700 dark:text-white block mb-2">Wallet Address:</label>
                                            <input type="text" 
                                            name="wallet_address" 
                                            class="glass-input w-full form-text" 
                                            required
                                            placeholder="Enter your wallet address"
                                            minlength="10"
                                            title="Please enter a valid wallet address.">
                                        </div>
                                        <div>
                                            <label class="form-label text-gray-700 dark:text-white block mb-2">Amount (RM):</label>
                                            <input type="text" name="crypto_amount" class="glass-input w-full form-text" value="<?php echo number_format($total_amount, 2); ?>" readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 form-text">Note: The system simulates a successful payment. In a real-world application, this would involve a cryptocurrency payment gateway.</p>
                                        </div>

                                    <?php } else if ($current_choice == 'Visa' || $current_choice == 'MasterCard' || $current_choice == 'American Express') { ?>
                            
                                        <div class="md:col-span-2">
                                            <label class="form-label text-gray-700 dark:text-white block mb-2">Card Number:</label>
                                            <input type="text"
                                            name="card_number"
                                            class="glass-input w-full form-text"
                                            placeholder="XXXX XXXX XXXX XXXX"
                                            required
                                            pattern="\d{16}"
                                            title="Please enter a valid 16-digit card number.">
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="form-label text-gray-700 dark:text-white block mb-2">Cardholder Name:</label>
                                            <input type="text"
                                            name="card_name"
                                            class="glass-input w-full form-text"
                                            placeholder="Full Name on Card" 
                                            required
                                            pattern="[A-Za-z\s]+"
                                            title="Please enter a valid name.">
                                        </div>

                                        <div>
                                            <label class="form-label text-gray-700 dark:text-white block mb-2">Expiry Date (MM/YY):</label>
                                            <input type="text" 
                                            name="expiry_date" 
                                            class="glass-input w-full form-text" 
                                            placeholder="MM/YY" 
                                            required
                                            pattern="^(0[1-9]|1[0-2])\/\d{2}$"
                                            title="Please enter a valid expiry date in MM/YY format.">
                                        </div>

                                        <div>
                                            <label class="form-label text-gray-700 dark:text-white block mb-2">CVV:</label>
                                            <input type="text" 
                                            name="cvv" 
                                            class="glass-input w-full form-text" 
                                            placeholder="XXX" 
                                            required
                                            pattern="\d{3}"
                                            title="Please enter a valid CVV.">
                                        </div>

                                    <?php } ?>
                                </div>

                                <input type="hidden" name="action" value="pay">
                                <input type="hidden" name="payment_method" value="<?php echo htmlspecialchars($current_choice); ?>">
                                <button type="submit" class="w-full py-4 mt-8 rounded-lg select-btn font-semibold text-lg transform hover:scale-[1.005] transition-transform">
                                    <i class="fas fa-lock mr-2"></i> Pay RM<?php echo number_format($total_amount, 2); ?> Now
                                </button>
                            </div>
                        <?php } ?>
                        
                    </form>
                <?php } ?>
            </div>
        </div>
    </main>

    <footer class="relative z-10 bg-white/90 backdrop-blur-md border-t border-gray-300 dark:bg-black/80 dark:border-gray-800 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-4 md:mb-0">
                    <span class="text-xl font-bold text-gray-900 dark:text-white heading"> Theatre Zenith Atrium</span>
                </div>
                
                <div class="text-center md:text-right">
                    <p class="text-gray-600 dark:text-gray-400 text-sm form-text">© 2023 Theatre Zenith Atrium. All rights reserved.</p>
                    <p class="text-gray-500 dark:text-gray-500 text-xs mt-1 form-text">Design inspired by modern cinematic interfaces.</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // --- Theme Toggle Functionality ---
        const themeToggle = document.getElementById('theme-toggle');
        const htmlElement = document.documentElement;
        
        // Toggle Handler
        themeToggle.addEventListener('click', function() {
            const isCurrentlyDark = htmlElement.classList.toggle('dark');
            
            if (isCurrentlyDark) {
                localStorage.setItem('color-theme', 'dark');
            } else {
                localStorage.setItem('color-theme', 'light');
            }
        });
    </script>
</body>
</html>
