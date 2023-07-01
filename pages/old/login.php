<?php
include '../includes/head.php';
include '../includes/connection.php';
session_start();
if (isset($_SESSION['username'])) {
   header('Location: dashboard.php');
   exit();
}
if (isset($_POST['login'])) {
   $username = $_POST['username'];
   $password = $_POST['password'];
   $query = "SELECT s.SOLDIERID, s.NAME, l.ROLE
             FROM LOGIN l
             JOIN SOLDIER s ON l.SOLDIERID = s.SOLDIERID
             WHERE l.SOLDIERID = :username AND l.PASSWORD = :password";
   $stmt = oci_parse($conn, $query);
   oci_bind_by_name($stmt, ':username', $username);
   oci_bind_by_name($stmt, ':password', $password);

   if (oci_execute($stmt)) {
      // Check if a matching user is found
      if ($row = oci_fetch_assoc($stmt)) {
         // User is authenticated
         $_SESSION['userid'] = $row['SOLDIERID'];
         $_SESSION['username'] = $row['NAME']; // Store NAME from SOLDIER table in the session
         $_SESSION['role'] = $row['ROLE'];
         $_SESSION['success'] = 'Logged in successfully.';
         $_SESSION['error'] = '';
         header('Location: blankpage.php');
         exit();
      } else {
         $_SESSION['error'] = 'Invalid username or password.';
      }
   } else {
      $error = oci_error($stmt);
      $_SESSION['error'] = 'Database error: ' . $error['message'];
   }

   oci_free_statement($stmt);
   oci_close($conn);
}

?>

<!DOCTYPE html>
<html>
<?php include '../includes/head.php'; ?>

<body class="hold-transition login-page">
   <div class="login-box">
      <div class="card card-outline card-primary">
         <div class="card-header text-center">
            <a href="index.php" class="h3"><b>UNIT MANAGEMENT SYSTEM</b></a>
         </div>
         <div class="card-body">
            <?php
            if (isset($_SESSION['success'])) {
               echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
               unset($_SESSION['success']);
            }
            if (isset($_SESSION['error'])) {
               echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
               unset($_SESSION['error']);
            }
            ?>
            <form method="post">
               <div class="input-group mb-3">
                  <input type="text" class="form-control" placeholder="Personal No" id="username" name="username">
                  <div class="input-group-append">
                     <div class="input-group-text">
                        <span class="fas fa-user-alt"></span>
                     </div>
                  </div>
               </div>
               <div class="input-group mb-3">
                  <input type="password" class="form-control" placeholder="Password" id="password" name="password">
                  <div class="input-group-append">
                     <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-5">
                     <input class="btn btn-primary btn-block" type="submit" name="login" value="Log in">
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</body>

</html>