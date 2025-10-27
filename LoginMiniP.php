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
        body { 
            font-family: Arial, sans-serif; 
            background-color: rgb(241, 219, 219); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            color: white; 
        }
        .card { 
            background: Black; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); 
            width: 300px; 
            text-align: center; 
        }
        .card h2 { 
            margin-bottom: 20px; 
        }
        .card input { 
            width: 90%; 
            padding: 10px; 
            margin: 10px 0; 
            border: 1px solid #ccc; 
            border-radius: 8px; 
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
            margin-top: 15px; 
            display: block; 
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Login</h2>
        <form method="POST">
            <input type="email" name="email_address" placeholder="Enter email" required><br>
            <input type="password" name="password" placeholder="Enter password" required><br>
            <input type="submit" value="Login">
        </form>
        <a class="link" href="RegisterMiniP.php">Don't have an account? Register here</a>
    </div>
</body>
</html>