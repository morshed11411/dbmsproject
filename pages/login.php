<?php
include '../includes/connection.php'; // Assuming you've included the database connection here with a short name like $conn

if (isset($_SESSION['username'])) {
   header('Location: dashboard.php');
   exit();
}

function login($conn, $username, $password)
{


   $query = "SELECT s.SOLDIERID, s.NAME, l.ROLE, l.FAILED_LOGIN_ATTEMPTS, l.LAST_LOGIN_TIME, l.STATUS, l.PASSWORD
             FROM LOGIN l
             JOIN SOLDIER s ON l.SOLDIERID = s.SOLDIERID
             WHERE l.SOLDIERID = :username";

   $stmt = oci_parse($conn, $query);
   oci_bind_by_name($stmt, ':username', $username);

   if (oci_execute($stmt)) {
      $row = oci_fetch_assoc($stmt);
      if ($row) {
         if ($row['STATUS'] == 1) {
            $_SESSION['error'] = 'Your account is disabled. Please reset your password.';
         } else {
            if ($password == $row['PASSWORD']) { // Compare plain text passwords
               // Reset failed login attempts
               $query = "UPDATE LOGIN SET FAILED_LOGIN_ATTEMPTS = 0 WHERE SOLDIERID = :username";
               $stmt = oci_parse($conn, $query);
               oci_bind_by_name($stmt, ':username', $username);
               oci_execute($stmt);

               // Update last login time
               $query = "UPDATE LOGIN SET LAST_LOGIN_TIME = CURRENT_TIMESTAMP WHERE SOLDIERID = :username";
               $stmt = oci_parse($conn, $query);
               oci_bind_by_name($stmt, ':username', $username);
               oci_execute($stmt);

               $_SESSION['username'] = $row['NAME'];
               $_SESSION['userid'] = $row['SOLDIERID'];
               $_SESSION['role'] = $row['ROLE'];
               $_SESSION['success'] = 'Logged in successfully.';
               header('Location: dashboard.php');
               exit();
            } else {
               // Update failed login attempts
               $failedAttempts = $row['FAILED_LOGIN_ATTEMPTS'] + 1;
               $query = "UPDATE LOGIN SET FAILED_LOGIN_ATTEMPTS = :failedAttempts WHERE SOLDIERID = :username";
               $stmt = oci_parse($conn, $query);
               oci_bind_by_name($stmt, ':failedAttempts', $failedAttempts);
               oci_bind_by_name($stmt, ':username', $username);
               oci_execute($stmt);

               if ($failedAttempts >= 5) {
                  // Disable the user
                  $query = "UPDATE LOGIN SET STATUS = 1 WHERE SOLDIERID = :username";
                  $stmt = oci_parse($conn, $query);
                  oci_bind_by_name($stmt, ':username', $username);
                  oci_execute($stmt);
                  $_SESSION['error'] = 'Your account has been disabled. Please reset your password.';
               } else {
                  $_SESSION['error'] = 'Invalid username or password. You have ' . (5 - $failedAttempts) . ' attempts remaining.';
               }
            }
         }
      } else {
         $_SESSION['error'] = 'Invalid username.';
      }
   } else {
      $error = oci_error($stmt);
      $_SESSION['error'] = 'Fatal error please contact with the Administrator;';
      //$_SESSION['error'] = 'Database error: ' . $error['message'];
   }

   oci_free_statement($stmt);
   oci_close($conn);
   return $row['FAILED_LOGIN_ATTEMPTS'];
}


if (isset($_POST['login'])) {
   $username = $_POST['username'];
   $password = $_POST['password'];
   $loginAttempt=login($conn, $username, $password);
}
?>

<!DOCTYPE html>
<html>
<?php include '../includes/head.php'; ?>

<body class="hold-transition login-page"
   style="background-image: linear-gradient(to top, #1e3c72 0%, #1e3c72 1%, #2a5298 100%);">
   <div class="login-box">
      <div class="card card-outline card-primary">
         <div class="card-header text-center">
            <div class="logo-container">
               <img src="../assets/logo.png" alt="Logo" class="logo img-circle img-responsive">
            </div>
            <a href="#" class="h3"><b>UNIT PERSONNEL COORDINATION SYSTEM</b></a>
         </div>
         <div class="card-body">
            <?php include '../includes/alert.php'; ?>
            <?php if ($loginAttempt >= 4) { ?>
               <!-- Display the reset button only -->
               <div class="row mt-2">
                  <div class="col-12 text-center">
                     <a href="forgotpassword.php" class="btn btn-warning">Forgot Password?</a>
                  </div>
               </div>
            <?php } else { ?>
               <!-- Display the login form -->
               <form method="post">
                  <div class="input-group mb-3">
                     <input type="text" class="form-control" placeholder="Personal No" id="username" name="username" value="<?=$username?>">
                     <div class="input-group-append">
                        <div class="input-group-text">
                           <span class="fas fa-user-alt"></span>
                        </div>
                     </div>
                  </div>
                  <div class="input-group mb-3">
                     <input type="password" class="form-control" placeholder="Password" id="password" name="password">
                     <div class="input-group-append">
                        <span class="input-group-text">
                           <i class="fas fa-eye-slash" id="togglePassword"></i>
                        </span>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-12 text-center">
                        <input class="btn btn-primary btn-block w-100" type="submit" name="login" value="Log in">
                     </div>
                  </div>
               </form>
            <?php } ?>


         </div>
      </div>

   </div>

   <script>
      document.addEventListener("DOMContentLoaded", function () {
         const passwordInput = document.getElementById("password");
         const togglePasswordIcon = document.getElementById("togglePassword");

         togglePasswordIcon.addEventListener("click", function () {
            if (passwordInput.type === "password") {
               passwordInput.type = "text";
               togglePasswordIcon.classList.remove("fa-eye-slash");
               togglePasswordIcon.classList.add("fa-eye");
            } else {
               passwordInput.type = "password";
               togglePasswordIcon.classList.remove("fa-eye");
               togglePasswordIcon.classList.add("fa-eye-slash");
            }
         });
      });
   </script>


</body>


</html>