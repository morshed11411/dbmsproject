<?php
include 'connection.php';


// Check if the user is logged in
if (!isset($_SESSION['username'])) {
  header("Location: ../index.php");
  exit();
}

// Logout functionality
if (isset($_POST['logout'])) {
  // Clear all session data
  session_unset();
  session_destroy();
  session_start();

  $_SESSION['logout'] = 'Logged out successfully.';

  // Redirect to the login page
  header("Location: ../index.php");
  exit();
}

// Retrieve the username from the session
$username = $_SESSION['username'];
$userid = $_SESSION['userid'];

?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head.php';
include '../includes/notifications.php';
?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">

  <?php
    include 'navbar.php';

  ?>
  <div class="wrapper">

    <!-- Content Wrapper -->
    <div class="content-wrapper">
      <div class="content">
        <div class="container-fluid">