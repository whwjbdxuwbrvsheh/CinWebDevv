<?php 
session_start(); 
require 'ConnMiniP.php'; 

if($_POST) { 
    $email_address = $_POST['email_address']; 
    $password = $_POST['password']; 
    
    $result = login($email_address, $password); 
    
    if($result){ 
        $_SESSION['user_id'] = $result['user_id'];  // ✅ FIXED! Use $result instead of $user
        $_SESSION['email_address'] = $result['email_address']; 
        header("Location: IndexMiniP.php"); 
        exit(); 
    } else { 
        echo "<script>alert('Login Unsuccessful');</script>"; 
    } 
} 
?>

<html>
<head>
    <title>Login</title>
    <style>
        /*
        * 1. Global Reset and Background
        */
        body {
            /* Set the custom background image */
            background-image: url('https://i.pinimg.com/1200x/59/ec/a7/59eca7aafe53bb2b91466b48f57fa731.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: 'Saira Semi Condensed', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #fff;
            overflow: hidden;
        }

                /* Title Styling */
        h1 {
            font-family: 'Manrope', 'Urbanist', 'Nunito', sans-serif;
            font-size: 2.5em;
            font-weight: 700;
            letter-spacing: 1px;
            color: #ffffff;
            margin: 0;
            text-shadow: 
                0 0 10px rgba(255, 255, 255, 0.4),
                0 0 30px rgba(255, 0, 0, 0.2); /* red tint to match theme */
            transition: transform 0.4s ease, text-shadow 0.4s ease;
        }

        /* Hover animation (optional subtle float) */
        h1:hover {
            transform: scale(1.03);
            text-shadow: 
                0 0 20px rgba(255, 255, 255, 0.8),
                0 0 40px rgba(255, 0, 0, 0.5);
        }

        /* Subtitle Styling */
        .subtitle {
            font-family: 'Inter', 'Roboto', sans-serif;
            font-size: 1em;
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.5;
            margin-top: 10px;
            letter-spacing: 0.3px;
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.2);
        }

        .left-panel h1, .left-panel .subtitle {
            opacity: 0;
            transform: translateY(10px);
            animation: fadeUp 0.8s ease forwards;
        }

        .left-panel .subtitle {
            animation-delay: 0.2s;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /*
        * 2. Main Container for the Two-Panel Layout (Large and Prominent)
        */
        .main-glass-container {
            position: relative;
            background-color: transparent;
            border-radius: 20px;
            box-shadow: 0 10px 75px 0 rgba(0, 0, 0, 1);
            border: none; 
            display: flex;
            width: 95%; 
            max-width: 1000px; 
            min-height: 650px; 
            overflow: hidden; 
        }

        /*
        * 3. Left Panel (Form Side - Darkest Smoky Gray, NO OUTLINE, 2/5 of width)
        */
        .left-panel {
            /* KEY CHANGE: 2 units of 5 for the login part */
            flex: 2; 
            background-color: rgba(15, 15, 15, 0.88); 
            backdrop-filter: blur(1px);
            -webkit-backdrop-filter: blur(1px);
            padding: 50px; 
            display: flex;
            flex-direction: column;
            justify-content: center;
            
            border: none; 
            border-radius: 20px 0 0 20px;
        }

        /*
        * 4. Form Styling (Inputs & Button)
        */
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 15px 15px;
            border: none;
            border-radius: 8px;
            background-color: rgba(19, 19, 19, 0.62); 
            color: #ffffff;
            font-size: 1em;
            box-sizing: border-box;
            transition: background-color 0.3s, box-shadow 0.4s ease;

            box-shadow: 0 0 8px rgba(255, 255, 255, 0.2);
        }

        /* when user is typing or autofilled */
        input[type="email"]:not(:placeholder-shown),
        input[type="password"]:not(:placeholder-shown) {
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.7),
                        0 0 25px rgba(255, 255, 255, 0.3);
        }

        /* when focused */
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            background-color: rgba(25, 25, 25, 0.8);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.7),
                        0 0 25px rgba(255, 255, 255, 0.3);
        }



        input[type="submit"] {
            background-color: #ffffff;
            color: #000000;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: 
                background-color 0.3s ease,
                transform 0.15s ease,
                box-shadow 0.3s ease;
            margin-top: 20px;
        }

        /* when hovered */
        input[type="submit"]:hover {
            transform: scale(1.07); /* slightly bigger */
            background-color: #f2f2f2; /* subtle shade change */
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.4); /* glow */
        }

        /* when clicked */
        input[type="submit"]:active {
            transform: scale(0.97); /* quick press effect */
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.2);
        }

        /* when focused (keyboard nav) */
        input[type="submit"]:focus {
            outline: none;
            box-shadow: 0 0 12px rgba(255, 255, 255, 0.5);
        }


        input[type="submit"]:hover {
            background-color: #f0f0f0;
        }

        /* Register text container */
        .register-link-group {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9em;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Link itself */
        .register-link-group a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            position: relative;
            transition: 
                color 0.3s ease,
                text-shadow 0.3s ease,
                transform 0.2s ease;
        }

        /* Hover effect — glow + slight lift */
        .register-link-group a:hover {
            color: #a40000ff; /* bright cyan accent fits glass theme */
            text-shadow: 0 0 8px rgba(185, 2, 2, 0.97);
            transform: scale(1.05);
        }

        /* Optional underline animation */
        .register-link-group a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 0%;
            height: 2px;
            background: #ff0000e8;
            transition: width 0.3s ease;
        }

        /* Expand underline on hover */
        .register-link-group a:hover::after {
            width: 100%;
        }


        /*
        * 5. Right Panel (Aesthetic Side - Crystal Clear Glass, subtle OUTLINE, 3/5 of width)
        */
        .right-panel {
            /* KEY CHANGE: 3 units of 5 for the glass part */
            flex: 3; 
            .clear-glass {
            background: rgba(255, 255, 255, 0.05);  /* almost transparent */
            backdrop-filter: blur(15px);            /* strong blur behind */
            -webkit-backdrop-filter: blur(15px);    /* Safari/Chrome support */
            border: 1px solid rgba(255, 255, 255, 0.2); /* very subtle border */
            border-radius: 10px;                     /* rounded corners */
            box-shadow: 0 4px 40px rgba(0, 0, 0, 0.05); /* optional soft shadow */
        
        }

            
            border-radius: 0 20px 20px 0; 
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-left: none; 
            box-shadow: inset 1px 0 10px rgba(255, 255, 255, 0.05); 
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .main-glass-container {
                flex-direction: column;
                min-height: auto;
                max-width: 500px;
                border-radius: 20px;
            }

            .right-panel {
                min-height: 200px;
                border-radius: 20px 20px 0 0;
                order: -1;
            }

            .left-panel {
                padding: 30px;
                border-radius: 0 0 20px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="main-glass-container">
        
        <div class="left-panel">
            <p class="logo-text"></p> 
            <h1>Login</h1>
            <p class="subtitle">Welcome back! Please enter your details to access your cinema account.</p><br>
            <form method="POST">
                <div class="input-group">
                    <input type="email" name="email_address" placeholder="Email Address" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <input type="submit" value="Login">
            </form>
            
            <p class="register-link-group">
                Don't have an account? <a href="RegisterMiniP.php">Register here</a>
            </p>
        </div>

        <div class="right-panel">
            </div>
    </div>
        <script src="following-dot-cursor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            followingDotCursor({
                color: "#804309d0",  // Dot color (semi-transparent dark gray)
                zIndex: "999999"
            });
        });
    </script>

</body>
</html>
