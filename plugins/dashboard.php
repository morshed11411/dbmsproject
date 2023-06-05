<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Retrieve the username from the session
$username = $_SESSION['username'];

// Display the dashboard content
echo "Welcome to the Dashboard, " . $username . "!";

// Other dashboard content goes here
?>
