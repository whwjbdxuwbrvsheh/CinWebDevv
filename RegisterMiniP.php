<?php 
require 'ConnMiniP.php';   

// Handle form submission
if($_POST) {   
    $full_name = $_POST['full_name'];
    $mobile_number = $_POST['mobile_number'];
    $email_address = $_POST['email_address'];
    $password = $_POST['password'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $race = $_POST['race'];
    $profession = $_POST['profession'];
    $location = $_POST['location'];

    // Register the user
    if (register($full_name, $mobile_number, $email_address, $password, $date_of_birth, $gender, $race, $profession, $location)) {
        echo "<script>alert('Registration successful!');</script>";
    } else {
        echo "<script>alert('Registration failed!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TZA Theatre Zenith Atrium</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            transition: background-color 0.5s ease-in-out, color 0.5s ease-in-out, border-color 0.5s ease-in-out, box-shadow 0.5s ease-in-out, transform 0.3s ease-in-out;
        }

        body {
            background-color: #000000;
        }

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

        .input-field, .select-box {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .input-field:focus, .select-box:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(164, 0, 0, 0.5);
            outline: none;
            transform: scale(1.02);
        }

        .input-field::placeholder {
            color: rgba(255, 255, 255, 0.6);
            font-family: 'Inter', sans-serif;
        }

        .register-btn {
            background: #a40000;
            border: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
        }

        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(164, 0, 0, 0.4);
            background: #8e0000;
        }

        .register-btn:active {
            transform: translateY(0);
        }

        .login-link {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            position: relative;
            font-family: 'Inter', sans-serif;
        }

        .login-link:hover {
            color: #ff6b6b;
        }

        .login-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background: #ff6b6b;
            transition: width 0.3s ease;
        }

        .login-link:hover::after {
            width: 100%;
        }

        input[type="radio"] {
            appearance: none;
            border: 2px solid rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            cursor: pointer;
            position: relative;
            transition: 0.25s;
        }

        input[type="radio"]:checked {
            border-color: #a40000;
        }

        input[type="radio"]:checked::before {
            content: '';
            position: absolute;
            top: 4px; 
            left: 4px;
            width: 8px; 
            height: 8px;
            background: #a40000;
            border-radius: 50%;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1) brightness(2);
            opacity: 0.9;
            cursor: pointer;
            transition: transform 0.2s, opacity 0.2s;
        }

        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            transform: scale(1.2);
            opacity: 1;
        }

        .custom-select { 
            position: relative; 
            width: 100%; 
        }

        .select-box { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            cursor: pointer; 
            padding: 15px;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
        }

        .select-arrow { 
            width: 18px; 
            height: 18px; 
            fill: #ccc; 
            transition: 0.3s; 
        }

        .select-box:hover .select-arrow { 
            fill: #fff; 
            transform: rotate(180deg); 
        }

        .options {
            position: absolute;
            width: 100%;
            margin-top: 5px;
            padding: 0;
            list-style: none;
            background: rgba(19, 19, 19, 0.95);
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
            display: none;
            z-index: 10;
            max-height: 220px;
            overflow-y: auto;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .options li {
            padding: 12px 15px;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
            color: white;
            font-family: 'Inter', sans-serif;
        }

        .options li:hover {
            background: rgba(164, 0, 0, 0.2);
            transform: scale(1.01);
        }

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

        .form-label {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            color: white;
        }

        .form-text {
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.9);
        }

        .heading {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            color: white;
        }
    </style>
