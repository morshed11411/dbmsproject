<?php
session_start();
include '../includes/connection.php';

if (isset($_POST['submit'])) {
    // Form processing code
    $soldier_id = $_SESSION['userid'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the current password matches the stored password for the soldier
    $query = "SELECT PASSWORD FROM LOGIN WHERE SOLDIERID = :soldier_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldier_id);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    $stored_password = $row['PASSWORD'];

    if ($stored_password === $current_password) {
        // Current password is correct, proceed with changing the password
        if ($new_password === $confirm_password) {
            // New password and confirm password match
            $update_query = "UPDATE LOGIN SET PASSWORD = :new_password WHERE SOLDIERID = :soldier_id";
            $update_stmt = oci_parse($conn, $update_query);
            oci_bind_by_name($update_stmt, ':new_password', $new_password);
            oci_bind_by_name($update_stmt, ':soldier_id', $soldier_id);
            $result = oci_execute($update_stmt);
            oci_free_statement($update_stmt);
            oci_close($conn);
            if ($result !== false) {
                session_unset();
                session_destroy();
                session_start();

                $_SESSION['message'] = "Password changed successfully. Please login again.";

                header("location:../index.php");
                exit();
            } else {
                $error_message = "Failed to update password.";
            }
        } else {
            $error_message = "New password and confirm password do not match. Please try again.";
        }
    } else {
        $error_message = "Invalid current password. Please try again.";
    }
}

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Change Password</h3>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php
        if (isset($error_message)) {
            echo '<div class="alert alert-danger">' . $error_message . '</div>';
        }
        ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <!-- Change Password Form -->
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="current_password">Current Password:</label>
                                <input type="password" name="current_password" id="current_password"
                                    class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="new_password">New Password:</label>
                                <input type="password" name="new_password" id="new_password" class="form-control"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password:</label>
                                <input type="password" name="confirm_password" id="confirm_password"
                                    class="form-control" required>
                            </div>

                            <input type="submit" name="submit" value="Change Password" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

