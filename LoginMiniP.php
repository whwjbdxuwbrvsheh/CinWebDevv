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


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Custom font and animation overrides */
    @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@700&family=Nunito:wght@600&family=Urbanist:wght@600&family=Inter&family=Saira+Semi+Condensed:wght@400;700&display=swap');

    body {
      background-image: url('https://i.pinimg.com/1200x/59/ec/a7/59eca7aafe53bb2b91466b48f57fa731.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      font-family: 'Saira Semi Condensed', 'Arial', sans-serif;
      color: #fff;
      overflow: hidden;
    }

    h1 {
      font-family: 'Manrope', 'Urbanist', 'Nunito', sans-serif;
      font-size: 2.5em;
      font-weight: 700;
      letter-spacing: 1px;
      color: #ffffff;
      margin: 0;
      text-shadow: 
        0 0 10px rgba(255, 255, 255, 0.4),
        0 0 30px rgba(255, 0, 0, 0.2);
      transition: transform 0.4s ease, text-shadow 0.4s ease;
    }

    h1:hover {
      transform: scale(1.03);
      text-shadow: 
        0 0 20px rgba(255, 255, 255, 0.8),
        0 0 40px rgba(255, 0, 0, 0.5);
    }

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

    input[type="email"],
    input[type="password"] {
      background-color: rgba(19, 19, 19, 0.62);
      color: #ffffff;
      box-shadow: 0 0 8px rgba(255, 255, 255, 0.2);
      transition: background-color 0.3s, box-shadow 0.4s ease;
    }

    input[type="email"]:focus,
    input[type="password"]:focus {
      background-color: rgba(25, 25, 25, 0.8);
      box-shadow: 0 0 15px rgba(255, 255, 255, 0.7),
                  0 0 25px rgba(255, 255, 255, 0.3);
      outline: none;
    }

    input[type="email"]:not(:placeholder-shown),
    input[type="password"]:not(:placeholder-shown) {
      box-shadow: 0 0 15px rgba(255, 255, 255, 0.7),
                  0 0 25px rgba(255, 255, 255, 0.3);
    }

    input[type="submit"] {
      transition: 
        background-color 0.3s ease,
        transform 0.15s ease,
        box-shadow 0.3s ease;
    }

    input[type="submit"]:hover {
      transform: scale(1.07);
      background-color: #f2f2f2;
      box-shadow: 0 0 15px rgba(255, 255, 255, 0.4);
    }

    input[type="submit"]:active {
      transform: scale(0.97);
      box-shadow: 0 0 8px rgba(255, 255, 255, 0.2);
    }

    input[type="submit"]:focus {
      outline: none;
      box-shadow: 0 0 12px rgba(255, 255, 255, 0.5);
    }

    .register-link-group a {
      color: #ffffff;
      position: relative;
      transition: 
        color 0.3s ease,
        text-shadow 0.3s ease,
        transform 0.2s ease;
    }

    .register-link-group a:hover {
      color: #a40000ff;
      text-shadow: 0 0 8px rgba(185, 2, 2, 0.97);
      transform: scale(1.05);
    }

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

    .register-link-group a:hover::after {
      width: 100%;
    }

    .clear-glass {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 10px;
      box-shadow: 0 4px 40px rgba(0, 0, 0, 0.05);
    }
  </style>
</head>
<body class="flex justify-center items-center min-h-screen">

  <div class="main-glass-container flex w-[95%] max-w-[1000px] min-h-[650px] rounded-2xl overflow-hidden shadow-[0_10px_75px_rgba(0,0,0,1)]">

    <!-- LEFT PANEL -->
    <div class="left-panel flex-[2] bg-[rgba(15,15,15,0.88)] backdrop-blur-sm p-12 flex flex-col justify-center rounded-l-2xl">
      <h1>Login</h1>
      <p class="subtitle">Welcome back! Please enter your details to access your cinema account.</p>
      <br>

      <form method="POST" class="flex flex-col gap-5">
        <input type="email" name="email_address" placeholder="Email Address" required
          class="w-full px-4 py-3 rounded-md border-none text-white text-base placeholder-gray-400">
        <input type="password" name="password" placeholder="Password" required
          class="w-full px-4 py-3 rounded-md border-none text-white text-base placeholder-gray-400">
        <input type="submit" value="Login"
          class="mt-5 bg-white text-black font-bold text-lg rounded-md py-3 cursor-pointer">
      </form>

      <p class="register-link-group text-center mt-8 text-sm text-[rgba(255,255,255,0.7)]">
        Don't have an account? <a href="RegisterMiniP.php">Register here</a>
      </p>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel flex-[3] flex justify-center items-center border border-[rgba(255,255,255,0.1)] border-l-0 shadow-[inset_1px_0_10px_rgba(255,255,255,0.05)] rounded-r-2xl">
    </div>

  </div>

  <script src="following-dot-cursor.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      followingDotCursor({
        color: "#804309d0",
        zIndex: "999999"
      });
    });
  </script>
</body>
</html>

