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

<html>
    <head>
        <title>Register</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background:rgb(241, 219, 219);
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                color: white;
                margin: 40px;
            }
            .card {
                background: Black;
                padding: 30px;
                border-radius: 20px;
                box-shadow: 0 4px 10px rgba(231, 58, 58, 0.98);
                width: 350px;
                margin: 40
            }
            .card h2 {
                text-align: center;
                margin-bottom: 20px;
            }
            .card input, .card select {
                width: 100%;
                padding: 10px;
                margin: 8px 0;
                border: 1px solid #ccc;
                border-radius: 8px;
            }
            .radio-group {
                margin: 10px 0;
            }
            .card input[type="submit"] {
                background: #28a745;
                color: white;
                border: none;
                cursor: pointer;
                transition: 0.3s;
            }
            .card input[type="submit"]:hover {
                background: #218838;
            }
            .link {
                display: block;
                text-align: center;
                margin-top: 15px;
            }
        </style>
    </head>
    <body>
    <div class="card">
        <h2>Register</h2>
        <form method="POST">
            <input type="text" name="full_name" placeholder="Enter full name"><br>
            <input type="tel" name="mobile_number" placeholder="Enter mobile number"><br>
            <input type="email" name="email_address" placeholder="Enter email"><br>
            <input type="password" name="password"  placeholder="Enter password"><br>
            <input type="date" name="date_of_birth"><br>
            Gender: 
                <input type="radio" name="gender" value="Male"> Male
                <input type="radio" name="gender" value="Female"> Female
                <br>
            Race:
            <select name="race">
                <option value="">Select Race</option>
                <option value="Malay">Malay</option>
                <option value="Chinese">Chinese</option>
                <option value="Indian">Indian</option>
                <option value="Other">Other</option>
            </select><br><br>
            Profession:
            <select name="profession">
                <option value="">Select Profession</option>
                <option value="Student">Student</option>
                <option value="Corporate">Corporate</option>
                <option value="Business Owner">Business Owner</option>
                <option value="Unemployed">Unemployed</option>
                <option value="Others">Others</option>
            </select><br><br>
            Location:
            <select name="location">
                <option value="">Select Location</option>
                <option value="Johor">Johor</option>
                <option value="Kedah">Kedah</option>
                <option value="Kelantan">Kelantan</option>
                <option value="Melaka">Melaka</option>
                <option value="Negeri Sembilan">Negeri Sembilan</option>
                <option value="Pahang">Pahang</option>
                <option value="Penang">Penang</option>
                <option value="Perak">Perak</option>
                <option value="Perlis">Perlis</option>
                <option value="Sabah">Sabah</option>
                <option value="Sarawak">Sarawak</option>
                <option value="Selangor">Selangor</option>
                <option value="Terengganu">Terengganu</option>
                <option value="Kuala Lumpur">Kuala Lumpur</option>
                <option value="Putrajaya">Putrajaya</option>
                <option value="Labuan">Labuan</option>
            </select><br><br>
            <input type="submit" value="Register">
        </form>
        <a class="link" href="LoginMiniP.php">Already have an account? Login here</a>
    </div>
    </body>
</html>