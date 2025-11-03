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
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
        }

        .dark .glass-container {
            background: rgba(0, 0, 0, 0.7);
            border: 1px solid rgba(164, 0, 0, 0.2);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }

        /* Input styling */
        .input-field, .select-box {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #1f2937;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .dark .input-field, .dark .select-box {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }

        .input-field:focus, .select-box:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(164, 0, 0, 0.5);
            outline: none;
            transform: scale(1.02);
        }

        .input-field::placeholder {
            color: rgba(107, 114, 128, 0.8);
            font-family: 'Inter', sans-serif;
        }

        .dark .input-field::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        /* Button styling */
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

        /* Link styling */
        .login-link {
            color: #4b5563;
            transition: all 0.3s ease;
            position: relative;
            font-family: 'Inter', sans-serif;
        }

        .dark .login-link {
            color: rgba(255, 255, 255, 0.9);
        }

        .login-link:hover {
            color: #a40000;
        }

        .login-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background: #a40000;
            transition: width 0.3s ease;
        }

        .login-link:hover::after {
            width: 100%;
        }

        /* Radio button styling */
        input[type="radio"] {
            appearance: none;
            border: 2px solid rgba(107, 114, 128, 0.6);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            cursor: pointer;
            position: relative;
            transition: 0.25s;
        }

        .dark input[type="radio"] {
            border: 2px solid rgba(255, 255, 255, 0.6);
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

        /* Date input styling */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.5);
            opacity: 0.9;
            cursor: pointer;
            transition: transform 0.2s, opacity 0.2s;
        }

        .dark input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1) brightness(2);
        }

        /* Custom dropdown styling */
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
            fill: #6b7280; 
            transition: 0.3s; 
        }

        .dark .select-arrow { 
            fill: #ccc; 
        }

        .select-box:hover .select-arrow { 
            fill: #a40000; 
            transform: rotate(180deg); 
        }

        .options {
            position: absolute;
            width: 100%;
            margin-top: 5px;
            padding: 0;
            list-style: none;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 10;
            max-height: 220px;
            overflow-y: auto;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .dark .options {
            background: rgba(0, 0, 0, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .options li {
            padding: 12px 15px;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
            color: #1f2937;
            font-family: 'Inter', sans-serif;
        }

        .dark .options li {
            color: white;
        }

        .options li:hover {
            background: rgba(164, 0, 0, 0.1);
            transform: scale(1.01);
        }

        .dark .options li:hover {
            background: rgba(164, 0, 0, 0.3);
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
            color: #374151;
        }

        .dark .form-label {
            color: white;
        }

        .form-text {
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            color: #6b7280;
        }

        .dark .form-text {
            color: rgba(255, 255, 255, 0.9);
        }

        .heading {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            color: #1f2937;
        }

        .dark .heading {
            color: white;
        }
    </style>
</head>
<body class="bg-white text-gray-900 dark:bg-darkbg dark:text-white min-h-screen font-sans flex items-center justify-center">
    
    <!-- Background Image with Subtle Overlay -->
    <div class="absolute inset-0 z-0">
        <img src="https://i.pinimg.com/1200x/f0/b0/c3/f0b0c339e09dfaa74f7c8f68b94a5ce3.jpg" 
             alt="Cinema Background" 
             class="w-full h-full object-cover opacity-60 dark:opacity-60">
        <div class="absolute inset-0 bg-white/40 dark:bg-black/70"></div>
    </div>

    <!-- Theme Toggle Button -->
    <div class="absolute top-6 right-6 z-50">
        <button id="theme-toggle" title="Toggle Dark Mode" class="p-3 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-gray-700 hover:text-primary-500 dark:bg-white/10 dark:border-white/20 dark:text-white dark:hover:text-primary-500 transition-all duration-300 shadow-lg hover:scale-110">
            <i class="fas fa-moon dark:hidden text-xl"></i>
            <i class="fas fa-sun hidden dark:block text-xl"></i>
        </button>
    </div>

    <!-- Main Content - Single Centered Container -->
    <div class="relative z-10 w-full max-w-4xl mx-4">
        <div class="glass-container rounded-2xl overflow-hidden animate-fade-in-up p-12">
            
            <!-- Header -->
            <div class="text-center mb-8">
                <h2 class="text-3xl heading mb-2">
                    Create Account
                </h2>
                <p class="form-text">
                    Join Theatre Zenith Atrium
                </p>
            </div>

            <!-- Registration Form -->
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-sm form-label">Full Name</label>
                        <input type="text" 
                               name="full_name" 
                               placeholder="Enter your full name" 
                               required
                               class="w-full px-4 py-3 rounded-lg input-field placeholder-gray-500 dark:placeholder-white/70">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm form-label">Mobile Number</label>
                        <input type="tel" 
                               name="mobile_number" 
                               placeholder="Enter your mobile number" 
                               required
                               class="w-full px-4 py-3 rounded-lg input-field placeholder-gray-500 dark:placeholder-white/70">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm form-label">Email Address</label>
                        <input type="email" 
                               name="email_address" 
                               placeholder="Enter your email" 
                               required
                               class="w-full px-4 py-3 rounded-lg input-field placeholder-gray-500 dark:placeholder-white/70">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm form-label">Password</label>
                        <input type="password" 
                               name="password" 
                               placeholder="Create a password" 
                               required
                               class="w-full px-4 py-3 rounded-lg input-field placeholder-gray-500 dark:placeholder-white/70">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm form-label">Date of Birth</label>
                        <input type="date" 
                               name="date_of_birth" 
                               required
                               class="w-full px-4 py-3 rounded-lg input-field">
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-sm form-label">Gender</label>
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center space-x-2 form-text">
                                <input type="radio" name="gender" value="Male" required>
                                <span>Male</span>
                            </label>
                            <label class="flex items-center space-x-2 form-text">
                                <input type="radio" name="gender" value="Female">
                                <span>Female</span>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm form-label">Race</label>
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
                        <label class="text-sm form-label">Profession</label>
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
                        <label class="text-sm form-label">Location</label>
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
                <p class="form-text">
                    Already have an account? 
                    <a href="LoginMiniP.php" class="login-link font-semibold ml-1">
                        Sign In Here
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="text-center mt-6">
            <p class="text-gray-600 dark:text-white/70 text-sm form-text">
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
