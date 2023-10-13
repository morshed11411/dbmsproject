<?php
session_start();
include '../includes/connection.php'; // Include the connection.php file to establish a database connection
include '../includes/header.php';

?>


<!-- Rest of the code -->

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Access Management</h3>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addCompanyModal">Add Company</button>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <!-- User Access Form -->
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="soldier_id">Soldier ID:</label>
                                <input type="text" name="soldier_id" id="soldier_id" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirm Password:</label>
                                <input type="password" name="confirm_password" id="confirm_password"
                                    class="form-control" required>
                            </div>

                            <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                        </form>
                        <?php
                        if (isset($_POST['submit'])) {
                            // Form processing code
                            $soldier_id = $_POST['soldier_id'];
                            $password = $_POST['password'];
                            $confirm_password = $_POST['confirm_password'];
                            $access_role = "admin"; // Set the AccessRole to "admin"
                        
                            // Validate password and confirm password
                            if ($password === $confirm_password) {
                                // Passwords match, continue with the processing
                                include 'conn.php'; // Include the conn.php file for database connection
                        
                                // Perform the necessary database operations
                                $query = "UPDATE Soldier SET AccessRole = :access_role WHERE SoldierID = :soldier_id";
                                $stmt = oci_parse($conn, $query);
                                oci_bind_by_name($stmt, ':access_role', $access_role);
                                oci_bind_by_name($stmt, ':soldier_id', $soldier_id);
                                oci_execute($stmt);

                                // Update the password in the Soldier table
                                $query = "UPDATE Soldier SET Password = :password WHERE SoldierID = :soldier_id";
                                $stmt = oci_parse($conn, $query);
                                oci_bind_by_name($stmt, ':password', $password);
                                oci_bind_by_name($stmt, ':soldier_id', $soldier_id);
                                oci_execute($stmt);

                                oci_free_statement($stmt);
                                oci_close($conn);

                                echo "User access added successfully.";
                            } else {
                                echo "Password and confirm password do not match. Please try again.";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2>User Access List</h2>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Soldier ID</th>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include 'conn.php'; // Include the conn.php file for database connection
                                
                                $query = "SELECT SoldierID, Name, CompanyID FROM Soldier WHERE AccessRole='admin'";
                                $stmt = oci_parse($conn, $query);
                                oci_execute($stmt);

                                while ($row = oci_fetch_assoc($stmt)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['SOLDIERID'] . "</td>";
                                    echo "<td>" . $row['NAME'] . "</td>";
                                    echo "<td>" . $row['COMPANYID'] . "</td>";
                                    echo "<td><a href='manage_access.php?soldier_id=" . $row['SOLDIERID'] . "'>Remove Access</a></td>";
                                    echo "</tr>";
                                }

                                oci_free_statement($stmt);
                                oci_close($conn);
                                ?>
                            </tbody>
                            <?php
                            // remove_access.php
                            
                            include 'conn.php'; // Include the conn.php file for database connection
                            
                            if (isset($_GET['soldier_id'])) {
                                $soldier_id = $_GET['soldier_id'];

                                // Perform the necessary database operations to remove user access
                                // For example, you can execute an UPDATE query on the Soldier table to remove the access role
                            
                                $query = "UPDATE Soldier SET AccessRole = NULL, PASSWORD = NULL   WHERE SoldierID = :soldier_id";
                                $stmt = oci_parse($conn, $query);
                                oci_bind_by_name($stmt, ':soldier_id', $soldier_id);
                                oci_execute($stmt);

                                oci_free_statement($stmt);
                                oci_close($conn);


                                header("Location: manage_access.php");
                            }
                            ?>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>