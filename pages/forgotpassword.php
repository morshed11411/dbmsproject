<?php
session_start();

include '../includes/connection.php'; // Include your database connection

function generateResetCode()
{
    // Generate a random 6-digit code
    $resetCode = rand(100000, 999999);

    // Ensure the code is exactly 6 digits long
    return str_pad($resetCode, 6, '0', STR_PAD_LEFT);
}

if (isset($_POST['request_reset'])) {
    $username = $_POST['username'];

    // Check if the user is disabled (you may need to adjust the table and column names)
    $query = "SELECT STATUS FROM LOGIN WHERE SOLDIERID = :username";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':username', $username);
    oci_execute($stmt);

    $row = oci_fetch_assoc($stmt);
    if ($row && $row['STATUS'] != 0) {
        // User is not disabled, proceed with the reset request
        $query = "SELECT req_id FROM pwd_reset_req WHERE username = :username AND is_approved = 0";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':username', $username);
        oci_execute($stmt);

        $existingRequest = oci_fetch_assoc($stmt);

        if (!$existingRequest) {
            $reset_code = generateResetCode();

            // Insert the reset request into the database
            $query = "INSERT INTO pwd_reset_req (username, reset_code) VALUES (:username, :reset_code)";
            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':username', $username);
            oci_bind_by_name($stmt, ':reset_code', $reset_code);

            if (oci_execute($stmt)) {
                // Reset request successfully recorded
                // You can send an email with the reset code to the user here

                // Redirect the user to the enter_reset_code.php page with the username as a query parameter
                $_SESSION['success'] = 'An unique 6 digit code is sent to your Admin';

                header('Location: enter_reset_code.php?username=' . urlencode($username));
                exit();
            } else {
                // Handle database error
                $_SESSION['error'] = 'Error requesting password reset. Please try again.';
            }
        } else {
            // request is already pending, display an error message
            $_SESSION['error'] = 'A reset request is already sent for you';
            header('Location: enter_reset_code.php?username=' . urlencode($username));
            exit();

        }
    } else {
        // User is disabled, display an error message
        $_SESSION['error'] = 'Your account is not disabled. Password can not be reset';
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

                    <h4>Forgot Password</h4>
                    <?php include '../includes/alert.php'; ?>

                    <form method="post">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="request_reset">Request Reset</button>
                    </form>


                </div>

            </div>
        </div>

    </div>


</body>

</html>