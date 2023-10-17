<?php
session_start();

include '../includes/connection.php'; // Include your database connection

if (isset($_POST['reset_password'])) {
    $username = $_GET['username']; // Get the username from the URL query parameter
    $reset_code = $_POST['reset_code'];
    $new_password = $_POST['new_password'];

    // Check if the reset code is valid for the given username
    $query = "SELECT * FROM pwd_reset_req WHERE username = :username AND reset_code = :reset_code AND is_approved = 1";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':username', $username);
    oci_bind_by_name($stmt, ':reset_code', $reset_code);
    oci_execute($stmt);

    if ($row = oci_fetch_assoc($stmt)) {
        // Reset code is valid, update the user's password
        //$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $hashed_password = $new_password;

        // Update the user's password in the appropriate table (replace 'users' with your actual table name)
        $query = "UPDATE login SET password = :hashed_password,   FAILED_LOGIN_ATTEMPTS=0, LAST_LOGIN_TIME =null, STATUS=0 WHERE SOLDIERID = :username";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':hashed_password', $hashed_password);
        oci_bind_by_name($stmt, ':username', $username);

        if (oci_execute($stmt)) {
            // Password successfully reset
            // You may want to delete the used reset code from the database here
            // Redirect the user to the login page
            $query = "DELETE FROM pwd_reset_req WHERE username = :username";
            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':username', $username);
            oci_execute($stmt);

            $_SESSION['success'] = 'Resetting password Successful. Please login now.';

            header('Location: login.php');
            exit();
        } else {
            // Handle database error
            $_SESSION['error'] = 'Error resetting password. Please try again.';
        }
    } else {
        // Invalid reset code
        $_SESSION['error'] = 'Invalid reset code or reset code has not been approved.';
    }
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
                <div class="container">
                    <h4>Reset Password</h4>
                    <?php include '../includes/alert.php'; ?>

                    <form method="post">
                        <div class="form-group">
                            <label for="reset_code">Reset Code:</label>
                            <input type="text" class="form-control" id="reset_code" name="reset_code" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password:</label>
                            <input type="text" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="reset_password">Reset Password</button>
                    </form>


                </div>

            </div>
        </div>

    </div>


</body>

</html>