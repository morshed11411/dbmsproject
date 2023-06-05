<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>UNIT MANAGEMENT SYSTEM</title>
      <!-- Google Font: Source Sans Pro -->
      <link rel="stylesheet"
         href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
      <!-- Font Awesome -->
      <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
      <!-- icheck bootstrap -->
      <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
      <!-- Theme style -->
      <link rel="stylesheet" href="dist/css/adminlte.min.css">
   </head>
   <body class="hold-transition login-page">
      <div class="login-box">
         <!-- /.login-logo -->
         <div class="card card-outline card-primary">
            <div class="card-header text-center">
               <a href="#" class="h3"><b>UNIT MANAGEMENT SYSTEM</b></a>
            </div>
            <div class="card-body">
               <?php
                  error_reporting(0);
                  ini_set('display_errors', 0);
                  // Start the session
                  session_start();
                  if (isset($_SESSION['username'])) {
                    // Redirect to the login page if not logged in
                    header("Location: dashboard.php");
                    exit();
                }
                
                  // Check if the login form is submitted
                  if (isset($_POST['login'])) {
                    // Get the form data
                    $username = $_POST['username'];
                    $password = $_POST['password'];
                  
                    // Establish a connection to the Oracle database
                    $conn = oci_connect('UMS', '12345', 'localhost/XE');
                    if (!$conn) {
                      $e = oci_error();
                      echo "Failed to connect to Oracle: " . $e['message'];
                    } else {
                      // Prepare the SQL statement
                      $query = "SELECT * FROM soldier WHERE soldierid = :username AND password = :password";
                      $stmt = oci_parse($conn, $query);
                  
                      // Bind the parameters
                      oci_bind_by_name($stmt, ':username', $username);
                      oci_bind_by_name($stmt, ':password', $password);
                  
                      // Execute the statement
                      oci_execute($stmt);
                  
                      // Check if a matching user is found
                      if ($row = oci_fetch_assoc($stmt)) {
                        // User is authenticated
                        $_SESSION['username'] = $row['NAME']; // Ensure the column name is correct
                        // You can store other relevant data as needed
                  
                        // Redirect to the dashboard or desired page
                        header("Location: dashboard.php");
                        exit();
                      } else {
                        // Invalid credentials
                        echo "<center>Invalid username or password.</center>";
                      }
                  
                      oci_free_statement($stmt);
                      oci_close($conn);
                    }
                  }
                  ?>
               <form action="index.php" method="post">
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
                     <!-- /.col -->
                     <div class="col-5">
                        <input class="btn btn-primary btn-block" type="submit" name="login" value="Log in">
                     </div>
                     <!-- /.col -->
                  </div>
               </form>
            </div>
            <!-- /.card-body -->
         </div>
         <!-- /.card -->
      </div>
      <!-- /.login-box -->
      <!-- jQuery -->
      <script src="plugins/jquery/jquery.min.js"></script>
      <!-- Bootstrap 4 -->
      <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
      <!-- AdminLTE App -->
      <script src="dist/js/adminlte.min.js"></script>
   </body>
</html>