</head>
<body class="bg-black text-white min-h-screen font-sans flex items-center justify-center">
    
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

    <!-- Main Content - Single Centered Container -->
    <div class="relative z-10 w-full max-w-4xl mx-4">
        <div class="glass-container rounded-2xl overflow-hidden animate-fade-in-up border border-white/10 p-12">
            
            <!-- Header -->
            <div class="text-center mb-8">
                <h2 class="text-3xl heading mb-2">
                    Create Account
                </h2>
                <p class="text-white/70 form-text">
                    Join Theatre Zenith Atrium
                </p>
            </div>

            <!-- Registration Form -->
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-white/80 text-sm form-label">Full Name</label>
                        <input type="text" 
                               name="full_name" 
                               placeholder="Enter your full name" 
                               required
                               class="w-full px-4 py-3 rounded-lg input-field text-white placeholder-white/60">
                    </div>

                    <div class="space-y-2">
                        <label class="text-white/80 text-sm form-label">Mobile Number</label>
                        <input type="tel" 
                               name="mobile_number" 
                               placeholder="Enter your mobile number" 
                               required
                               class="w-full px-4 py-3 rounded-lg input-field text-white placeholder-white/60">
                    </div>

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
                               placeholder="Create a password" 
                               required
                               class="w-full px-4 py-3 rounded-lg input-field text-white placeholder-white/60">
                    </div>

                    <div class="space-y-2">
                        <label class="text-white/80 text-sm form-label">Date of Birth</label>
                        <input type="date" 
                               name="date_of_birth" 
                               required
                               class="w-full px-4 py-3 rounded-lg input-field text-white">
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-white/80 text-sm form-label">Gender</label>
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center space-x-2 text-white form-text">
                                <input type="radio" name="gender" value="Male" required>
                                <span>Male</span>
                            </label>
                            <label class="flex items-center space-x-2 text-white form-text">
                                <input type="radio" name="gender" value="Female">
                                <span>Female</span>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-white/80 text-sm form-label">Race</label>
                        <div class="custom-select">
                            <div class="select-box" onclick="toggleDropdown(this)">
                                <span class="form-text">Select Race</span>
                                <svg class="select-arrow" viewBox="0 0 20 20"><path d="M5 7l5 5 5-5H5z"/></svg>
                            </div>
                            <ul class="options">
                                <li>Malay</li><li>Chinese</li><li>Indian</li><li>Other</li>
                            </ul>
                            <input type="hidden" name="race">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-white/80 text-sm form-label">Profession</label>
                        <div class="custom-select">
                            <div class="select-box" onclick="toggleDropdown(this)">
                                <span class="form-text">Select Profession</span>
                                <svg class="select-arrow" viewBox="0 0 20 20"><path d="M5 7l5 5 5-5H5z"/></svg>
                            </div>
                            <ul class="options">
                                <li>Student</li><li>Corporate</li><li>Business Owner</li><li>Unemployed</li><li>Others</li>
                            </ul>
                            <input type="hidden" name="profession">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-white/80 text-sm form-label">Location</label>
                        <div class="custom-select">
                            <div class="select-box" onclick="toggleDropdown(this)">
                                <span class="form-text">Select Location</span>
                                <svg class="select-arrow" viewBox="0 0 20 20"><path d="M5 7l5 5 5-5H5z"/></svg>
                            </div>
                            <ul class="options">
                                <li>Johor</li><li>Kedah</li><li>Kelantan</li><li>Melaka</li><li>Negeri Sembilan</li><li>Pahang</li><li>Penang</li><li>Perak</li><li>Perlis</li><li>Sabah</li><li>Sarawak</li><li>Selangor</li><li>Terengganu</li><li>Kuala Lumpur</li><li>Putrajaya</li><li>Labuan</li>
                            </ul>
                            <input type="hidden" name="location">
                        </div>
                    </div>
                </div>

                <button type="submit" 
                        class="col-span-2 w-full py-4 rounded-lg register-btn text-lg font-semibold mt-4 transform hover:scale-[1.02] transition-transform">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Account
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-white/70 form-text">
                    Already have an account? 
                    <a href="LoginMiniP.php" class="login-link font-semibold ml-1">
                        Sign In Here
                    </a>
                </p>
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

        // Dropdown functionality
        function toggleDropdown(el) {
            const dropdown = el.nextElementSibling;
            const all = document.querySelectorAll('.options');
            all.forEach(opt => opt !== dropdown && (opt.style.display = 'none'));
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        document.addEventListener('click', e => {
            if (!e.target.closest('.custom-select')) {
                document.querySelectorAll('.options').forEach(opt => opt.style.display = 'none');
            }
        });

        document.querySelectorAll('.options li').forEach(li => {
            li.addEventListener('click', e => {
                const customSelect = e.target.closest('.custom-select');
                const box = customSelect.querySelector('.select-box span');
                const hiddenInput = customSelect.querySelector('input[type="hidden"]');
                
                box.textContent = e.target.textContent;
                hiddenInput.value = e.target.textContent;
                e.target.parentElement.style.display = 'none';
            });
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
