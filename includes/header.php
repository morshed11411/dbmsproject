<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

// Logout functionality
if (isset($_POST['logout'])) {
  // Clear all session data
  session_unset();
  session_destroy();
  session_start();

  $_SESSION['success'] = 'Logged out successfully.';

  // Redirect to the login page
  header("Location: ../index.php");
  exit();
}

// Retrieve the username from the session
$username = $_SESSION['username'];
$userid = $_SESSION['userid'];

  include 'connection.php'

?>

<!DOCTYPE html>
<html lang="en">

<?php include 'head.php'; ?>


<body>
  <div class="wrapper">
    <?php include 'navbar.php'; ?>
    <div class="content-wrapper">