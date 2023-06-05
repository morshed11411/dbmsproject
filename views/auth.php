<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
  // Redirect to the login page if not logged in
  header("Location: index.php");
  exit();
}

// Logout functionality
if (isset($_POST['logout'])) {
  // Clear all session data
  session_unset();
  session_destroy();

  // Redirect to the login page
  header("Location: index.php");
  exit();
}

// Retrieve the username from the session
$username = $_SESSION['username'];
?>
