<?php
session_start();
require 'ConnMiniP.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: LoginMiniP.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Call your existing delete function
if (DeleteUserByID($user_id)) {
    // End session and redirect to login
    session_destroy();
    header("Location: LoginMiniP.php");
    exit();
} else {
    echo "Error deleting account. Please try again.";
}
?>
