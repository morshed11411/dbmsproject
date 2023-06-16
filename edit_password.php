<?php include 'views/auth.php'; ?>

<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Change Password</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <!-- Change Password Form -->
                                    <form method="post" action="">
                                        <div class="form-group">
                                            
                                            <input type="hidden" name="soldier_id" id="soldier_id" class="form-control"
                                                required>
                                        </div>

                                        <div class="form-group">
                                            <label for="current_password">Current Password:</label>
                                            <input type="password" name="current_password" id="current_password"
                                                class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="new_password">New Password:</label>
                                            <input type="password" name="new_password" id="new_password"
                                                class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="confirm_password">Confirm New Password:</label>
                                            <input type="password" name="confirm_password" id="confirm_password"
                                                class="form-control" required>
                                        </div>

                                        <input type="submit" name="submit" value="Change Password"
                                            class="btn btn-primary">
                                    </form>
                                    <?php
                                    if (isset($_POST['submit'])) {
                                        // Form processing code
                                        $soldier_id = $_POST['soldier_id'];
                                        $current_password = $_POST['current_password'];
                                        $new_password = $_POST['new_password'];
                                        $confirm_password = $_POST['confirm_password'];

                                        include 'conn.php'; // Include the conn.php file for database connection

                                        // Check if the current password matches the stored password for the soldier
                                        $query = "SELECT Password FROM Soldier WHERE SoldierID = :soldier_id";
                                        $stmt = oci_parse($conn, $query);
                                        oci_bind_by_name($stmt, ':soldier_id', $soldier_id);
                                        oci_execute($stmt);
                                        $row = oci_fetch_assoc($stmt);
                                        $stored_password = $row['PASSWORD'];

                                        if ($stored_password === $current_password) {
                                            // Current password is correct, proceed with changing the password
                                            if ($new_password === $confirm_password) {
                                                // New password and confirm password match
                                                $update_query = "UPDATE Soldier SET Password = :new_password WHERE SoldierID = :soldier_id";
                                                $update_stmt = oci_parse($conn, $update_query);
                                                oci_bind_by_name($update_stmt, ':new_password', $new_password);
                                                oci_bind_by_name($update_stmt, ':soldier_id', $soldier_id);
                                                oci_execute($update_stmt);

                                                oci_free_statement($update_stmt);
                                                oci_close($conn);

                                                echo "Password changed successfully.";
                                            } else {
                                                echo "New password and confirm password do not match. Please try again.";
                                            }
                                        } else {
                                            echo "Invalid current password. Please try again.";
                                        }

                                        oci_free_statement($stmt);
                                        oci_close($conn);
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include 'views/footer.php'; ?>
    </div>
</body>

</html>
