<?php 
session_start(); 
require 'ConnMiniP.php'; 

if($_POST) { 
    $email_address = $_POST['email_address']; 
    $password = $_POST['password']; 
    
    $result = login($email_address, $password); 
    
    if($result){ 
        $_SESSION['user_id'] = $result['user_id'];
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TZA Theatre Zenith Atrium</title>
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

        /* Glass morphism effect */
        .glass-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }

        .dark .glass-container {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(164, 0, 0, 0.2);
        }

        /* Input styling */
        .input-field {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .input-field:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(164, 0, 0, 0.5);
            outline: none;
            transform: scale(1.02);
        }

        .input-field::placeholder {
            color: rgba(255, 255, 255, 0.6);
            font-family: 'Inter', sans-serif;
        }

        /* Button styling */
        .login-btn {
            background: #a40000;
            border: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(164, 0, 0, 0.4);
            background: #8e0000;
        }

        .login-btn:active {
            transform: translateY(0);
        }

        /* Link styling */
        .register-link {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            position: relative;
            font-family: 'Inter', sans-serif;
        }

        .register-link:hover {
            color: #ff6b6b;
        }

        .register-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background: #ff6b6b;
            transition: width 0.3s ease;
        }

        .register-link:hover::after {
            width: 100%;
        }

        /* Background animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        /* Clean typography */
        .form-label {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
        }

        .form-text {
            font-family: 'Inter', sans-serif;
            font-weight: 400;
        }

        .heading {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-white text-gray-900 dark:bg-darkbg dark:text-white min-h-screen font-sans flex items-center justify-center">
    
    <!-- Background Image with Subtle Overlay -->
    <div class="absolute inset-0 z-0">
        <img src="https://i.pinimg.com/1200x/f0/b0/c3/f0b0c339e09dfaa74f7c8f68b94a5ce3.jpg" 
             alt="Cinema Background" 
             class="w-full h-full object-cover opacity-80 dark:opacity-60">
        <div class="absolute inset-0 bg-gradient-to-br from-black/70 via-black/50 to-black/70"></div>
    </div>

    <!-- Theme Toggle Button -->
    <div class="absolute top-6 right-6 z-50">
        <button id="theme-toggle" title="Toggle Dark Mode" class="p-3 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white hover:text-primary-500 dark:hover:text-primary-500 transition-all duration-300 shadow-lg hover:scale-110">
            <i class="fas fa-moon dark:hidden text-xl"></i>
            <i class="fas fa-sun hidden dark:block text-xl"></i>
        </button>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 w-full max-w-4xl mx-4">
        <div class="glass-container rounded-2xl overflow-hidden animate-fade-in-up border border-white/10">
            <div class="flex flex-col md:flex-row min-h-[600px]">
                
                <!-- Left Panel - Brand & Visual -->
                <div class="md:w-2/5 p-8 flex flex-col justify-center items-center text-center relative">
                    <!-- Subtle background image on left panel -->
                    <div class="absolute inset-0 z-0 opacity-20">
                        <img src="https://i.pinimg.com/1200x/f0/b0/c3/f0b0c339e09dfaa74f7c8f68b94a5ce3.jpg" 
                             alt="Background" 
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/80 to-transparent"></div>
                    </div>
                    
                    <div class="relative z-10">
                        <h1 class="text-2xl heading text-white mb-2">
                            Welcome Back
                        </h1>
                        <p class="text-white/70 form-text">
                            to Theatre Zenith Atrium
                        </p>
                    </div>
                </div>

                <!-- Right Panel - Login Form -->
                <div class="md:w-3/5 p-12 flex flex-col justify-center relative">
                    <!-- Subtle background on right panel -->
                    <div class="absolute inset-0 z-0 opacity-10">
                        <div class="w-full h-full bg-gradient-to-l from-primary-500/20 to-transparent"></div>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="mb-8">
                            <h2 class="text-3xl heading text-white mb-3">
                                Sign In
                            </h2>
                            <p class="text-white/70 form-text">
                                Enter your account details
                            </p>
                        </div>

                        <form method="POST" class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-white/80 text-sm form-label">Email Address</label>
                                <input type="email" 
                                       name="email_address" 
                                       placeholder="Enter your email" 
                                       required
                                       class="w-full px-4 py-3 rounded-lg input-field text-white placeholder-white/60">
                            </div>

                            <div class="space-y-2">
                                <label class="text-white/80 text-sm form-label">Password</label>
                                <input type="password" 
                                       name="password" 
                                       placeholder="Enter your password" 
                                       required
                                       class="w-full px-4 py-3 rounded-lg input-field text-white placeholder-white/60">
                            </div>

                            <button type="submit" 
                                    class="w-full py-4 rounded-lg login-btn text-lg font-semibold mt-6 transform hover:scale-[1.02] transition-transform">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Sign In
                            </button>
                        </form>

                        <div class="mt-8 text-center">
                            <p class="text-white/70 form-text">
                                Don't have an account? 
                                <a href="RegisterMiniP.php" class="register-link font-semibold ml-1">
                                    Create Account
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="text-center mt-6">
            <p class="text-white/50 text-sm form-text">
                © 2023 Theatre Zenith Atrium
            </p>
        </div>
    </div>

    <script>
        // --- Theme Toggle Functionality ---
        const themeToggle = document.getElementById('theme-toggle');
        const htmlElement = document.documentElement;
        
        // 1. Initial setup (Check localStorage or system preference)
        const isDarkMode = localStorage.getItem('color-theme') === 'dark' || 
                            (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

        if (isDarkMode) {
            htmlElement.classList.add('dark');
        } else {
            htmlElement.classList.remove('dark');
        }
        
        // 2. Toggle Handler
        themeToggle.addEventListener('click', function() {
            const isCurrentlyDark = htmlElement.classList.toggle('dark');
            
            if (isCurrentlyDark) {
                localStorage.setItem('color-theme', 'dark');
            } else {
                localStorage.setItem('color-theme', 'light');
            }
        });

        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.input-field');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('transform', 'scale-105');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('transform', 'scale-105');
                });
            });
        });
    </script>
</body>
</html>
