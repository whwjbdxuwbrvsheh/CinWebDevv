<?php
session_start();
require 'ConnMiniP.php';

// Ensure user is logged in
if (!isset($_SESSION['email_address'])) {
    header("Location: LoginMiniP.php");
    exit();
}

$email = $_SESSION['email_address'];
$sql_user = "SELECT * FROM users WHERE email_address = '$email'";
$result = $conn->query($sql_user);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile | Theatre Zenith Atrium</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            primary: {
              500: '#a40000',
              600: '#8e0000',
              700: '#730000',
            },
            darkbg: '#0a0a0a',
            cardbg: '#181818',
            lightcard: '#f0f0f0',
          },
          fontFamily: {
            sans: ['Inter', 'system-ui', 'sans-serif'],
          },
        },
      },
    }
  </script>
</head>

<body class="bg-white text-gray-900 dark:bg-darkbg dark:text-white min-h-screen font-sans">

<!-- Navbar (exact copy from index.php) -->
<nav class="sticky top-0 z-50 shadow-2xl border-b border-gray-900 
           bg-white/90 backdrop-blur-md
           dark:bg-darkbg/95 dark:border-primary-700/50 dark:shadow-none">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-20"> 
      <div class="flex items-center space-x-3">
        <span class="text-2xl font-bold text-gray-900 dark:text-white heading tracking-wider">Theatre Zenith Atrium</span>
      </div>

      <div class="flex items-center space-x-4">
        <a href="IndexMiniP.php" class="text-gray-700 dark:text-gray-300 hover:text-primary-500 transition">Home</a>
        <a href="ProfileMiniP.php" class="text-primary-500 font-semibold">Profile</a>

        <button id="theme-toggle" title="Toggle Dark Mode" 
                class="p-3 rounded-full bg-gray-200 dark:bg-cardbg text-gray-700 dark:text-gray-400 hover:text-primary-500 dark:hover:text-primary-500 transition-colors duration-300 shadow-md">
          <i class="fas fa-moon dark:hidden text-xl"></i>
          <i class="fas fa-sun hidden dark:block text-xl"></i>
        </button>
      </div>
    </div>
  </div>
</nav>

<!-- Hero Banner -->
<section class="relative h-72 overflow-hidden bg-gray-900">
  <div class="absolute inset-0">
    <img src="https://i.pinimg.com/1200x/f0/b0/c3/f0b0c339e09dfaa74f7c8f68b94a5ce3.jpg" 
         class="w-full h-full object-cover opacity-70 dark:opacity-50" />
  </div>
  <div class="absolute inset-0 bg-gradient-to-t from-darkbg/95 via-darkbg/60 to-transparent z-10"></div>
  <div class="relative z-20 h-full flex flex-col justify-end pb-10 px-8">
    <h1 class="text-5xl font-bold text-white heading">Your Profile</h1>
    <p class="text-gray-300 text-lg mt-2">View and manage your account details.</p>
  </div>
</section>

<!-- Profile Section -->
<main class="py-16">
  <div class="max-w-4xl mx-auto px-6">
    <div class="bg-lightcard dark:bg-cardbg rounded-2xl shadow-2xl p-10 border border-gray-200 dark:border-primary-500/40">
      <div class="flex flex-col items-center text-center space-y-6">
        <div class="h-28 w-28 rounded-full bg-primary-500 text-white flex items-center justify-center text-4xl font-bold shadow-lg ring-4 ring-primary-500/40">
          <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($user['full_name']) ?></h2>
        <p class="text-gray-500 dark:text-gray-400"><?= htmlspecialchars($user['email_address']) ?></p>
      </div>

      <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 gap-6 text-gray-700 dark:text-gray-300">
        <div>
          <h3 class="font-semibold text-primary-500 dark:text-primary-400">Mobile Number</h3>
          <p><?= htmlspecialchars($user['mobile_number']) ?></p>
        </div>
        <div>
          <h3 class="font-semibold text-primary-500 dark:text-primary-400">Date of Birth</h3>
          <p><?= htmlspecialchars($user['date_of_birth']) ?></p>
        </div>
        <div>
          <h3 class="font-semibold text-primary-500 dark:text-primary-400">Gender</h3>
          <p><?= htmlspecialchars($user['gender']) ?></p>
        </div>
      </div>

      <div class="mt-10 text-center">
        <a href="EditProfileMiniP.php" 
           class="inline-block bg-primary-500 hover:bg-primary-600 text-white font-semibold px-6 py-3 rounded-lg shadow-lg transition transform hover:scale-105">
          ✏️ Edit Profile
        </a>
      </div>
    </div>
  </div>
</main>

<!-- Footer -->
<footer class="bg-gray-100 dark:bg-cardbg border-t border-gray-200 dark:border-primary-700/50 mt-12">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row justify-between items-center">
      <span class="text-xl font-bold text-gray-900 dark:text-white heading">Theatre Zenith Atrium</span>
      <p class="text-gray-500 dark:text-gray-400 text-sm form-text">© <?= date('Y') ?> Theatre Zenith Atrium. All rights reserved.</p>
    </div>
  </div>
</footer>

<script>
  // --- Dark Mode Toggle ---
  const themeToggle = document.getElementById('theme-toggle');
  const html = document.documentElement;
  const isDark = localStorage.getItem('color-theme') === 'dark' ||
                 (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
  if (isDark) html.classList.add('dark');

  themeToggle.addEventListener('click', () => {
    html.classList.toggle('dark');
    localStorage.setItem('color-theme', html.classList.contains('dark') ? 'dark' : 'light');
  });
</script>
</body>
</html>
