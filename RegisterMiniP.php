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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@700&family=Saira+Semi+Condensed:wght@400;700&display=swap" rel="stylesheet">
  
  <style>
    body {
      background: url('https://i.pinimg.com/1200x/59/ec/a7/59eca7aafe53bb2b91466b48f57fa731.jpg') center/cover no-repeat;
      font-family: 'Saira Semi Condensed', 'Arial', sans-serif;
      color: #fff;
    }

    /* === Animation === */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(25px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* === Heading === */
    h2 {
      font-family: 'Manrope', sans-serif;
      font-size: 2.5em;
      font-weight: 700;
      color: #fff;
      text-shadow: 0 0 10px rgba(255,255,255,0.4), 0 0 30px rgba(255,0,0,0.2);
      transition: transform 0.4s, text-shadow 0.4s;
    }
    h2:hover { transform: scale(1.03); text-shadow: 0 0 20px rgba(255,255,255,0.8), 0 0 40px rgba(255,0,0,0.5); }

    /* === Input === */
    .input-field, .select-box {
      width: 100%;
      padding: 15px;
      border: none;
      border-radius: 8px;
      background: rgba(19,19,19,0.62);
      color: #fff;
      font-size: 1em;
      box-shadow: 0 0 8px rgba(255,255,255,0.2);
      transition: background-color 0.3s, box-shadow 0.4s, transform 0.2s;
    }
    .input-field:focus, .select-box:hover {
      background: rgba(25,25,25,0.8);
      box-shadow: 0 0 15px rgba(255,255,255,0.7), 0 0 25px rgba(255,255,255,0.3);
      transform: scale(1.02);
      outline: none;
    }

    /* === Date Icon === */
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

    /* === Radio === */
    input[type="radio"] {
      appearance: none;
      border: 2px solid rgba(255,255,255,0.6);
      border-radius: 50%;
      width: 18px;
      height: 18px;
      cursor: pointer;
      position: relative;
      transition: 0.25s;
    }
    input[type="radio"]:checked {
      border-color: #fff;
      box-shadow: 0 0 8px rgba(255,255,255,0.8);
    }
    input[type="radio"]:checked::before {
      content: '';
      position: absolute;
      top: 4px; left: 4px;
      width: 8px; height: 8px;
      background: #fff;
      border-radius: 50%;
      box-shadow: 0 0 6px rgba(255,255,255,0.8);
    }

    /* === Custom Dropdown === */
    .custom-select { position: relative; width: 100%; }
    .select-box { display: flex; justify-content: space-between; align-items: center; cursor: pointer; }
    .select-arrow { width: 18px; height: 18px; fill: #ccc; transition: 0.3s; }
    .select-box:hover .select-arrow { fill: #fff; transform: rotate(180deg); }

    .options {
      position: absolute;
      width: 100%;
      margin-top: 5px;
      padding: 0;
      list-style: none;
      background: rgba(19,19,19,0.95);
      border-radius: 8px;
      box-shadow: 0 0 20px rgba(255,255,255,0.2);
      display: none;
      z-index: 10;
      max-height: 220px;
      overflow-y: auto;
    }
    .options li {
      padding: 12px 15px;
      cursor: pointer;
      transition: background 0.2s, transform 0.2s;
    }
    .options li:hover {
      background: rgba(255,255,255,0.15);
      transform: scale(1.01);
    }

    /* === Link === */
    .register-link a {
      color: #fff;
      text-decoration: none;
      position: relative;
      transition: color 0.3s, text-shadow 0.3s, transform 0.2s;
    }
    .register-link a:hover {
      color: #a40000;
      text-shadow: 0 0 8px rgba(185,2,2,0.97);
      transform: scale(1.05);
    }
    .register-link a::after {
      content: '';
      position: absolute;
      left: 0; bottom: -2px;
      width: 0; height: 2px;
      background: #ff0000e8;
      transition: width 0.3s;
    }
    .register-link a:hover::after { width: 100%; }
  </style>
</head>

<body class="flex justify-center items-center min-h-screen">
  <div class="bg-[rgba(15,15,15,0.88)] backdrop-blur-md rounded-2xl shadow-[0_10px_75px_rgba(0,0,0,1)] w-[950px] max-w-[95%] px-16 py-14 animate-[fadeUp_0.8s_ease_forwards]">
    <h2 class="text-center mb-10">Register</h2>

    <form class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-6 w-full">
      <!-- Left -->
      <div class="space-y-3">
        <input type="text" class="input-field" placeholder="Full Name" required>
        <input type="tel" class="input-field" placeholder="Mobile Number" required>
        <input type="email" class="input-field" placeholder="Email Address" required>
        <input type="password" class="input-field" placeholder="Password" required>
        <input type="date" class="input-field" required>
      </div>

      <!-- Right -->
      <div class="space-y-3">
        <div class="flex items-center space-x-6">
          <label class="flex items-center space-x-2"><input type="radio" name="gender" required><span>Male</span></label>
          <label class="flex items-center space-x-2"><input type="radio" name="gender"><span>Female</span></label>
        </div>

        <!-- Dropdowns -->
        <div class="custom-select">
          <div class="select-box" onclick="toggleDropdown(this)">
            <span>Select Race</span>
            <svg class="select-arrow" viewBox="0 0 20 20"><path d="M5 7l5 5 5-5H5z"/></svg>
          </div>
          <ul class="options">
            <li>Malay</li><li>Chinese</li><li>Indian</li><li>Other</li>
          </ul>
        </div>

        <div class="custom-select">
          <div class="select-box" onclick="toggleDropdown(this)">
            <span>Select Profession</span>
            <svg class="select-arrow" viewBox="0 0 20 20"><path d="M5 7l5 5 5-5H5z"/></svg>
          </div>
          <ul class="options">
            <li>Student</li><li>Corporate</li><li>Business Owner</li><li>Unemployed</li><li>Others</li>
          </ul>
        </div>

        <div class="custom-select">
          <div class="select-box" onclick="toggleDropdown(this)">
            <span>Select Location</span>
            <svg class="select-arrow" viewBox="0 0 20 20"><path d="M5 7l5 5 5-5H5z"/></svg>
          </div>
          <ul class="options">
            <li>Johor</li><li>Kedah</li><li>Kelantan</li><li>Melaka</li><li>Negeri Sembilan</li><li>Pahang</li><li>Penang</li><li>Perak</li><li>Perlis</li><li>Sabah</li><li>Sarawak</li><li>Selangor</li><li>Terengganu</li><li>Kuala Lumpur</li><li>Putrajaya</li><li>Labuan</li>
          </ul>
        </div>
      </div>

      <input type="submit" value="Register" class="col-span-2 bg-white text-black font-bold text-lg py-4 rounded-lg mt-5 transition-all duration-300 hover:scale-105 hover:bg-gray-100 hover:shadow-[0_0_15px_rgba(255,255,255,0.4)] active:scale-95">
    </form>

    <p class="register-link mt-8 text-sm text-[rgba(255,255,255,0.7)] text-center">
      Already have an account? <a href="LoginMiniP.php">Login here</a>
    </p>
  </div>

  <script>
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
        const box = e.target.closest('.custom-select').querySelector('.select-box span');
        box.textContent = e.target.textContent;
        e.target.parentElement.style.display = 'none';
      });
    });
  </script>
</body>
</html>
