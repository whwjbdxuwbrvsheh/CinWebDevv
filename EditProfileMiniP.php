<?php
session_start();
require 'ConnMiniP.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // fallback if only email is in session
    if (isset($_SESSION['email_address'])) {
        $user = GetUserByEmail($_SESSION['email_address']);
        $_SESSION['user_id'] = $user['user_id'];
    } else {
        header("Location: LoginMiniP.php");
        exit();
    }
}

$user_id = $_SESSION['user_id'];
$user = GetUserByID($user_id); // get current user details

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $mobile_number = $_POST['mobile_number'];
    $email_address = $_POST['email_address'];
    $gender = $_POST['gender'];
    $race = $_POST['race'];
    $profession = $_POST['profession'];
    $location = $_POST['location'];
    UpdateByID($user_id, $full_name, $mobile_number, $email_address, $gender, $race, $profession, $location);
    header("Location: ProfileMiniP.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                        lightcard: '#f9f9f9',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
</head>
<body class="bg-white dark:bg-darkbg text-gray-900 dark:text-white font-sans min-h-screen flex flex-col">

<!-- Navbar -->
<nav class="sticky top-0 z-50 shadow-2xl border-b border-gray-900 
        bg-white/90 backdrop-blur-md dark:bg-darkbg/95 dark:border-primary-700/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <div class="text-2xl font-bold text-gray-900 dark:text-white tracking-wider">
                Theatre Zenith Atrium
            </div>

            <div class="flex items-center space-x-4">
                <button id="theme-toggle" title="Toggle Dark Mode" class="p-3 rounded-full bg-gray-200 dark:bg-cardbg text-gray-700 dark:text-gray-400 hover:text-primary-500 dark:hover:text-primary-500 transition-colors duration-300 shadow-md">
                    <i class="fas fa-moon dark:hidden text-xl"></i>
                    <i class="fas fa-sun hidden dark:block text-xl"></i>
                </button>

                <div class="flex items-center space-x-3 group relative">
                    <div class="hidden md:block text-right">
                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-xs">
                            <?php echo htmlspecialchars($_SESSION['email_address']); ?>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-primary-500 transition-colors">
                            <a href="LogoutMiniP.php" class="hover:underline">Sign Out</a>
                        </div>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold text-lg shadow-lg ring-2 ring-primary-500">
                        <?php echo strtoupper(substr($_SESSION['email_address'], 0, 1)); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl w-full bg-lightcard dark:bg-cardbg rounded-xl p-10 shadow-2xl border border-gray-200 dark:border-primary-700/40">
        <h2 class="text-3xl font-bold text-center mb-8 text-gray-900 dark:text-white">Edit Profile</h2>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-semibold mb-2">Full Name</label>
                <input type="text" name="full_name" value="<?php echo ($user['full_name']); ?>" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-darkbg px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Mobile Number</label>
                <input type="text" name="mobile_number" value="<?php echo ($user['mobile_number']); ?>" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-darkbg px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Email Address</label>
                <input type="email" name="email_address" value="<?php echo ($user['email_address']); ?>" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-darkbg px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Gender</label>
                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="radio" name="gender" value="Male" <?php if($user['gender']=='Male') echo 'checked'; ?> class="text-primary-500 focus:ring-primary-500">
                        <span class="ml-2">Male</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="gender" value="Female" <?php if($user['gender']=='Female') echo 'checked'; ?> class="text-primary-500 focus:ring-primary-500">
                        <span class="ml-2">Female</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="gender" value="Other" <?php if($user['gender']=='Other') echo 'checked'; ?> class="text-primary-500 focus:ring-primary-500">
                        <span class="ml-2">Other</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Race</label>
                <select name="race" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-darkbg px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                    <?php 
                    $races = ['Malay', 'Chinese', 'Indian', 'Other'];
                    foreach ($races as $r) {
                        $selected = ($user['race'] == $r) ? 'selected' : '';
                        echo "<option value='$r' $selected>$r</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Profession</label>
                <select name="profession" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-darkbg px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                    <?php 
                    $professions = ['Student', 'Corporate', 'Business Owner', 'Unemployed', 'Others'];
                    foreach ($professions as $p) {
                        $selected = ($user['profession'] == $p) ? 'selected' : '';
                        echo "<option value='$p' $selected>$p</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Location</label>
                <select name="location" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-darkbg px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                    <?php 
                    $locations = ['Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Penang', 'Perak', 'Perlis', 'Sabah', 'Sarawak', 'Selangor', 'Terengganu', 'Kuala Lumpur', 'Putrajaya', 'Labuan'];
                    foreach ($locations as $l) {
                        $selected = ($user['location'] == $l) ? 'selected' : '';
                        echo "<option value='$l' $selected>$l</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="flex justify-between items-center pt-6">
                <button type="submit"
                    class="bg-primary-500 hover:bg-primary-600 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition-all transform hover:scale-105">
                    Save Changes
                </button>
                <a href="ProfileMiniP.php"
                    class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-bold py-3 px-8 rounded-lg shadow-lg transition-all">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<!-- Footer -->
<footer class="bg-gray-100 dark:bg-cardbg border-t border-gray-200 dark:border-primary-700/50 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center md:text-left">
        <p class="text-gray-500 dark:text-gray-400 text-sm">© 2023 Theatre Zenith Atrium. All rights reserved.</p>
    </div>
</footer>

<script src="https://kit.fontawesome.com/a2d04b6c2d.js" crossorigin="anonymous"></script>
<script>
    const themeToggle = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;
    const isDarkMode = localStorage.getItem('color-theme') === 'dark' ||
        (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

    if (isDarkMode) htmlElement.classList.add('dark');
    else htmlElement.classList.remove('dark');

    themeToggle.addEventListener('click', function() {
        const isCurrentlyDark = htmlElement.classList.toggle('dark');
        localStorage.setItem('color-theme', isCurrentlyDark ? 'dark' : 'light');
    });
</script>

</body>
</html>